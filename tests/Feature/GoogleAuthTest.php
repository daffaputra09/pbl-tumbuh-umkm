<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\Business;
use App\Models\SocialAccount;
use App\Models\Umkm;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request as PsrRequest;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    /** @var list<array{level: string, message: string, context: array<string, mixed>}> */
    private array $capturedLogs = [];

    public function test_login_page_shows_google_and_a_password_toggle(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Lanjutkan dengan Google');
        $response->assertSee('form.dataset.submitting', false);
        $response->assertSee('#4285F4', false);
        $response->assertSee('Tampilkan kata sandi');
        $response->assertSee('Dari data, menjadi');
    }

    public function test_register_page_shows_google_and_a_password_toggle(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Daftar dengan Google');
        $response->assertSee('#4285F4', false);
        $response->assertSee('Tampilkan kata sandi');
        $response->assertSee('Nama usaha dicatat sekarang');
        $response->assertSee('Nama usaha');
        $response->assertSee('Dari data, menjadi');
    }

    public function test_google_redirect_starts_the_provider_flow(): void
    {
        $this->fakeGoogleRedirect();

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_google_callback_creates_a_business_owner_and_logs_in(): void
    {
        $this->fakeGoogleUser();

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('umkm.profil'));

        $user = User::query()->where('email', 'siti@example.com')->first();

        $this->assertNotNull($user);
        $this->assertSame(User::ROLE_BUSINESS_OWNER, $user->role);
        $this->assertNull($user->password);
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_user_id' => 'google-1',
        ]);
    }

    public function test_google_callback_keeps_an_existing_role_when_the_email_matches(): void
    {
        $officer = User::factory()->create([
            'email' => 'siti@example.com',
            'role' => User::ROLE_OFFICER,
        ]);

        $this->fakeGoogleUser();

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($officer);
        $this->assertSame(User::ROLE_OFFICER, $officer->refresh()->role);
        $this->assertSame(1, SocialAccount::query()->count());
    }

    public function test_google_callback_does_not_create_a_business_before_the_profile_form(): void
    {
        $this->fakeGoogleUser();

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('umkm.profil'));

        $this->assertSame(0, Business::query()->count());
        $this->assertSame(0, Umkm::query()->count());
    }

    public function test_google_callback_opens_the_dashboard_when_the_business_profile_exists(): void
    {
        $owner = User::factory()->create([
            'email' => 'siti@example.com',
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);

        Business::factory()->create([
            'user_id' => $owner->id,
            'created_by' => $owner->id,
        ]);

        $this->fakeGoogleUser();

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('umkm.dashboard'));
    }

    public function test_google_callback_rejects_an_inactive_account(): void
    {
        User::factory()->create([
            'email' => 'siti@example.com',
            'is_active' => false,
        ]);

        $this->fakeGoogleUser();

        $this->listenForLogs();

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'login' => EnsureUserIsActive::MESSAGE,
            ]);

        $this->assertGuest();
        $this->assertSame('inactive_account', $this->findLog('Google sign-in rejected.')['context']['reason']);
    }

    public function test_unverified_google_email_does_not_link_to_an_existing_user(): void
    {
        User::factory()->create([
            'email' => 'siti@example.com',
            'role' => User::ROLE_OFFICER,
        ]);

        $this->fakeGoogleUser(verified: false);
        $this->listenForLogs();

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertSame(1, User::query()->count());
        $this->assertSame(0, SocialAccount::query()->count());
        $this->assertSame(
            'unverified_email_matches_existing_account',
            $this->findLog('Google sign-in rejected.')['context']['reason'],
        );
    }

    public function test_google_callback_logs_the_provider_error_without_the_client_secret(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'super-secret-value',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
            'app.url' => 'http://localhost',
        ]);

        $exception = new ClientException(
            'Google rejected super-secret-value',
            new PsrRequest('POST', 'https://www.googleapis.com/oauth2/v4/token'),
            new Response(400, [], json_encode([
                'error' => 'redirect_uri_mismatch',
                'error_description' => 'Bad Request',
            ], JSON_THROW_ON_ERROR)),
        );

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andThrow($exception);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
        $this->listenForLogs();

        $this->withSession(['state' => 'expected-state'])
            ->get(route('auth.google.callback', [
                'error' => 'access_denied',
                'error_description' => 'user cancelled',
            ]))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);

        $this->assertGuest();

        $failure = $this->findLog('Google sign-in failed.');
        $context = $failure['context'];

        $this->assertSame('warning', $failure['level']);
        $this->assertSame('stateful_callback', $context['stage']);
        $this->assertSame(ClientException::class, $context['exception']);
        $this->assertSame('Google rejected [redacted]', $context['message']);
        $this->assertSame(400, $context['http_status']);
        $this->assertSame('redirect_uri_mismatch', $context['oauth_error']);
        $this->assertSame('Bad Request', $context['oauth_error_description']);
        $this->assertTrue($context['session_had_state']);
        $this->assertFalse($context['has_authorization_code']);
        $this->assertSame('access_denied', $context['google_error']);
        $this->assertSame('user cancelled', $context['google_error_description']);
        $this->assertSame('http://localhost/auth/google/callback', $context['redirect_uri']);
        $this->assertTrue($context['client_secret_configured']);
        $this->assertStringNotContainsString('super-secret-value', (string) json_encode($context));
    }

    public function test_google_callback_logs_a_lost_oauth_state_and_the_stateless_failure(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
            'app.url' => 'http://localhost',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andThrow(new InvalidStateException);
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andThrow(new RuntimeException('stateless token exchange failed'));
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
        $this->listenForLogs();

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);

        $rejected = $this->findLog('Google sign-in rejected.');
        $failed = $this->findLog('Google sign-in failed.');

        $this->assertSame('stateful_callback', $rejected['context']['stage']);
        $this->assertSame('oauth_state_missing_or_mismatch', $rejected['context']['reason']);
        $this->assertFalse($rejected['context']['session_had_state']);
        $this->assertFalse($rejected['context']['has_state']);
        $this->assertSame('stateless_callback', $failed['context']['stage']);
        $this->assertSame(RuntimeException::class, $failed['context']['exception']);
        $this->assertSame('stateless token exchange failed', $failed['context']['message']);
    }

    public function test_google_redirect_logs_when_configuration_is_incomplete(): void
    {
        config([
            'services.google.client_id' => '',
            'services.google.client_secret' => '',
            'services.google.redirect' => '',
        ]);

        $this->fakeGoogleRedirect();
        $this->listenForLogs();

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');

        $rejected = $this->findLog('Google sign-in rejected.');

        $this->assertSame('redirect', $rejected['context']['stage']);
        $this->assertSame('incomplete_google_configuration', $rejected['context']['reason']);
        $this->assertFalse($rejected['context']['client_id_configured']);
        $this->assertFalse($rejected['context']['client_secret_configured']);
        $this->assertNull($rejected['context']['redirect_uri']);
    }

    public function test_google_redirect_logs_when_the_callback_host_differs_from_the_browser(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'secret',
            'services.google.redirect' => 'http://127.0.0.1:8000/auth/google/callback',
            'app.url' => 'http://127.0.0.1:8000',
        ]);

        $this->fakeGoogleRedirect();
        $this->listenForLogs();

        $this->get(route('auth.google.redirect'))
            ->assertRedirect('https://accounts.google.com/o/oauth2/auth');

        $rejected = $this->findLog('Google sign-in rejected.');

        $this->assertSame('redirect', $rejected['context']['stage']);
        $this->assertSame('redirect_target_does_not_match_request', $rejected['context']['reason']);
        $this->assertFalse($rejected['context']['redirect_host_matches_request']);
        $this->assertSame('http://127.0.0.1:8000/auth/google/callback', $rejected['context']['redirect_uri']);
    }

    public function test_google_redirect_logs_the_exception_and_returns_to_login(): void
    {
        config([
            'services.google.client_id' => 'client-id',
            'services.google.client_secret' => 'secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
            'app.url' => 'http://localhost',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')->once()->andThrow(new RuntimeException('missing google driver'));
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
        $this->listenForLogs();

        $this->get(route('auth.google.redirect'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);

        $failed = $this->findLog('Google sign-in failed.');

        $this->assertSame('redirect', $failed['context']['stage']);
        $this->assertSame(RuntimeException::class, $failed['context']['exception']);
        $this->assertSame('missing google driver', $failed['context']['message']);
    }

    public function test_google_callback_registers_a_new_owner_when_the_session_state_is_lost(): void
    {
        $googleUser = (new GoogleUser)->setRaw([
            'email_verified' => true,
        ])->map([
            'id' => 'google-1',
            'nickname' => null,
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'avatar' => 'https://example.com/avatar.png',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andThrow(new InvalidStateException);
        $provider->shouldReceive('stateless')->once()->andReturnSelf();
        $provider->shouldReceive('user')->once()->andReturn($googleUser);
        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('umkm.profil'));

        $this->assertDatabaseHas('users', [
            'email' => 'siti@example.com',
            'role' => User::ROLE_BUSINESS_OWNER,
        ]);
    }

    private function listenForLogs(): void
    {
        $this->capturedLogs = [];

        Log::listen(function (MessageLogged $event): void {
            $this->capturedLogs[] = [
                'level' => $event->level,
                'message' => $event->message,
                'context' => $event->context,
            ];
        });
    }

    /**
     * @return array{level: string, message: string, context: array<string, mixed>}
     */
    private function findLog(string $message): array
    {
        foreach ($this->capturedLogs as $entry) {
            if ($entry['message'] === $message) {
                return $entry;
            }
        }

        $this->fail('Missing log message ['.$message.'].');
    }

    private function fakeGoogleRedirect(): void
    {
        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
    }

    private function fakeGoogleUser(bool $verified = true): void
    {
        $googleUser = (new GoogleUser)->setRaw([
            'email_verified' => $verified,
        ])->map([
            'id' => 'google-1',
            'nickname' => null,
            'name' => 'Siti Aminah',
            'email' => 'siti@example.com',
            'avatar' => 'https://example.com/avatar.png',
        ]);

        $provider = Mockery::mock(Provider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->once()->with('google')->andReturn($provider);
    }
}

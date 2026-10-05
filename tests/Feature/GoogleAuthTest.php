<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\Business;
use App\Models\SocialAccount;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

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
        $response->assertSee('Data usaha dilengkapi');
        $response->assertDontSee('Nama usaha');
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

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'login' => EnsureUserIsActive::MESSAGE,
            ]);

        $this->assertGuest();
    }

    public function test_unverified_google_email_does_not_link_to_an_existing_user(): void
    {
        User::factory()->create([
            'email' => 'siti@example.com',
            'role' => User::ROLE_OFFICER,
        ]);

        $this->fakeGoogleUser(verified: false);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertSame(1, User::query()->count());
        $this->assertSame(0, SocialAccount::query()->count());
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

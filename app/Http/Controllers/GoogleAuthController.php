<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\SocialAccount;
use App\Models\User;
use GuzzleHttp\Exception\ResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class GoogleAuthController extends Controller
{
    /** @var array<string, mixed> */
    private array $googleContext = [];

    private bool $googleFailureLogged = false;

    public function redirect(Request $request): RedirectResponse
    {
        $this->googleFailureLogged = false;
        $this->googleContext = $this->googleRequestContext($request);

        if ($this->googleConfigurationIsIncomplete() || ! $this->redirectTargetMatchesRequest()) {
            $this->logGoogleRejection('redirect', $this->redirectRejectionReason());
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (Throwable $exception) {
            $this->logGoogleFailure('redirect', $exception);

            return redirect()->route('login')->withErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $this->googleFailureLogged = false;
        $this->googleContext = $this->googleRequestContext($request);

        try {
            $googleUser = $this->googleUser();
        } catch (Throwable $exception) {
            if (! $this->googleFailureLogged) {
                $this->logGoogleFailure('callback', $exception);
            }

            return redirect()->route('login')->withErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);
        }

        if (! $googleUser instanceof GoogleUser) {
            $this->logGoogleRejection('callback', 'provider_returned_an_unexpected_user');

            return redirect()->route('login')->withErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);
        }

        $user = $this->resolveUser($googleUser);

        if (! $user instanceof User) {
            return redirect()->route('login')->withErrors([
                'login' => 'Akun Google ini tidak bisa dipakai. Gunakan email yang sudah terverifikasi Google.',
            ]);
        }

        if (! $user->is_active) {
            $this->logGoogleRejection('callback', 'inactive_account');

            return redirect()->route('login')->withErrors([
                'login' => EnsureUserIsActive::MESSAGE,
            ]);
        }

        try {
            $this->rememberSocialAccount($googleUser, $user);
        } catch (Throwable $exception) {
            $this->logGoogleFailure('callback', $exception);

            return redirect()->route('login')->withErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRouteName());
    }

    private function googleUser(): GoogleUser
    {
        $driver = Socialite::driver('google');

        try {
            $googleUser = $driver->user();
        } catch (InvalidStateException $exception) {
            $this->logGoogleRejection('stateful_callback', 'oauth_state_missing_or_mismatch');

            try {
                $googleUser = $driver->stateless()->user();
            } catch (Throwable $statelessException) {
                $this->logGoogleFailure('stateless_callback', $statelessException);

                throw $statelessException;
            }
        } catch (Throwable $exception) {
            $this->logGoogleFailure('stateful_callback', $exception);

            throw $exception;
        }

        if (! $googleUser instanceof GoogleUser) {
            throw new InvalidStateException;
        }

        return $googleUser;
    }

    private function resolveUser(GoogleUser $googleUser): ?User
    {
        $emailVerified = $this->emailIsVerified($googleUser);
        $email = $googleUser->getEmail();

        $account = SocialAccount::query()
            ->where('provider', 'google')
            ->where('provider_user_id', (string) $googleUser->getId())
            ->first();

        $user = $account?->user;

        if (! $user instanceof User && is_string($email) && $email !== '') {
            $existing = User::query()->where('email', $email)->first();

            if ($existing instanceof User && $emailVerified) {
                $user = $existing;
            }

            if ($existing instanceof User && ! $emailVerified) {
                $this->logGoogleRejection('callback', 'unverified_email_matches_existing_account');

                return null;
            }
        }

        if ($user instanceof User) {
            return $user;
        }

        if (! is_string($email) || $email === '') {
            $this->logGoogleRejection('callback', 'google_account_has_no_email');

            return null;
        }

        $name = trim((string) $googleUser->getName());

        $user = new User([
            'name' => $name !== '' ? mb_substr($name, 0, 255) : 'Pemilik UMKM',
            'email' => $email,
            'role' => User::ROLE_BUSINESS_OWNER,
            'is_active' => true,
        ]);
        $user->password = null;
        $user->email_verified_at = $emailVerified ? now() : null;
        $user->save();

        return $user;
    }

    private function rememberSocialAccount(GoogleUser $googleUser, User $user): void
    {
        $avatar = $googleUser->getAvatar();

        SocialAccount::query()->updateOrCreate(
            [
                'provider' => 'google',
                'provider_user_id' => (string) $googleUser->getId(),
            ],
            [
                'user_id' => $user->id,
                'email' => $googleUser->getEmail(),
                'avatar_url' => is_string($avatar) && strlen($avatar) <= 255 ? $avatar : null,
            ],
        );
    }

    private function emailIsVerified(GoogleUser $googleUser): bool
    {
        $raw = $googleUser->user['email_verified'] ?? $googleUser->user['verified_email'] ?? false;

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @return array<string, mixed>
     */
    private function googleRequestContext(Request $request): array
    {
        $redirect = $this->configuredRedirectUri();
        $appUrl = config('app.url');

        return [
            'has_authorization_code' => $request->filled('code'),
            'has_state' => $request->filled('state'),
            'session_had_state' => $request->hasSession() && $request->session()->has('state'),
            'google_error' => $this->queryValue($request, 'error'),
            'google_error_description' => $this->queryValue($request, 'error_description'),
            'request_root' => $request->getSchemeAndHttpHost(),
            'app_url' => is_string($appUrl) && $appUrl !== '' ? $appUrl : null,
            'redirect_uri' => $redirect,
            'redirect_host_matches_request' => $this->redirectPartMatchesRequest($redirect, PHP_URL_HOST, $request->getHost()),
            'redirect_scheme_matches_request' => $this->redirectPartMatchesRequest($redirect, PHP_URL_SCHEME, $request->getScheme()),
            'client_id_configured' => filled(config('services.google.client_id')),
            'client_secret_configured' => filled(config('services.google.client_secret')),
            'session_driver' => config('session.driver'),
            'session_domain' => config('session.domain'),
            'session_secure' => config('session.secure'),
            'session_same_site' => config('session.same_site'),
        ];
    }

    private function googleConfigurationIsIncomplete(): bool
    {
        return ! ($this->googleContext['client_id_configured'] ?? false)
            || ! ($this->googleContext['client_secret_configured'] ?? false)
            || ($this->googleContext['redirect_uri'] ?? null) === null;
    }

    private function redirectTargetMatchesRequest(): bool
    {
        return ($this->googleContext['redirect_host_matches_request'] ?? false) === true
            && ($this->googleContext['redirect_scheme_matches_request'] ?? false) === true;
    }

    private function redirectRejectionReason(): string
    {
        if ($this->googleConfigurationIsIncomplete()) {
            return 'incomplete_google_configuration';
        }

        return 'redirect_target_does_not_match_request';
    }

    /**
     * @param  array<string, mixed>  $extra
     */
    private function logGoogleRejection(string $stage, string $reason, array $extra = []): void
    {
        Log::warning('Google sign-in rejected.', [
            'stage' => $stage,
            'reason' => $reason,
            ...$this->googleContext,
            ...$extra,
        ]);
    }

    private function logGoogleFailure(string $stage, Throwable $exception): void
    {
        $context = [
            'stage' => $stage,
            'exception' => $exception::class,
            'message' => $this->redact($exception->getMessage()),
            'exception_code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'previous' => $this->previousExceptionSummary($exception),
        ];

        try {
            $context = [
                ...$context,
                ...$this->providerErrorContext($exception),
                ...$this->googleContext,
            ];
        } catch (Throwable $contextException) {
            $context['context_error'] = $contextException::class.': '.$this->redact($contextException->getMessage());
        }

        $this->googleFailureLogged = true;

        Log::warning('Google sign-in failed.', $context);
    }

    private function previousExceptionSummary(Throwable $exception): ?string
    {
        $previous = $exception->getPrevious();

        if (! $previous instanceof Throwable) {
            return null;
        }

        return $previous::class.': '.$this->redact($previous->getMessage());
    }

    /**
     * @return array<string, mixed>
     */
    private function providerErrorContext(Throwable $exception): array
    {
        $current = $exception;

        while ($current instanceof Throwable) {
            if ($current instanceof ResponseException) {
                return $this->responseErrorContext($current->getResponse());
            }

            $current = $current->getPrevious();
        }

        return [];
    }

    /**
     * @return array<string, mixed>
     */
    private function responseErrorContext(ResponseInterface $response): array
    {
        $body = (string) $response->getBody();
        $decoded = json_decode($body, true);
        $context = [
            'http_status' => $response->getStatusCode(),
        ];

        if (! is_array($decoded)) {
            $context['response_excerpt'] = $this->redact(mb_substr($body, 0, 300));

            return $context;
        }

        $error = $decoded['error'] ?? null;
        $description = $decoded['error_description'] ?? null;
        $context['oauth_error'] = is_string($error) ? $this->redact($error) : null;
        $context['oauth_error_description'] = is_string($description) ? $this->redact($description) : null;

        return $context;
    }

    private function configuredRedirectUri(): ?string
    {
        $redirect = config('services.google.redirect');

        if (! is_string($redirect) || $redirect === '') {
            return null;
        }

        return $redirect;
    }

    private function redirectPartMatchesRequest(?string $redirect, int $part, string $requestValue): bool
    {
        if ($redirect === null) {
            return false;
        }

        $redirectValue = parse_url($redirect, $part);

        return is_string($redirectValue) && strcasecmp($redirectValue, $requestValue) === 0;
    }

    private function queryValue(Request $request, string $key): ?string
    {
        $value = $request->query($key);

        if (! is_string($value) || $value === '') {
            return null;
        }

        return $this->redact($value);
    }

    private function redact(string $value): string
    {
        $secret = config('services.google.client_secret');

        if (! is_string($secret) || $secret === '') {
            return $value;
        }

        return str_replace($secret, '[redacted]', $value);
    }
}

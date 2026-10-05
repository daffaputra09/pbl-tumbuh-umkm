<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as GoogleUser;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = $this->googleUser();
        } catch (Throwable $exception) {
            Log::warning('Google sign-in failed.', ['exception' => $exception]);

            return redirect()->route('login')->withErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);
        }

        if (! $googleUser instanceof GoogleUser) {
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
            return redirect()->route('login')->withErrors([
                'login' => EnsureUserIsActive::MESSAGE,
            ]);
        }

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

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->homeRouteName());
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
                return null;
            }
        }

        if ($user instanceof User) {
            return $user;
        }

        if (! is_string($email) || $email === '') {
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

    private function googleUser(): GoogleUser
    {
        $driver = Socialite::driver('google');

        try {
            $googleUser = $driver->user();
        } catch (InvalidStateException) {
            $googleUser = $driver->stateless()->user();
        }

        if (! $googleUser instanceof GoogleUser) {
            throw new InvalidStateException;
        }

        return $googleUser;
    }

    private function emailIsVerified(GoogleUser $googleUser): bool
    {
        $raw = $googleUser->user['email_verified'] ?? $googleUser->user['verified_email'] ?? false;

        return filter_var($raw, FILTER_VALIDATE_BOOLEAN);
    }
}

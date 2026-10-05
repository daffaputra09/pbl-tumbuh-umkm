<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureUserIsActive;
use App\Models\SocialAccount;
use App\Models\Umkm;
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
    private const BUSINESS_NAME_SESSION_KEY = 'oauth.business_name';

    public function redirect(Request $request): RedirectResponse
    {
        $businessName = $request->string('nama_usaha')->trim()->toString();

        if ($businessName !== '') {
            $request->validate([
                'nama_usaha' => ['string', 'max:255'],
            ]);

            $request->session()->put(self::BUSINESS_NAME_SESSION_KEY, $businessName);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $googleUser = $this->googleUser();
        } catch (Throwable $exception) {
            Log::warning('Google sign-in failed.', ['exception' => $exception]);
            $request->session()->forget(self::BUSINESS_NAME_SESSION_KEY);

            return redirect()->route('login')->withErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);
        }

        if (! $googleUser instanceof GoogleUser) {
            return redirect()->route('login')->withErrors([
                'login' => 'Masuk dengan Google gagal. Coba lagi.',
            ]);
        }

        $user = $this->resolveUser($request, $googleUser);

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

    private function resolveUser(Request $request, GoogleUser $googleUser): ?User
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
                $request->session()->forget(self::BUSINESS_NAME_SESSION_KEY);

                return null;
            }
        }

        if ($user instanceof User) {
            $request->session()->forget(self::BUSINESS_NAME_SESSION_KEY);

            return $user;
        }

        if (! is_string($email) || $email === '') {
            $request->session()->forget(self::BUSINESS_NAME_SESSION_KEY);

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

        $businessName = $request->session()->pull(self::BUSINESS_NAME_SESSION_KEY);

        if (is_string($businessName) && $businessName !== '') {
            Umkm::query()->create([
                'id_user' => $user->id,
                'nama_usaha' => $businessName,
            ]);
        }

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

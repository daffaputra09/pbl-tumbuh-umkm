<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PasswordResetController extends Controller
{
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Isi email yang terdaftar.',
            'email.email' => 'Format email belum tepat.',
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if ($user instanceof User && $user->is_active) {
            try {
                $status = Password::sendResetLink([
                    'email' => $user->email,
                ]);
            } catch (Throwable $exception) {
                Log::warning('Password reset email failed.', ['exception' => $exception]);

                return back()->withErrors([
                    'email' => 'Email belum terkirim. Coba lagi sebentar lagi.',
                ])->onlyInput('email');
            }

            if ($status === Password::RESET_THROTTLED) {
                return back()->withErrors([
                    'email' => 'Tunggu sebentar sebelum meminta tautan lagi.',
                ])->onlyInput('email');
            }
        }

        return back()->with('status', 'Jika email itu terdaftar, tautan atur ulang kata sandi sudah dikirim. Periksa kotak masuk dan folder spam.');
    }

    public function edit(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ], [
            'email.required' => 'Isi email yang terdaftar.',
            'email.email' => 'Format email belum tepat.',
            'password.required' => 'Isi kata sandi baru.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Ulangi kata sandi baru yang sama.',
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        if (! $user instanceof User || ! $user->is_active) {
            return back()->withErrors([
                'email' => 'Tautan tidak berlaku atau sudah kedaluwarsa. Minta tautan baru.',
            ])->onlyInput('email');
        }

        $status = Password::reset(
            [
                'email' => $validated['email'],
                'password' => $validated['password'],
                'password_confirmation' => $validated['password_confirmation'],
                'token' => $validated['token'],
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Kata sandi sudah diperbarui. Silakan masuk.');
        }

        return back()->withErrors([
            'email' => 'Tautan tidak berlaku atau sudah kedaluwarsa. Minta tautan baru.',
        ])->onlyInput('email');
    }
}

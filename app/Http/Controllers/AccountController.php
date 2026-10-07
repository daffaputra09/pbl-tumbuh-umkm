<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $this->accountUser($request);
        Gate::authorize('view', $user);

        return view('account.edit', [
            'user' => $user,
            'hasPassword' => $user->hasPassword(),
            'usesGoogle' => $user->socialAccounts()->where('provider', 'google')->exists(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $this->accountUser($request);
        Gate::authorize('update', $user);
        $hasPassword = $user->hasPassword();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => [Rule::excludeIf(! $hasPassword || ! $request->filled('password')), 'required', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan email yang valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'current_password.required' => 'Isi kata sandi saat ini untuk mengganti kata sandi.',
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.min' => 'Kata sandi baru minimal 8 karakter.',
            'password.confirmed' => 'Ulangi kata sandi baru yang sama.',
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $passwordCreated = ! $hasPassword && filled($validated['password'] ?? null);

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        $status = $passwordCreated
            ? 'Kata sandi sudah dibuat. Masuk berikutnya bisa memakai email dan kata sandi ini.'
            : 'Profil akun sudah disimpan.';

        return redirect()->route('account.edit')->with('status', $status);
    }

    private function accountUser(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $this->accountUser($request);

        return view('account.edit', ['user' => $user]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $this->accountUser($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => [Rule::excludeIf(! $request->filled('password')), 'required', 'current_password'],
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

        if (filled($validated['password'] ?? null)) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()->route('account.edit')->with('status', 'Profil akun sudah disimpan.');
    }

    private function accountUser(Request $request): User
    {
        $user = $request->user();

        abort_unless($user instanceof User, 403);

        return $user;
    }
}

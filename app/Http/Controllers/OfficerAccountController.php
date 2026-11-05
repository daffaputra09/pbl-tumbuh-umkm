<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OfficerAccountController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $officers = User::query()
            ->where('role', User::ROLE_OFFICER)
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        return view('pimpinan.petugas.index', [
            'officers' => $officers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Masukkan email yang valid.',
            'email.unique' => 'Email ini sudah dipakai akun lain.',
            'phone.max' => 'Nomor HP maksimal 20 karakter.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Ulangi kata sandi yang sama.',
        ]);

        $officer = User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $validated['password'],
            'role' => User::ROLE_OFFICER,
            'is_active' => true,
        ]);

        $officer->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return redirect()
            ->route('pimpinan.petugas.index')
            ->with('success', 'Akun petugas berhasil ditambahkan.');
    }

    public function toggle(Request $request, User $officer): RedirectResponse
    {
        abort_if($request->user()?->is($officer), 403);
        abort_unless($officer->hasRole(User::ROLE_OFFICER), 404);

        Gate::authorize('deactivate', $officer);

        $officer->update([
            'is_active' => ! $officer->is_active,
        ]);

        $message = $officer->is_active
            ? 'Akun petugas berhasil diaktifkan.'
            : 'Akun petugas berhasil dinonaktifkan. Petugas ini tidak bisa masuk sampai diaktifkan lagi.';

        return redirect()
            ->route('pimpinan.petugas.index')
            ->with('success', $message);
    }
}

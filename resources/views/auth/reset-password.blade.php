@extends('layouts.auth')

@section('title', 'Kata sandi baru')
@section('heading', 'Kata sandi baru')
@section('subheading', 'Buat kata sandi baru untuk akun ini. Tautan hanya berlaku satu jam.')

@section('content')
    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <x-text-field
            id="email"
            name="email"
            type="email"
            label="Email"
            :value="old('email', $email)"
            autocomplete="email"
            placeholder="nama@email.com"
            :readonly="$email !== ''"
        />

        <x-password-field
            label="Kata sandi baru"
            autocomplete="new-password"
            placeholder="Minimal 8 karakter"
        />

        <x-password-field
            id="password_confirmation"
            name="password_confirmation"
            label="Ulangi kata sandi baru"
            autocomplete="new-password"
            placeholder="Ulangi kata sandi baru"
        />

        <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-primary px-6 text-[15px] font-semibold text-primary-foreground shadow-[0_1px_0_0_rgb(255_255_255/0.2)_inset,0_8px_20px_-8px_rgb(15_118_110/0.7)] hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60">
            Simpan kata sandi
            <x-hugeicon name="ArrowRight02Icon" :size="18" class="text-primary-foreground" />
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted-foreground">
        <a href="{{ route('password.request') }}" class="font-semibold text-brand hover:text-brand-hover">Minta tautan baru</a>
    </p>
@endsection

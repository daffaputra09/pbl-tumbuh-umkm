@extends('layouts.auth')

@section('title', 'Lupa kata sandi')
@section('heading', 'Lupa kata sandi')
@section('subheading', 'Masukkan email akun. Kami mengirim tautan untuk membuat kata sandi baru.')

@section('content')
    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <x-text-field
            id="email"
            name="email"
            type="email"
            label="Email"
            :value="old('email')"
            autocomplete="email"
            placeholder="nama@email.com"
            autofocus
        />

        <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-primary px-6 text-[15px] font-semibold text-primary-foreground shadow-[0_1px_0_0_rgb(255_255_255/0.2)_inset,0_8px_20px_-8px_rgb(15_118_110/0.7)] hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60">
            Kirim tautan
            <x-hugeicon name="ArrowRight02Icon" :size="18" class="text-primary-foreground" />
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted-foreground">
        Sudah ingat kata sandinya?
        <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-hover">Masuk</a>
    </p>
@endsection

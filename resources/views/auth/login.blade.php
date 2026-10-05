@extends('layouts.auth')

@section('title', 'Masuk')
@section('heading', 'Masuk')
@section('subheading', 'Lanjutkan ke data usaha, verifikasi, atau pemantauan desa.')

@section('content')
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <x-text-field
            id="login"
            name="login"
            label="Email atau nama pengguna"
            :value="old('login')"
            autocomplete="username"
            placeholder="nama@email.com"
            autofocus
        />

        <x-password-field />

        <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-primary px-6 text-[15px] font-semibold text-primary-foreground shadow-[0_1px_0_0_rgb(255_255_255/0.2)_inset,0_8px_20px_-8px_rgb(15_118_110/0.7)] hover:bg-brand-hover">
            Masuk
            <x-hugeicon name="ArrowRight02Icon" :size="18" class="text-primary-foreground" />
        </button>
    </form>

    <div class="my-5 flex items-center gap-3 text-xs font-medium text-muted-foreground">
        <span class="h-px flex-1 bg-border"></span>
        atau
        <span class="h-px flex-1 bg-border"></span>
    </div>

    <a href="{{ route('auth.google.redirect') }}" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full border border-border bg-white px-6 text-[15px] font-semibold text-ink shadow-xs hover:border-brand-200 hover:bg-brand-50">
        <x-google-logo :size="18" />
        Lanjutkan dengan Google
    </a>

    <p class="mt-6 text-center text-sm text-muted-foreground">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-semibold text-brand hover:text-brand-hover">Daftar sebagai pelaku usaha</a>
    </p>
@endsection

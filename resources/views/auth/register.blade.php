@extends('layouts.auth')

@section('title', 'Daftar')
@section('heading', 'Daftarkan usaha')
@section('subheading', 'Buat akun pelaku usaha. Data usaha lengkap bisa diisi setelah masuk.')

@section('content')
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <x-text-field
            id="name"
            name="name"
            label="Nama lengkap"
            :value="old('name')"
            autocomplete="name"
            placeholder="Nama Anda"
            autofocus
        />

        <x-text-field
            id="email"
            name="email"
            type="email"
            label="Email"
            :value="old('email')"
            autocomplete="email"
            placeholder="nama@email.com"
        />

        <x-password-field autocomplete="new-password" placeholder="Minimal 8 karakter" />

        <x-text-field
            id="nama_usaha"
            name="nama_usaha"
            label="Nama usaha"
            :value="old('nama_usaha')"
            autocomplete="organization"
            placeholder="Nama usaha"
        />

        <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-primary px-6 text-[15px] font-semibold text-primary-foreground shadow-[0_1px_0_0_rgb(255_255_255/0.2)_inset,0_8px_20px_-8px_rgb(15_118_110/0.7)] hover:bg-brand-hover disabled:cursor-not-allowed disabled:opacity-60">
            Buat akun
            <x-hugeicon name="ArrowRight02Icon" :size="18" class="text-primary-foreground" />
        </button>
    </form>

    <div class="my-5 flex items-center gap-3 text-xs font-medium text-muted-foreground">
        <span class="h-px flex-1 bg-border"></span>
        atau
        <span class="h-px flex-1 bg-border"></span>
    </div>

    <form method="GET" action="{{ route('auth.google.redirect') }}" id="google-register">
        <input type="hidden" name="nama_usaha" value="">
        <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full border border-border bg-white px-6 text-[15px] font-semibold text-ink shadow-xs hover:border-brand-200 hover:bg-brand-50 disabled:cursor-not-allowed disabled:opacity-60">
            <x-google-logo :size="18" />
            Daftar dengan Google
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-muted-foreground">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-brand hover:text-brand-hover">Masuk</a>
    </p>

    <script>
        document.getElementById('google-register')?.addEventListener('submit', () => {
            const source = document.getElementById('nama_usaha');
            const target = document.querySelector('#google-register [name="nama_usaha"]');

            if (source && target) {
                target.value = source.value;
            }
        });
    </script>
@endsection

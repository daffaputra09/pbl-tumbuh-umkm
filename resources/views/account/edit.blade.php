@extends('layouts.app-shell')

@section('title', 'Profil akun - Tumbuh UMKM')

@push('head')
    <style>
        @keyframes auth-spin {
            to { transform: rotate(360deg); }
        }

        .auth-spinner {
            width: 1rem;
            height: 1rem;
            flex: none;
            border: 2px solid currentColor;
            border-right-color: transparent;
            border-radius: 999px;
            animation: auth-spin 0.7s linear infinite;
        }
    </style>
@endpush

@section('content')
    <div class="mx-auto grid w-full max-w-5xl items-start gap-6 lg:grid-cols-[18rem_minmax(0,1fr)] lg:gap-8">
        <aside class="flex items-center gap-4 rounded-3xl border border-brand-200 bg-brand-50 p-5 lg:block lg:p-6">
            <span class="grid size-14 shrink-0 place-items-center rounded-2xl bg-brand text-lg font-bold text-white lg:size-16 lg:text-xl" aria-hidden="true">{{ $user->initials() }}</span>
            <div class="min-w-0 lg:mt-5">
                <p class="text-sm font-semibold text-brand-hover">{{ $user->roleLabel() }}</p>
                <p class="truncate text-xl font-extrabold tracking-tight text-ink">{{ $user->name }}</p>
                <p class="mt-1 break-all text-sm text-foreground">{{ $user->email }}</p>
                @if ($usesGoogle)
                    <p class="mt-3 text-sm leading-relaxed text-brand-900">Terhubung ke Google.</p>
                @endif
            </div>
        </aside>

        <form method="POST" action="{{ route('account.update') }}" data-account-form class="rounded-3xl border border-border bg-white p-5 shadow-[0_12px_32px_-20px_rgb(15_23_42/0.35)] sm:p-8">
            @csrf
            @method('PUT')

            <h1 class="text-2xl font-extrabold tracking-tight text-ink">Profil akun</h1>
            <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">
                @if ($usesGoogle && ! $hasPassword)
                    Nama, email, dan nomor HP dipakai di akun ini. Kata sandi belum ada, jadi masuk saat ini lewat Google.
                @elseif ($usesGoogle)
                    Nama, email, dan nomor HP dipakai di akun ini. Akun ini juga terhubung ke Google.
                @else
                    Nama, email, dan nomor HP dipakai di akun ini. Peran tidak bisa diganti dari sini.
                @endif
            </p>

            @if (session('status'))
                <p class="mt-5 rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
            @endif

            @if ($errors->any())
                <div class="mt-5 rounded-2xl border border-destructive/20 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert">
                    <ul class="space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 space-y-4">
                <div>
                    <x-text-field
                        id="name"
                        name="name"
                        label="Nama"
                        :value="old('name', $user->name)"
                        autocomplete="name"
                        placeholder="Nama lengkap"
                        autofocus
                    />
                </div>

                <div>
                    <x-text-field
                        id="email"
                        name="email"
                        type="email"
                        label="Email"
                        :value="old('email', $user->email)"
                        autocomplete="email"
                        placeholder="nama@email.com"
                    />
                </div>

                <div>
                    <x-text-field
                        id="phone"
                        name="phone"
                        type="tel"
                        label="Nomor HP"
                        :value="old('phone', $user->phone)"
                        :required="false"
                        autocomplete="tel"
                        placeholder="08xxxxxxxxxx"
                        maxlength="20"
                    />
                </div>
            </div>

            <div class="mt-8 border-t border-border pt-6">
                <h2 class="text-lg font-extrabold tracking-tight text-ink">{{ $hasPassword ? 'Ganti kata sandi' : 'Buat kata sandi' }}</h2>
                <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">
                    @if ($hasPassword)
                        Kosongkan kalau tidak diubah. Untuk mengganti, isi kata sandi saat ini dulu.
                    @elseif ($usesGoogle)
                        Isi dua kolom di bawah kalau kamu ingin masuk dengan email, bukan hanya Google.
                    @else
                        Isi dua kolom di bawah untuk membuat kata sandi akun ini.
                    @endif
                </p>

                <div class="mt-4 space-y-4">
                    @if ($hasPassword)
                        <x-password-field
                            id="current_password"
                            name="current_password"
                            label="Kata sandi saat ini"
                            autocomplete="current-password"
                            placeholder="Kata sandi saat ini"
                            :required="false"
                        />
                    @endif

                    <x-password-field
                        id="password"
                        name="password"
                        label="Kata sandi baru"
                        autocomplete="new-password"
                        placeholder="Minimal 8 karakter"
                        :required="false"
                        minlength="8"
                    />

                    <x-password-field
                        id="password_confirmation"
                        name="password_confirmation"
                        label="Ulangi kata sandi baru"
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi baru"
                        :required="false"
                    />
                </div>
            </div>

            <div class="mt-8">
                <button type="submit" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-full bg-primary px-6 text-[15px] font-semibold text-primary-foreground shadow-[0_1px_0_0_rgb(255_255_255/0.2)_inset,0_8px_20px_-8px_rgb(15_118_110/0.7)] hover:bg-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto sm:min-w-48">
                    Simpan perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        const accountForm = document.querySelector('[data-account-form]');

        accountForm?.addEventListener('submit', (event) => {
            if (accountForm.dataset.submitting === 'true') {
                event.preventDefault();

                return;
            }

            accountForm.dataset.submitting = 'true';

            const button = event.submitter;

            if (button instanceof HTMLButtonElement) {
                button.style.opacity = '0.6';
                button.style.cursor = 'not-allowed';
                button.setAttribute('aria-busy', 'true');

                button.querySelectorAll('svg').forEach((icon) => {
                    icon.style.display = 'none';
                });

                if (!button.querySelector('.auth-spinner')) {
                    const spinner = document.createElement('span');
                    spinner.className = 'auth-spinner';
                    spinner.setAttribute('aria-hidden', 'true');
                    button.prepend(spinner);
                }
            }

            window.setTimeout(() => {
                if (button instanceof HTMLButtonElement) {
                    button.disabled = true;
                }
            }, 0);
        });

        document.querySelectorAll('[data-password-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.getAttribute('data-password-toggle'));

                if (!input) {
                    return;
                }

                const showing = input.type === 'text';
                input.type = showing ? 'password' : 'text';
                button.setAttribute('aria-pressed', showing ? 'false' : 'true');
                button.setAttribute('aria-label', showing ? 'Tampilkan kata sandi' : 'Sembunyikan kata sandi');
                button.querySelector('[data-icon="show"]').classList.toggle('hidden', !showing);
                button.querySelector('[data-icon="hide"]').classList.toggle('hidden', showing);
            });
        });
    </script>
@endpush

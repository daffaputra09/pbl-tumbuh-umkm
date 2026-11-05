@extends('layouts.app-shell')

@section('title', 'Kelola Petugas — TUMBUH UMKM')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        @if (session('success'))
            <p class="rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-900" role="status">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="rounded-2xl border border-destructive/20 bg-destructive/10 px-4 py-3 text-sm text-destructive" role="alert">
                <p class="font-semibold">Periksa isian berikut.</p>
                <ul class="mt-1 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-ink sm:text-3xl">Kelola petugas</h1>
            <p class="mt-1.5 max-w-2xl text-sm leading-relaxed text-muted-foreground">
                Tambah akun petugas desa dan nonaktifkan akun yang tidak lagi bertugas. Peran akun ini selalu Petugas Desa dan tidak bisa diubah dari halaman ini.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
            <div class="rounded-2xl border border-border bg-white px-4 py-4">
                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Total</p>
                <p class="mt-1 text-2xl font-bold text-ink">{{ $officers->count() }}</p>
            </div>
            <div class="rounded-2xl border border-border bg-white px-4 py-4">
                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Aktif</p>
                <p class="mt-1 text-2xl font-bold text-ink">{{ $officers->where('is_active', true)->count() }}</p>
            </div>
            <div class="col-span-2 rounded-2xl border border-border bg-white px-4 py-4 sm:col-span-1">
                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Nonaktif</p>
                <p class="mt-1 text-2xl font-bold text-ink">{{ $officers->where('is_active', false)->count() }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('pimpinan.petugas.store') }}" class="rounded-3xl border border-border bg-white p-5 sm:p-6">
            @csrf
            <h2 class="text-lg font-bold text-ink">Tambah petugas</h2>
            <p class="mt-1 text-sm text-muted-foreground">Petugas masuk dengan email dan kata sandi ini, lalu bisa menggantinya dari profil akun.</p>

            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                <x-text-field
                    id="name"
                    name="name"
                    label="Nama"
                    :value="old('name')"
                    autocomplete="name"
                    placeholder="Nama lengkap"
                />
                <x-text-field
                    id="email"
                    name="email"
                    type="email"
                    label="Email"
                    :value="old('email')"
                    autocomplete="off"
                    placeholder="nama@desa.test"
                />
                <x-text-field
                    id="phone"
                    name="phone"
                    type="tel"
                    label="Nomor HP"
                    :value="old('phone')"
                    :required="false"
                    autocomplete="tel"
                    placeholder="08xxxxxxxxxx"
                />
                <div class="hidden sm:block" aria-hidden="true"></div>
                <x-password-field
                    id="password"
                    name="password"
                    label="Kata sandi"
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />
                <x-password-field
                    id="password_confirmation"
                    name="password_confirmation"
                    label="Ulangi kata sandi"
                    autocomplete="new-password"
                    placeholder="Ketik ulang kata sandi"
                />
            </div>

            <div class="mt-5">
                <button type="submit" class="inline-flex h-11 items-center rounded-xl bg-brand px-4 text-sm font-semibold text-white hover:bg-brand-hover">
                    Simpan akun petugas
                </button>
            </div>
        </form>

        <div class="overflow-hidden rounded-2xl border border-border bg-white">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[40rem] text-left text-sm text-ink">
                    <thead class="bg-slate-50 text-xs font-semibold tracking-wide text-slate-500 uppercase">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Nomor HP</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($officers as $officer)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $officer->name }}</td>
                                <td class="px-4 py-3">{{ $officer->email }}</td>
                                <td class="px-4 py-3">{{ $officer->phone ?: 'Belum diisi' }}</td>
                                <td class="px-4 py-3">
                                    @if ($officer->is_active)
                                        <span class="inline-flex rounded-full bg-brand-50 px-2.5 py-1 text-xs font-semibold text-brand-hover">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <form
                                        method="POST"
                                        action="{{ route('pimpinan.petugas.toggle', $officer) }}"
                                        onsubmit="return confirm(@js($officer->is_active ? 'Nonaktifkan akun '.$officer->name.'? Petugas ini tidak bisa masuk sampai diaktifkan lagi.' : 'Aktifkan kembali akun '.$officer->name.'?'))"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex h-9 items-center rounded-xl border border-border px-3 text-sm font-semibold text-ink hover:bg-slate-50">
                                            {{ $officer->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-muted-foreground">
                                    Belum ada akun petugas. Tambah akun lewat formulir di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
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

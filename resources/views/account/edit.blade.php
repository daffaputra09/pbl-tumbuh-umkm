@extends('layouts.app-shell')

@section('title', 'Profil akun - Tumbuh UMKM')

@section('content')
    <div class="mx-auto w-full max-w-lg">
        <div class="rounded-3xl bg-white p-8 shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-slate-900/5 sm:p-10">
            <div class="mb-8">
                <p class="text-sm font-semibold text-teal-700">{{ $user->roleLabel() }}</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Profil akun</h1>
                <p class="mt-2 text-sm leading-relaxed text-slate-500">Ubah nama, email, nomor HP, atau kata sandi. Peran akun tidak bisa diganti dari halaman ini.</p>
            </div>

            @if (session('status'))
                <p class="mb-6 rounded-2xl bg-teal-50 px-4 py-3 text-sm font-medium text-teal-800" role="status">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('account.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-1.5 block text-sm font-semibold text-slate-700">Nama</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                        class="block w-full rounded-xl border-0 px-3.5 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6">
                    @error('name')
                        <p id="name-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-1.5 block text-sm font-semibold text-slate-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required autocomplete="email" @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        class="block w-full rounded-xl border-0 px-3.5 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6">
                    @error('email')
                        <p id="email-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="mb-1.5 block text-sm font-semibold text-slate-700">Nomor HP</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" autocomplete="tel" maxlength="20" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror
                        class="block w-full rounded-xl border-0 px-3.5 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6" placeholder="08xxxxxxxxxx">
                    @error('phone')
                        <p id="phone-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <fieldset class="space-y-5 border-t border-slate-200 pt-5">
                    <legend class="text-sm font-semibold text-slate-700">Ganti kata sandi</legend>
                    <p class="text-sm text-slate-500">Kosongkan jika kata sandi tetap.</p>

                    <div>
                        <label for="current_password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata sandi saat ini</label>
                        <input type="password" name="current_password" id="current_password" autocomplete="current-password" @error('current_password') aria-invalid="true" aria-describedby="current-password-error" @enderror
                            class="block w-full rounded-xl border-0 px-3.5 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6">
                        @error('current_password')
                            <p id="current-password-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-semibold text-slate-700">Kata sandi baru</label>
                        <input type="password" name="password" id="password" autocomplete="new-password" @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                            class="block w-full rounded-xl border-0 px-3.5 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6">
                        @error('password')
                            <p id="password-error" class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-semibold text-slate-700">Ulangi kata sandi baru</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
                            class="block w-full rounded-xl border-0 px-3.5 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6">
                    </div>
                </fieldset>

                <div class="pt-2">
                    <button type="submit" class="flex w-full justify-center rounded-xl bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600">
                        Simpan profil
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

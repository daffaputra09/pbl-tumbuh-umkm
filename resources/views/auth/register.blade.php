<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi UMKM - Tumbuh UMKM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen relative font-sans text-slate-800 selection:bg-teal-500 selection:text-white">
    <!-- Back Button -->
    <div class="absolute top-6 left-6 sm:top-10 sm:left-10 z-10">
        <a href="{{ route('landing') }}" class="group flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-medium text-slate-600 shadow-sm ring-1 ring-slate-900/5 transition-all hover:bg-slate-50 hover:text-teal-700 hover:shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:-translate-x-1"><path d="m15 18-6-6 6-6"/></svg>
            Kembali ke Beranda
        </a>
    </div>

    <!-- Background Pattern from Landing Page -->
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
        <div class="absolute inset-0 bg-grid mask-radial"></div>
        <div class="absolute -top-40 left-1/2 h-[520px] w-[900px] -translate-x-1/2 rounded-full bg-brand-200/50 blur-[120px]"></div>
        <div class="absolute top-40 -right-40 h-80 w-80 rounded-full bg-sun-200/50 blur-[100px]"></div>
        <div class="absolute top-72 -left-32 h-72 w-72 rounded-full bg-brand-300/30 blur-[100px]"></div>

        <div class="absolute top-36 left-[6%] hidden text-brand-400/70 md:block animate-float-slow">
            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="rotate-[-12deg]"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
        </div>
        <div class="absolute top-64 right-[8%] hidden text-sun/60 md:block animate-float">
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" class="rotate-[18deg]"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
        </div>

        <svg class="absolute top-28 left-0 hidden h-[420px] w-full lg:block opacity-70" viewBox="0 0 1440 420" preserveAspectRatio="none" fill="none">
            <path d="M-20 380 C 180 360, 240 250, 380 260 S 560 330, 700 220 S 980 120, 1100 150 S 1320 60, 1460 40" stroke="url(#growth)" stroke-width="1.5" stroke-dasharray="6 8"></path>
            <defs>
                <linearGradient id="growth" x1="0" x2="1440" y1="0" y2="0" gradientUnits="userSpaceOnUse">
                    <stop stop-color="#14b8a6" stop-opacity="0"></stop>
                    <stop offset="0.3" stop-color="#14b8a6" stop-opacity="0.5"></stop>
                    <stop offset="0.7" stop-color="#d97706" stop-opacity="0.5"></stop>
                    <stop offset="1" stop-color="#d97706" stop-opacity="0"></stop>
                </linearGradient>
            </defs>
        </svg>
    </div>

    <div class="w-full max-w-lg px-5 py-12">
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-slate-900/5 p-8 sm:p-10 relative z-10">
            <div class="text-center mb-10">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Daftarkan Usaha</h1>
                <p class="text-slate-500 mt-2 text-sm leading-relaxed">Mulai langkah baru bersama Tumbuh UMKM untuk mengembangkan potensi usaha Anda.</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-50/80 backdrop-blur-sm border border-red-200 text-red-600 px-4 py-3 rounded-2xl mb-6 shadow-sm" role="alert">
                    <ul class="list-disc list-inside text-sm font-medium space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 transition-shadow" placeholder="Masukkan nama Anda">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 transition-shadow" placeholder="nama@email.com">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" id="password" required
                        class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 transition-shadow" placeholder="Min. 8 karakter">
                </div>

                <div>
                    <label for="nama_usaha" class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Usaha</label>
                    <input type="text" name="nama_usaha" id="nama_usaha" value="{{ old('nama_usaha') }}" required
                        class="block w-full rounded-xl border-0 py-2.5 px-3.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-teal-600 sm:text-sm sm:leading-6 transition-shadow" placeholder="Masukkan nama usaha Anda">
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="group flex w-full justify-center items-center gap-2 rounded-xl bg-teal-600 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-teal-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-teal-600 transition-all hover:shadow-md hover:shadow-teal-600/20 active:scale-[0.98]">
                        Buat Akun
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform group-hover:translate-x-1"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-slate-600">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-teal-600 hover:text-teal-500 transition-colors">Masuk di sini</a>
            </div>
        </div>
    </div>
</body>
</html>

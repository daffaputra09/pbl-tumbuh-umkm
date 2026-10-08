<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Tumbuh UMKM</title>
    @vite(['resources/css/app.css'])
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
</head>
<body class="bg-canvas font-sans text-foreground antialiased">
    <div class="relative isolate min-h-svh overflow-hidden">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-grid mask-radial"></div>
            <div class="absolute -top-40 left-1/2 h-[520px] w-[min(900px,120vw)] -translate-x-1/2 rounded-full bg-brand-200/50 blur-3xl"></div>
            <div class="absolute top-40 -right-32 h-80 w-80 rounded-full bg-sun-200/50 blur-3xl"></div>
            <div class="absolute top-72 -left-24 h-72 w-72 rounded-full bg-brand-300/30 blur-3xl"></div>
            <div class="absolute top-28 left-[8%] hidden text-brand-400/70 lg:block">
                <x-hugeicon name="Leaf01Icon" :size="44" class="animate-float-slow -rotate-12" />
            </div>
            <div class="absolute top-64 right-[8%] hidden text-sun/60 lg:block">
                <x-hugeicon name="Leaf01Icon" :size="34" class="animate-float rotate-12" />
            </div>
        </div>

        <div class="mx-auto grid min-h-svh w-full max-w-6xl items-center gap-8 px-4 py-6 sm:px-6 lg:grid-cols-[minmax(0,1.05fr)_minmax(22rem,28rem)] lg:gap-16 lg:px-8 lg:py-10">
            <section class="hidden lg:block">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2.5" aria-label="Tumbuh UMKM, ke beranda">
                    <span class="relative grid size-11 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-brand-500 to-brand-hover text-white shadow-[0_8px_20px_-8px_rgb(15_118_110/0.9)]">
                        <span class="absolute inset-x-0 top-0 h-1/2 bg-white/15"></span>
                        <x-hugeicon name="Plant02Icon" :size="22" class="relative text-white" />
                    </span>
                    <span class="text-xl font-extrabold tracking-tight text-ink">Tumbuh<span class="text-brand">UMKM</span></span>
                </a>

                <h2 class="mt-8 max-w-xl text-5xl leading-[1.05] font-extrabold tracking-[-0.035em] text-ink">
                    Dari data, menjadi <span class="text-brand">aksi</span> untuk UMKM desa.
                </h2>
                <p class="mt-5 max-w-md text-base leading-relaxed text-muted-foreground">
                    Satu tempat untuk mendata usaha, memahami kendala, dan memantau pembinaan di desa.
                </p>

                <ul class="mt-8 flex flex-wrap gap-x-5 gap-y-2 text-sm">
                    <li class="font-medium text-muted-foreground">Dirancang untuk</li>
                    @foreach (['Pelaku UMKM', 'Petugas Desa', 'Kepala Desa'] as $role)
                        <li class="flex items-center gap-1.5 font-semibold text-ink">
                            <span class="grid size-5 place-items-center rounded-full bg-brand-100 text-brand">
                                <x-hugeicon name="Tick02Icon" :size="12" />
                            </span>
                            {{ $role }}
                        </li>
                    @endforeach
                </ul>
            </section>

            <div class="mx-auto w-full max-w-md lg:mx-0 lg:max-w-none">
                <a href="{{ route('landing') }}" class="mb-4 inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-muted-foreground hover:text-ink lg:hidden">
                    <x-hugeicon name="ArrowLeft02Icon" :size="18" />
                    Beranda
                </a>

                <div class="rounded-3xl border border-white bg-white/90 p-5 shadow-[0_20px_40px_-16px_rgb(15_23_42/0.28)] backdrop-blur-md sm:p-8">
                    <a href="{{ route('landing') }}" class="mb-6 flex items-center gap-2.5 lg:hidden" aria-label="Tumbuh UMKM, ke beranda">
                        <span class="relative grid size-9 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-brand-500 to-brand-hover text-white">
                            <span class="absolute inset-x-0 top-0 h-1/2 bg-white/15"></span>
                            <x-hugeicon name="Plant02Icon" :size="20" class="relative text-white" />
                        </span>
                        <span class="text-[17px] font-extrabold tracking-tight text-ink">Tumbuh<span class="text-brand">UMKM</span></span>
                    </a>

                    <h1 class="text-2xl font-extrabold tracking-tight text-ink">@yield('heading')</h1>
                    <p class="mt-1.5 text-sm leading-relaxed text-muted-foreground">@yield('subheading')</p>

                    @if (session('status'))
                        <div class="mt-5 rounded-2xl border border-brand-200 bg-brand-50 px-3 py-2.5 text-sm text-brand-900" role="status">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 rounded-2xl border border-destructive/20 bg-destructive/10 px-3 py-2.5 text-sm text-destructive" role="alert">
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-6">
                        @yield('content')
                    </div>
                </div>

                <p class="mt-4 hidden text-center text-sm lg:block">
                    <a href="{{ route('landing') }}" class="inline-flex min-h-11 items-center gap-2 font-semibold text-muted-foreground hover:text-ink">
                        <x-hugeicon name="ArrowLeft02Icon" :size="18" />
                        Kembali ke beranda
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('form').forEach((form) => {
            form.addEventListener('submit', (event) => {
                if (form.dataset.submitting === 'true') {
                    event.preventDefault();

                    return;
                }

                form.dataset.submitting = 'true';

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
</body>
</html>

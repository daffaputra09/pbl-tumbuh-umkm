@inject('shellData', 'App\Services\DashboardService')
@php
    $shellUser = auth()->user();
    $shell = $shellData->shellProps($shellUser, (int) ($pendingCount ?? ($stats['menunggu'] ?? 0)));
    $navigation = \App\Navigation\RoleNavigation::groups($shell['accountRole']);
    $village = $shell['village'];
@endphp
<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#0F766E">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Tumbuh UMKM')</title>
        @fonts
        @vite(['resources/css/app.css'])
        @stack('head')
    </head>
    <body>
        <div class="min-h-screen bg-background lg:pl-64">
            <aside class="fixed inset-y-0 left-0 z-30 hidden w-64 border-r border-border bg-white lg:block">
                @include('layouts.partials.sidebar')
            </aside>

            <div id="mobile-nav" class="fixed inset-0 z-50 hidden lg:hidden" role="dialog" aria-modal="true" aria-label="Menu navigasi">
                <button type="button" class="absolute inset-0 bg-ink/40" data-close-nav aria-label="Tutup menu"></button>
                <aside class="absolute inset-y-0 left-0 w-[min(18rem,85vw)] bg-white shadow-2xl">
                    <button type="button" class="absolute top-3.5 right-3 grid size-9 place-items-center rounded-lg text-slate-500 hover:bg-slate-100" data-close-nav aria-label="Tutup menu">
                        <x-hugeicon name="Cancel01Icon" :size="20" />
                    </button>
                    @include('layouts.partials.sidebar')
                </aside>
            </div>

            <header class="sticky top-0 z-20 border-b border-border bg-white">
                <div class="mx-auto flex h-16 max-w-[1400px] items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button type="button" id="open-nav" class="-ml-1 grid size-10 place-items-center rounded-xl text-ink hover:bg-slate-100 lg:hidden" aria-label="Buka menu">
                        <x-hugeicon name="Menu01Icon" :size="22" />
                    </button>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-xs font-medium text-slate-500">
                            <span class="hidden sm:inline">Tumbuh UMKM / </span>{{ \App\Navigation\RoleNavigation::headerTitle($shell['accountRole']) }}
                        </p>
                        <p class="truncate text-sm font-bold text-ink sm:text-base">{{ $village['name'] }}</p>
                    </div>
                    @if ($shell['accountRole'] === \App\Models\User::ROLE_OFFICER)
                        <a href="{{ route('dashboard') }}#antrean-verifikasi" class="grid size-10 place-items-center rounded-xl text-slate-600 hover:bg-brand-50 hover:text-brand-hover" aria-label="Antrean verifikasi">
                            <x-hugeicon name="Notification01Icon" :size="21" />
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl px-3 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                            <x-hugeicon name="Logout01Icon" :size="18" />
                            Keluar
                        </button>
                    </form>
                    <a href="{{ route('account.edit') }}" class="flex min-w-0 items-center gap-2.5 border-l border-border pl-3" aria-label="Profil {{ $shellUser->name }}, {{ $shellUser->roleLabel() }}">
                        <span class="grid size-9 shrink-0 place-items-center rounded-full bg-brand-100 text-xs font-bold text-brand-hover">{{ $shellUser->initials() }}</span>
                        <span class="min-w-0 leading-tight">
                            <span class="block max-w-24 truncate text-sm font-semibold text-ink sm:max-w-40">{{ $shellUser->name }}</span>
                            <span class="block max-w-24 truncate text-xs text-slate-500 sm:max-w-40">{{ $shellUser->roleLabel() }}</span>
                        </span>
                    </a>
                </div>
            </header>

            <main class="mx-auto max-w-[1400px] px-4 py-5 sm:px-6 sm:py-6 lg:px-8 lg:py-8">
                @yield('content')
            </main>
        </div>
        <script>
            const mobileNav = document.getElementById('mobile-nav');
            document.getElementById('open-nav')?.addEventListener('click', () => mobileNav?.classList.remove('hidden'));
            mobileNav?.querySelectorAll('[data-close-nav]').forEach((button) => {
                button.addEventListener('click', () => mobileNav.classList.add('hidden'));
            });
        </script>
        @stack('scripts')
    </body>
</html>

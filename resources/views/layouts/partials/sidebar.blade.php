<div class="flex h-full flex-col bg-white">
    <div class="flex h-16 shrink-0 items-center px-5">
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5" aria-label="Tumbuh UMKM, ke halaman utama">
            <span class="grid size-9 place-items-center rounded-xl bg-brand text-white">
                <x-hugeicon name="Plant02Icon" :size="20" class="text-white" />
            </span>
            <span class="text-[17px] font-extrabold tracking-tight text-ink">Tumbuh<span class="text-brand">UMKM</span></span>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 pt-2 pb-6" aria-label="Menu dashboard">
        @foreach ($navigation as $group)
            <div class="mt-5 first:mt-2">
                <p class="px-3 pb-2 text-[11px] font-bold tracking-wider text-slate-400 uppercase">{{ $group['title'] }}</p>
                <ul class="flex flex-col gap-0.5">
                    @foreach ($group['items'] as $item)
                        <li>
                            @if ($item['href'])
                                <a
                                    href="{{ $item['href'] }}"
                                    @if (\App\Navigation\RoleNavigation::isCurrent($item['href'])) aria-current="page" @endif
                                    class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold {{ \App\Navigation\RoleNavigation::isCurrent($item['href']) ? 'bg-brand text-white' : 'text-slate-600 hover:bg-brand-50 hover:text-brand-hover' }}"
                                >
                                    <x-hugeicon :name="$item['icon']" />
                                    {{ $item['label'] }}
                                </a>
                            @else
                                <span class="flex cursor-not-allowed items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-slate-400" title="Halaman ini sedang dikerjakan" aria-disabled="true">
                                    <x-hugeicon :name="$item['icon']" />
                                    <span class="flex-1">{{ $item['label'] }}</span>
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="border-t border-border p-4">
        <form method="POST" action="{{ route('logout') }}" class="mb-2">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                <x-hugeicon name="Logout01Icon" :size="18" />
                Keluar
            </button>
        </form>
        <a href="{{ route('landing') }}" class="flex items-center gap-2 rounded-lg px-1 py-1 text-xs font-medium text-slate-500 hover:text-brand-hover">
            <x-hugeicon name="Home01Icon" :size="15" />
            {{ $village['name'] }}, {{ $village['district'] }}
        </a>
    </div>
</div>

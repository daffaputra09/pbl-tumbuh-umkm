@extends('layouts.app-shell')

@section('title', 'Program Bantuan — TUMBUH UMKM')

@section('content')
    <div class="space-y-6">
        @if(session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 shrink-0" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-center gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-rose-600 shrink-0" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-800">
                <div class="font-semibold mb-1">Terdapat beberapa kesalahan:</div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-brand transition-colors mb-2">
                    <x-hugeicon name="ArrowLeft02Icon" :size="16" />
                    Kembali ke Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Program Bantuan</h1>
                <p class="mt-1 text-sm text-slate-600 max-w-3xl">
                    Kelola data program bantuan UMKM dan integrasikan dengan kategori kendala untuk pencocokan rekomendasi secara otomatis.
                </p>
            </div>
            <div class="shrink-0">
                <a
                    href="{{ route('petugas.program-bantuan.create') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Program
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Total Program</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-600">
                        <x-hugeicon name="GiftIcon" :size="18" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-ink">{{ $stats['total'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Seluruh program terdaftar</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Program Aktif</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-emerald-700">{{ $stats['active'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Siap direkomendasikan</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Draf</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-amber-50 text-amber-600">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-amber-600">{{ $stats['draft'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Belum dipublikasikan</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Ditutup / Arsip</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-400">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                            <rect width="20" height="5" x="2" y="3" rx="1"/>
                            <path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/>
                            <path d="M10 12h4"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-slate-600">{{ $stats['closed'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Masa program selesai</div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('petugas.program-bantuan.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center">
                <div class="relative flex-1">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="{{ $currentSearch }}"
                        placeholder="Cari nama program atau penyelenggara..."
                        class="w-full rounded-xl border border-slate-200 pl-10 pr-4 py-2 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                    >
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <select
                        name="status"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                    >
                        <option value="">Semua Status</option>
                        <option value="active" @selected($currentStatus === 'active')>Aktif</option>
                        <option value="draft" @selected($currentStatus === 'draft')>Draf</option>
                        <option value="closed" @selected($currentStatus === 'closed')>Ditutup</option>
                        <option value="archived" @selected($currentStatus === 'archived')>Diarsipkan</option>
                    </select>

                    <select
                        name="category_id"
                        class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-ink focus:border-brand focus:outline-none focus:ring-1 focus:ring-brand"
                    >
                        <option value="">Semua Kategori Kendala</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string) $currentCategoryId === (string) $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition-colors"
                    >
                        Filter
                    </button>

                    @if($currentSearch || $currentStatus || $currentCategoryId)
                        <a
                            href="{{ route('petugas.program-bantuan.index') }}"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 px-3.5 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold tracking-wider text-slate-500 uppercase">
                            <th class="py-3.5 pl-6 pr-3 w-12 text-center">No.</th>
                            <th class="py-3.5 px-3 min-w-[220px]">Program & Penyelenggara</th>
                            <th class="py-3.5 px-3 min-w-[240px]">Kategori Kendala Terkait</th>
                            <th class="py-3.5 px-3 text-center">Kuota</th>
                            <th class="py-3.5 px-3 text-center">Periode</th>
                            <th class="py-3.5 px-3 text-center">Status</th>
                            <th class="py-3.5 pl-3 pr-6 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($programs as $index => $program)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 pl-6 pr-3 text-center text-slate-400 text-xs font-semibold">
                                    {{ $programs->firstItem() + $index }}
                                </td>
                                <td class="py-4 px-3">
                                    <div class="font-semibold text-ink">
                                        <a href="{{ route('petugas.program-bantuan.show', $program) }}" class="hover:text-brand transition-colors">
                                            {{ $program->name }}
                                        </a>
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        <span>{{ $program->provider }}</span>
                                        @if($program->url)
                                            <a href="{{ $program->url }}" target="_blank" rel="noopener noreferrer" class="text-brand hover:underline inline-flex items-center gap-0.5">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                                    <polyline points="15 3 21 3 21 9"/>
                                                    <line x1="10" y1="14" x2="21" y2="3"/>
                                                </svg>
                                                Tautan
                                            </a>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse($program->obstacleCategories as $category)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-700 border border-teal-200/80">
                                                <x-hugeicon name="Tag01Icon" :size="12" class="text-teal-500" />
                                                <span>{{ $category->name }}</span>
                                                <span class="text-[10px] text-teal-600 font-semibold bg-teal-100/70 px-1 py-0.2 rounded">
                                                    {{ $category->pivot->minimum_level === 'high' ? 'Tinggi' : 'Sedang' }}
                                                </span>
                                            </span>
                                        @empty
                                            <span class="text-xs text-rose-500 italic">Belum terhubung ke kategori</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-4 px-3 text-center text-xs text-slate-600">
                                    @if($program->quota)
                                        <span class="font-semibold text-ink">{{ number_format($program->quota) }}</span>
                                        <span class="text-[10px] text-slate-400 block">kuota</span>
                                    @else
                                        <span class="text-slate-400 italic">Tak terbatas</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-center text-xs text-slate-600">
                                    @if($program->starts_on || $program->ends_on)
                                        <div class="whitespace-nowrap">
                                            {{ $program->starts_on ? $program->starts_on->format('d/m/Y') : '—' }}
                                            s.d.
                                            {{ $program->ends_on ? $program->ends_on->format('d/m/Y') : '—' }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Berkelanjutan</span>
                                    @endif
                                </td>
                                <td class="py-4 px-3 text-center">
                                    @php
                                        $statusBadge = match($program->status) {
                                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                            'draft' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'closed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                            'archived' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold border {{ $statusBadge }}">
                                        @if($program->status === 'active')
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                        @elseif($program->status === 'draft')
                                            <span class="size-1.5 rounded-full bg-amber-500"></span>
                                        @else
                                            <span class="size-1.5 rounded-full bg-slate-400"></span>
                                        @endif
                                        {{ $program->statusLabel() }}
                                    </span>
                                </td>
                                <td class="py-4 pl-3 pr-6 text-center">
                                    <div class="flex items-center justify-center">
                                        <a
                                            href="{{ route('petugas.program-bantuan.show', $program) }}"
                                            class="grid size-8 place-items-center rounded-lg border border-slate-300 bg-white text-slate-600 hover:border-brand hover:bg-brand-50 hover:text-brand transition-colors shadow-2xs"
                                            title="Lihat Detail Program"
                                            aria-label="Lihat Detail Program"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center">
                                    <div class="mx-auto grid size-12 place-items-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                        <x-hugeicon name="GiftIcon" :size="24" />
                                    </div>
                                    <div class="text-sm font-semibold text-ink">Tidak ada program bantuan yang ditemukan</div>
                                    <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                        @if($currentSearch || $currentStatus || $currentCategoryId)
                                            Coba ubah kata kunci pencarian atau bersihkan filter yang aktif.
                                        @else
                                            Belum ada data program bantuan. Klik tombol Tambah Program untuk membuat program baru.
                                        @endif
                                    </p>
                                    @if(!($currentSearch || $currentStatus || $currentCategoryId))
                                        <div class="mt-4">
                                            <a
                                                href="{{ route('petugas.program-bantuan.create') }}"
                                                class="inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M12 5v14M5 12h14"/>
                                                </svg>
                                                Tambah Program Pertama
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($programs->hasPages())
                <div class="border-t border-slate-200 px-6 py-4 bg-slate-50/50">
                    {{ $programs->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection

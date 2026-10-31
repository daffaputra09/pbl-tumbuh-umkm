@extends('layouts.app-shell')

@section('title', 'Rekomendasi Program UMKM — TUMBUH UMKM')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Rekomendasi Program UMKM</h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-600">
                Daftar program bantuan yang cocok untuk setiap UMKM berdasarkan hasil asesmen kendala, beserta minat yang didaftarkan pelaku usaha.
            </p>
        </div>

        {{-- Ringkasan Statistik --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-border bg-white p-4 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">UMKM Memperoleh Rekomendasi</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold tracking-tight text-ink">{{ $totalBusinessesCount }}</span>
                    <span class="text-xs text-slate-500">Usaha</span>
                </div>
            </div>
            <div class="rounded-2xl border border-border bg-white p-4 shadow-xs">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total Program Rekomendasi</p>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-2xl font-bold tracking-tight text-brand">{{ $totalRecommendationsCount }}</span>
                    <span class="text-xs text-slate-500">Program</span>
                </div>
            </div>
        </div>

        {{-- Panel Filter & Pencarian --}}
        <section aria-labelledby="filter-rekomendasi-heading" class="rounded-2xl border border-border bg-white p-5 shadow-xs">
            <h2 id="filter-rekomendasi-heading" class="text-sm font-semibold text-ink">Filter dan Pencarian Rekomendasi</h2>
            <form method="get" action="{{ request()->url() }}" class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-end">
                <div>
                    <label for="q" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Cari Nama UMKM</label>
                    <input
                        type="text"
                        id="q"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Nama UMKM atau pemilik…"
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-hidden focus:ring-1 focus:ring-brand"
                    >
                </div>

                <div>
                    <label for="kategori" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Kategori Kendala</label>
                    <select
                        id="kategori"
                        name="kategori"
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-hidden focus:ring-1 focus:ring-brand"
                    >
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($selectedCategoryId === $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="minat" class="block text-xs font-semibold uppercase tracking-wide text-slate-600">Status Minat Program</label>
                    <select
                        id="minat"
                        name="minat"
                        class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-hidden focus:ring-1 focus:ring-brand"
                    >
                        <option value="">Semua status minat</option>
                        <option value="sudah" @selected($selectedMinat === 'sudah')>Sudah mendaftar</option>
                        <option value="belum" @selected($selectedMinat === 'belum')>Belum mendaftar</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="submit"
                        class="inline-flex flex-1 items-center justify-center rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-brand-hover transition"
                    >
                        Terapkan
                    </button>
                    @if ($search !== '' || $selectedCategoryId || $selectedMinat)
                        <a
                            href="{{ request()->url() }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                            title="Reset filter"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            @if ($search !== '' || $selectedCategoryName || $selectedMinat)
                <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3 text-xs text-slate-600">
                    <span class="font-semibold text-slate-700">Filter aktif:</span>
                    @if ($search !== '')
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-700">
                            Pencarian: "{{ $search }}"
                        </span>
                    @endif
                    @if ($selectedCategoryName)
                        <span class="inline-flex items-center gap-1 rounded-full bg-brand/10 px-2.5 py-1 font-medium text-brand">
                            Kendala: {{ $selectedCategoryName }}
                        </span>
                    @endif
                    @if ($selectedMinat === 'sudah')
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 font-medium text-emerald-800">
                            Minat: Sudah mendaftar
                        </span>
                    @elseif ($selectedMinat === 'belum')
                        <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 font-medium text-slate-700">
                            Minat: Belum mendaftar
                        </span>
                    @endif
                </div>
            @endif
        </section>

        {{-- Tabel Daftar UMKM (Ringkas) --}}
        <div class="overflow-hidden rounded-2xl border border-border bg-white shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-border">
                        <tr>
                            <th class="px-5 py-3.5">Nama UMKM</th>
                            <th class="px-5 py-3.5">Kategori Kendala</th>
                            <th class="px-5 py-3.5 text-center">Jumlah Program</th>
                            <th class="px-5 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($businesses as $group)
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-ink">{{ $group['businessName'] }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">Pemilik: {{ $group['ownerName'] }}</div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        @forelse ($group['recommendationCategories'] as $catName)
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-800 border border-amber-200/70">
                                                {{ $catName }}
                                            </span>
                                        @empty
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600">
                                                Belum teridentifikasi
                                            </span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $group['totalRecommendations'] }} Program
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-center">
                                    <button
                                        type="button"
                                        onclick="openDetailModal('modal-detail-{{ $group['businessId'] }}')"
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-brand/20 bg-brand/5 px-3.5 py-1.5 text-xs font-semibold text-brand hover:bg-brand hover:text-white transition shadow-2xs"
                                    >
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="mx-auto max-w-sm">
                                        <p class="text-base font-semibold text-ink">Data tidak ditemukan</p>
                                        <p class="mt-1 text-sm text-slate-500">
                                            @if ($search !== '' || $selectedCategoryId || $selectedMinat)
                                                Tidak ada rekomendasi program yang sesuai dengan kriteria filter atau pencarian Anda.
                                            @else
                                                Belum ada rekomendasi program yang aktif saat ini.
                                            @endif
                                        </p>
                                        @if ($search !== '' || $selectedCategoryId || $selectedMinat)
                                            <div class="mt-4">
                                                <a
                                                    href="{{ request()->url() }}"
                                                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                                                >
                                                    Reset Filter
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modals Detail Rekomendasi UMKM (1 modal per UMKM pada halaman aktif) --}}
        @foreach ($businesses as $group)
            <div
                id="modal-detail-{{ $group['businessId'] }}"
                class="detail-modal fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-xs p-4 sm:p-6"
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title-{{ $group['businessId'] }}"
            >
                <div class="flex min-h-full items-center justify-center">
                    <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl border border-slate-200 transition-all overflow-hidden">
                        {{-- Modal Header --}}
                        <div class="flex items-start justify-between border-b border-border bg-slate-50/75 px-6 py-4">
                            <div>
                                <h3 id="modal-title-{{ $group['businessId'] }}" class="text-lg font-bold text-ink">
                                    Detail Rekomendasi Program
                                </h3>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                    <span class="font-semibold text-slate-800">{{ $group['businessName'] }}</span>
                                    <span>•</span>
                                    <span>Pemilik: <strong class="text-slate-700 font-medium">{{ $group['ownerName'] }}</strong></span>
                                    <span>•</span>
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 font-medium text-amber-800 border border-amber-200/70">
                                        Kendala: {{ $group['categoryLabel'] }}
                                    </span>
                                </div>
                            </div>
                            <button
                                type="button"
                                onclick="closeDetailModal('modal-detail-{{ $group['businessId'] }}')"
                                class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-200 hover:text-slate-700 transition"
                                aria-label="Tutup modal"
                            >
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body: Daftar Program Rekomendasi --}}
                        <div class="p-6">
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Daftar Program Rekomendasi ({{ $group['totalRecommendations'] }})
                                </p>
                                <span class="text-xs text-slate-500">
                                    {{ $group['appliedCount'] }} Diminati
                                </span>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-border">
                                <table class="w-full text-left text-sm text-ink">
                                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-border">
                                        <tr>
                                            <th class="px-4 py-3">Nama Program</th>
                                            <th class="px-4 py-3">Kategori Kendala</th>
                                            <th class="px-4 py-3">Penyelenggara</th>
                                            <th class="px-4 py-3 text-center">Status Minat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-border">
                                        @foreach ($group['recommendations'] as $recommendation)
                                            <tr class="hover:bg-slate-50/50 transition">
                                                <td class="px-4 py-3">
                                                    <div class="font-bold text-ink">
                                                        {{ $recommendation->assistanceProgram?->name ?? 'Program tanpa nama' }}
                                                    </div>
                                                    @if ($recommendation->assistanceProgram?->description)
                                                        <p class="text-xs text-slate-500 line-clamp-2 mt-0.5">
                                                            {{ $recommendation->assistanceProgram->description }}
                                                        </p>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs">
                                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 border border-slate-200">
                                                        {{ $recommendation->obstacleCategory?->name ?? 'Belum teridentifikasi' }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-slate-600 text-xs">
                                                    {{ $recommendation->assistanceProgram?->provider ?: 'Belum diisi' }}
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                                    @if ($recommendation->applied_at)
                                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                                            <span class="size-1.5 rounded-full bg-emerald-600"></span>
                                                            Sudah mendaftar
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600 border border-slate-200">
                                                            <span class="size-1.5 rounded-full bg-slate-400"></span>
                                                            Belum mendaftar
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end border-t border-border bg-slate-50 px-6 py-3.5">
                            <button
                                type="button"
                                onclick="closeDetailModal('modal-detail-{{ $group['businessId'] }}')"
                                class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Pagination Berdasarkan Jumlah UMKM --}}
        @if ($businesses->hasPages())
            <div class="rounded-2xl border border-border bg-white px-4 py-3 shadow-xs">
                {{ $businesses->links() }}
            </div>
        @endif
    </div>

    <script>
        function openDetailModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }
        }

        function closeDetailModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.detail-modal:not(.hidden)').forEach(function(modal) {
                    modal.classList.add('hidden');
                });
                document.body.classList.remove('overflow-hidden');
            }
        });

        document.querySelectorAll('.detail-modal').forEach(function(modal) {
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                }
            });
        });
    </script>
@endsection

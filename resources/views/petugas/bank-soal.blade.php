@extends('layouts.app-shell')

@section('title', 'Bank Soal — TUMBUH UMKM')

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
                <div class="font-semibold mb-1">Terdapat kesalahan pengisian formulir:</div>
                <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header Section --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-brand transition-colors mb-2">
                    <x-hugeicon name="ArrowLeft02Icon" :size="16" />
                    Kembali ke Dashboard
                </a>
                <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Bank Soal Kuesioner</h1>
                <p class="mt-1 text-sm text-slate-600 max-w-3xl">
                    Kelola instrumen pertanyaan Likert dan opsi jawaban kuesioner asesmen kendala usaha UMKM yang digunakan untuk penilaian skor dan rekomendasi program.
                </p>
            </div>
            <div class="shrink-0 flex items-center gap-2">
                <a
                    href="{{ route('petugas.kategori-kendala.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-colors"
                >
                    <x-hugeicon name="Tag01Icon" :size="17" />
                    Kategori Kendala
                </a>
                <button
                    type="button"
                    onclick="openCreateModal()"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah Pertanyaan
                </button>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Total Soal</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-slate-100 text-slate-600">
                        <x-hugeicon name="CheckListIcon" :size="18" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-ink">{{ $stats['total'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Seluruh instrumen</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Soal Aktif</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                            <path d="M20 6 9 17l-5-5"/>
                        </svg>
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-emerald-700">{{ $stats['active'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Tampil di kuesioner UMKM</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Skala Likert</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-brand-50 text-brand">
                        <x-hugeicon name="Analytics01Icon" :size="18" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-brand">{{ $stats['likert'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">5 skala (0 - 100)</div>
            </div>

            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold tracking-wider text-slate-400 uppercase">Pilihan Ganda</span>
                    <span class="grid size-9 place-items-center rounded-xl bg-amber-50 text-amber-600">
                        <x-hugeicon name="CheckListIcon" :size="18" />
                    </span>
                </div>
                <div class="mt-2 text-2xl font-bold text-amber-700">{{ $stats['single_choice'] }}</div>
                <div class="text-xs text-slate-500 mt-0.5">Pilihan tunggal pendukung</div>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="rounded-2xl border border-slate-200/80 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('petugas.bank-soal.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('petugas.bank-soal.index', array_filter(['type' => $selectedType, 'status' => $selectedStatus, 'q' => $searchQuery])) }}"
                        class="rounded-xl px-3 py-1.5 text-xs font-semibold transition-colors {{ empty($selectedCategory) ? 'bg-brand text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                    >
                        Semua Kategori
                    </a>
                    @foreach($categories as $cat)
                        <a
                            href="{{ route('petugas.bank-soal.index', array_filter(['category_id' => $cat->id, 'type' => $selectedType, 'status' => $selectedStatus, 'q' => $searchQuery])) }}"
                            class="rounded-xl px-3 py-1.5 text-xs font-semibold transition-colors {{ (string)$selectedCategory === (string)$cat->id ? 'bg-brand text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                        >
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-2 lg:pt-0 border-t border-slate-100 lg:border-t-0">
                    <input type="hidden" name="category_id" value="{{ $selectedCategory }}">

                    <select
                        name="type"
                        onchange="this.form.submit()"
                        class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 bg-white focus:border-brand focus:outline-none"
                    >
                        <option value="">Semua Tipe</option>
                        <option value="likert" {{ $selectedType === 'likert' ? 'selected' : '' }}>Likert (5 Skala)</option>
                        <option value="single_choice" {{ $selectedType === 'single_choice' ? 'selected' : '' }}>Pilihan Tunggal</option>
                    </select>

                    <select
                        name="status"
                        onchange="this.form.submit()"
                        class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 bg-white focus:border-brand focus:outline-none"
                    >
                        <option value="">Semua Status</option>
                        <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="inactive" {{ $selectedStatus === 'inactive' ? 'selected' : '' }}>Hanya Nonaktif</option>
                    </select>

                    <div class="relative flex-1 sm:w-64">
                        <input
                            type="search"
                            name="q"
                            value="{{ $searchQuery }}"
                            placeholder="Cari teks pertanyaan..."
                            class="w-full rounded-xl border border-slate-200 pl-8 pr-3 py-1.5 text-xs text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none"
                        >
                        <svg class="absolute left-2.5 top-2 text-slate-400" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                    </div>

                    @if($selectedCategory || $selectedType || $selectedStatus || $searchQuery)
                        <a
                            href="{{ route('petugas.bank-soal.index') }}"
                            class="text-xs font-medium text-rose-600 hover:underline px-1"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Section --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-bold tracking-wider text-slate-600 uppercase">
                            <th class="py-3.5 pl-6 pr-3 w-12 text-center">No.</th>
                            <th class="py-3.5 px-4 min-w-[320px]">Pertanyaan &amp; Bantuan</th>
                            <th class="py-3.5 px-4">Kategori</th>
                            <th class="py-3.5 px-4 text-center w-32">Tipe</th>
                            <th class="py-3.5 px-3 text-center w-20">Bobot</th>
                            <th class="py-3.5 px-4 whitespace-nowrap min-w-[190px]">Opsi Jawaban</th>
                            <th class="py-3.5 px-3 text-center w-20">Urutan</th>
                            <th class="py-3.5 px-4 text-center w-28">Status</th>
                            <th class="py-3.5 pl-3 pr-6 text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($questions as $index => $question)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                {{-- Nomor --}}
                                <td class="py-3.5 pl-6 pr-3 text-center align-middle font-semibold text-slate-500">
                                    {{ $index + 1 }}
                                </td>

                                {{-- Pertanyaan & Bantuan --}}
                                <td class="py-3.5 px-4 align-middle">
                                    <div class="min-w-[320px] max-w-lg">
                                        <div class="font-bold text-ink leading-snug text-sm">
                                            {{ $question->prompt }}
                                        </div>
                                        @if(filled($question->help_text))
                                            <p class="mt-1 text-xs italic text-slate-500 leading-relaxed line-clamp-2" title="{{ $question->help_text }}">
                                                <span class="font-semibold text-slate-400 not-italic">Bantuan:</span> {{ $question->help_text }}
                                            </p>
                                        @endif
                                        @if($question->answers_count > 0)
                                            <div class="mt-1">
                                                <span class="inline-flex items-center gap-1 rounded bg-amber-50 px-1.5 py-0.5 text-[10.5px] font-semibold text-amber-800 border border-amber-200/60">
                                                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                                                    {{ $question->answers_count }} jawaban asesmen UMKM tercatat
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kategori --}}
                                <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100/90 px-2.5 py-1 text-xs font-semibold text-slate-700 border border-slate-200/60">
                                        <x-hugeicon name="Tag01Icon" :size="13" class="text-slate-400" />
                                        {{ $question->obstacleCategory->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Tipe Soal --}}
                                <td class="py-3.5 px-4 text-center align-middle whitespace-nowrap">
                                    @if($question->type === 'likert')
                                        <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-semibold text-brand border border-brand-200/80">
                                            <span class="size-1.5 rounded-full bg-brand"></span>
                                            Likert (5 Skala)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 border border-amber-200/80">
                                            <span class="size-1.5 rounded-full bg-amber-500"></span>
                                            Pilihan Tunggal
                                        </span>
                                    @endif
                                </td>

                                {{-- Bobot --}}
                                <td class="py-3.5 px-3 text-center align-middle whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-sm">
                                        {{ number_format((float)$question->weight, 2) }}
                                    </div>
                                    @if($question->is_reverse_scored)
                                        <span class="inline-block mt-0.5 text-[10px] font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-200" title="Skor terbalik (100 - nilai opsi)">
                                            Reverse
                                        </span>
                                    @endif
                                </td>

                                {{-- Opsi Jawaban --}}
                                <td class="py-3.5 px-4 align-middle whitespace-nowrap">
                                    <div class="space-y-1.5">
                                        @if($question->type === 'likert')
                                            <div class="inline-flex items-center gap-1.5 rounded-md bg-slate-50 border border-slate-200/60 px-2 py-0.5 text-[11px] text-slate-600 whitespace-nowrap">
                                                <span class="font-medium text-slate-500">Skor:</span>
                                                <span class="font-bold text-slate-800">0</span>
                                                <span class="text-slate-400">&ndash;</span>
                                                <span class="font-bold text-brand">100</span>
                                                <span class="text-slate-400 font-normal">({{ $question->options->count() }} skala)</span>
                                            </div>
                                        @else
                                            <div class="space-y-0.5">
                                                <div class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 border border-amber-200/60 px-2 py-0.5 text-[11px] text-amber-800 font-semibold whitespace-nowrap">
                                                    <span>{{ $question->options->count() }} Opsi</span>
                                                    @if($question->options->isNotEmpty())
                                                        <span class="text-amber-500 font-normal">&bull;</span>
                                                        <span class="font-normal">Skor {{ $question->options->min('score') ?? 0 }}&ndash;{{ $question->options->max('score') ?? 100 }}</span>
                                                    @endif
                                                </div>
                                                @if($question->options->isNotEmpty())
                                                    <div class="text-[11px] text-slate-500 truncate max-w-[210px] pt-0.5" title="{{ $question->options->pluck('label')->join(', ') }}">
                                                        {{ $question->options->pluck('label')->take(2)->join(', ') }}{{ $question->options->count() > 2 ? '...' : '' }}
                                                    </div>
                                                @endif
                                            </div>
                                        @endif

                                        <div>
                                            <button
                                                type="button"
                                                data-question-id="{{ $question->id }}"
                                                data-question-prompt="{{ $question->prompt }}"
                                                onclick="openOptionsModal(this)"
                                                class="text-xs font-semibold text-brand hover:underline inline-flex items-center gap-1.5 whitespace-nowrap transition-colors"
                                            >
                                                <x-hugeicon name="CheckListIcon" :size="13" class="shrink-0" />
                                                Kelola Opsi Jawaban
                                            </button>
                                        </div>
                                        <script type="application/json" id="options-data-{{ $question->id }}">@json($question->options)</script>
                                    </div>
                                </td>

                                {{-- Urutan --}}
                                <td class="py-3.5 px-3 text-center align-middle font-bold text-slate-700 whitespace-nowrap">
                                    {{ $question->sort_order }}
                                </td>

                                {{-- Status --}}
                                <td class="py-3.5 px-4 text-center align-middle whitespace-nowrap">
                                    <form method="POST" action="{{ route('petugas.bank-soal.toggle', $question) }}" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            title="Klik untuk mengubah status aktif/nonaktif"
                                            class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold border transition-colors cursor-pointer {{ $question->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-slate-200' }}"
                                        >
                                            <span class="size-1.5 rounded-full {{ $question->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            {{ $question->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>

                                {{-- Aksi --}}
                                <td class="py-3.5 pl-3 pr-6 text-center align-middle whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Edit Button --}}
                                        <button
                                            type="button"
                                            data-question-id="{{ $question->id }}"
                                            data-question-category="{{ $question->obstacle_category_id }}"
                                            data-question-type="{{ $question->type }}"
                                            data-question-prompt="{{ $question->prompt }}"
                                            data-question-help="{{ $question->help_text ?? '' }}"
                                            data-question-weight="{{ $question->weight }}"
                                            data-question-reverse="{{ $question->is_reverse_scored ? '1' : '0' }}"
                                            data-question-sort="{{ $question->sort_order }}"
                                            data-question-active="{{ $question->is_active ? '1' : '0' }}"
                                            data-answers-count="{{ $question->answers_count }}"
                                            onclick="openEditModal(this)"
                                            title="Edit Pertanyaan"
                                            aria-label="Edit Pertanyaan"
                                            class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:border-brand hover:bg-brand-50 hover:text-brand transition-colors"
                                        >
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                                <path d="m15 5 4 4"/>
                                            </svg>
                                        </button>

                                        {{-- Delete Button --}}
                                        @if($question->answers_count === 0)
                                            <form
                                                method="POST"
                                                action="{{ route('petugas.bank-soal.destroy', $question) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini beserta seluruh opsi jawabannya?');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    title="Hapus Pertanyaan"
                                                    aria-label="Hapus Pertanyaan"
                                                    class="grid size-8 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:border-rose-400 hover:bg-rose-50 hover:text-rose-700 transition-colors"
                                                >
                                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                        <path d="M3 6h18"/>
                                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                        <line x1="10" y1="11" x2="10" y2="17"/>
                                                        <line x1="14" y1="11" x2="14" y2="17"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <button
                                                type="button"
                                                disabled
                                                title="Pertanyaan tidak dapat dihapus karena sudah memiliki riwayat jawaban asesmen UMKM. Silakan nonaktifkan status pertanyaan."
                                                aria-label="Pertanyaan tidak dapat dihapus"
                                                class="grid size-8 place-items-center rounded-lg border border-slate-100 bg-slate-50 text-slate-300 cursor-not-allowed"
                                            >
                                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M3 6h18"/>
                                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                                    <line x1="10" y1="11" x2="10" y2="17"/>
                                                    <line x1="14" y1="11" x2="14" y2="17"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <span class="grid size-12 place-items-center rounded-full bg-slate-100 text-slate-400 mb-2">
                                            <x-hugeicon name="CheckListIcon" :size="24" />
                                        </span>
                                        <p class="font-medium text-slate-600">Belum ada pertanyaan pada Bank Soal</p>
                                        <p class="text-xs text-slate-400 mt-0.5">Klik tombol Tambah Pertanyaan di atas atau jalankan seeder untuk mengisi pertanyaan awal.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pertanyaan --}}
    <div id="create-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-ink">Tambah Pertanyaan Bank Soal</h2>
                    <button type="button" onclick="closeCreateModal()" class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <x-hugeicon name="Cancel01Icon" :size="18" />
                    </button>
                </div>

                <form method="POST" action="{{ route('petugas.bank-soal.store') }}" class="mt-4 space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create_category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Kategori Kendala <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="create_category_id"
                                name="obstacle_category_id"
                                required
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                                <option value="">Pilih Kategori Kendala</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ (string)$selectedCategory === (string)$category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="create_type" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tipe Pertanyaan <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="create_type"
                                name="type"
                                required
                                onchange="toggleCreateType(this.value)"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                                <option value="likert">Likert (5 Skala: 0 - 100%)</option>
                                <option value="single_choice">Pilihan Tunggal (Kustom)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="create_prompt" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Teks Pertanyaan / Pernyataan <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="create_prompt"
                            name="prompt"
                            required
                            rows="3"
                            placeholder="Contoh: Saya kesulitan menyediakan modal untuk mengembangkan usaha."
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        ></textarea>
                    </div>

                    <div>
                        <label for="create_help_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Teks Bantuan / Penjelasan Tambahan
                        </label>
                        <input
                            type="text"
                            id="create_help_text"
                            name="help_text"
                            placeholder="Panduan bagi UMKM dalam memahami pertanyaan ini (opsional)"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create_weight" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Bobot Pertanyaan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="create_weight"
                                name="weight"
                                required
                                min="0"
                                max="99.99"
                                step="0.01"
                                value="1.00"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                            <p class="text-[11px] text-slate-500 mt-0.5">Standar bernilai 1.00 pada rumus skor kategori.</p>
                        </div>

                        <div>
                            <label for="create_sort_order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Tampilan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="create_sort_order"
                                name="sort_order"
                                required
                                min="0"
                                value="{{ $questions->count() + 1 }}"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-1">
                        <div>
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_reverse_scored" value="0">
                                <input
                                    type="checkbox"
                                    name="is_reverse_scored"
                                    value="1"
                                    class="size-4 rounded text-brand focus:ring-brand border-slate-300"
                                >
                                <div>
                                    <span class="text-sm font-semibold text-slate-700">Skor Terbalik (Reverse)</span>
                                    <p class="text-[11px] text-slate-500">Gunakan jika pernyataan positif (100 - nilai opsi).</p>
                                </div>
                            </label>
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input
                                    type="checkbox"
                                    name="is_active"
                                    value="1"
                                    checked
                                    class="size-4 rounded text-brand focus:ring-brand border-slate-300"
                                >
                                <span class="text-sm font-semibold text-slate-700">Status Aktif</span>
                            </label>
                        </div>
                    </div>

                    {{-- Notice Likert vs Single Choice --}}
                    <div id="likert-note" class="rounded-xl bg-brand-50 border border-brand-200 p-3 text-xs text-brand-900 leading-relaxed">
                        <span class="font-bold">Info Opsi Likert:</span> Pertanyaan Likert akan otomatis dibuat dengan 5 skala jawaban standar: <em>Sangat tidak setuju (0)</em>, <em>Tidak setuju (25)</em>, <em>Netral (50)</em>, <em>Setuju (75)</em>, dan <em>Sangat setuju (100)</em>.
                    </div>

                    <div id="single-choice-options-builder" class="hidden space-y-2 border-t border-slate-100 pt-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Opsi Jawaban Pilihan Tunggal</span>
                            <button type="button" onclick="addSingleChoiceRow()" class="text-xs font-semibold text-brand hover:underline">
                                + Tambah Opsi
                            </button>
                        </div>
                        <div id="single-choice-rows" class="space-y-2">
                            <div class="flex items-center gap-2 single-choice-row">
                                <input type="text" name="options[0][label]" placeholder="Label Opsi 1" disabled class="flex-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
                                <input type="number" name="options[0][score]" placeholder="Skor (0-100)" min="0" max="100" disabled class="w-24 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
                                <input type="number" name="options[0][value]" placeholder="Value" min="1" value="1" disabled class="w-16 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
                            </div>
                            <div class="flex items-center gap-2 single-choice-row">
                                <input type="text" name="options[1][label]" placeholder="Label Opsi 2" disabled class="flex-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
                                <input type="number" name="options[1][score]" placeholder="Skor (0-100)" min="0" max="100" disabled class="w-24 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
                                <input type="number" name="options[1][value]" placeholder="Value" min="1" value="2" disabled class="w-16 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            onclick="closeCreateModal()"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                        >
                            Simpan Pertanyaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Pertanyaan --}}
    <div id="edit-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <div class="w-full max-w-xl rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-bold text-ink">Edit Pertanyaan</h2>
                    <button type="button" onclick="closeEditModal()" class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <x-hugeicon name="Cancel01Icon" :size="18" />
                    </button>
                </div>

                <form id="edit-form" method="POST" action="" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Kategori Kendala <span class="text-rose-500">*</span>
                            </label>
                            <select
                                id="edit_category_id"
                                name="obstacle_category_id"
                                required
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Tipe Pertanyaan
                            </label>
                            <input
                                type="text"
                                id="edit_type_display"
                                readonly
                                disabled
                                class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed font-medium"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="edit_prompt" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Teks Pertanyaan / Pernyataan <span class="text-rose-500">*</span>
                        </label>
                        <textarea
                            id="edit_prompt"
                            name="prompt"
                            required
                            rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        ></textarea>
                    </div>

                    <div>
                        <label for="edit_help_text" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Teks Bantuan / Penjelasan Tambahan
                        </label>
                        <input
                            type="text"
                            id="edit_help_text"
                            name="help_text"
                            class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                        >
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit_weight" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Bobot Pertanyaan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="edit_weight"
                                name="weight"
                                required
                                min="0"
                                max="99.99"
                                step="0.01"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>

                        <div>
                            <label for="edit_sort_order" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                Urutan Tampilan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="edit_sort_order"
                                name="sort_order"
                                required
                                min="0"
                                class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/10"
                            >
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center pt-1">
                        <div>
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_reverse_scored" value="0">
                                <input
                                    type="checkbox"
                                    id="edit_is_reverse_scored"
                                    name="is_reverse_scored"
                                    value="1"
                                    class="size-4 rounded text-brand focus:ring-brand border-slate-300"
                                >
                                <span class="text-sm font-semibold text-slate-700">Skor Terbalik (Reverse)</span>
                            </label>
                        </div>

                        <div>
                            <label class="inline-flex items-center gap-2.5 cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input
                                    type="checkbox"
                                    id="edit_is_active"
                                    name="is_active"
                                    value="1"
                                    class="size-4 rounded text-brand focus:ring-brand border-slate-300"
                                >
                                <span class="text-sm font-semibold text-slate-700">Status Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div id="edit-locked-notice" class="hidden rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-900 leading-relaxed">
                        Pertanyaan ini sudah memiliki riwayat asesmen UMKM. Kategori kendala dan tipe soal dikunci demi menjaga integritas data riwayat.
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                        <button
                            type="button"
                            onclick="closeEditModal()"
                            class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                        >
                            Perbarui Pertanyaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Kelola Opsi Jawaban --}}
    <div id="options-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/50 backdrop-blur-sm p-4 sm:p-6" role="dialog" aria-modal="true">
        <div class="flex min-h-full items-center justify-center">
            <div class="w-full max-w-2xl rounded-2xl bg-white p-6 shadow-xl border border-slate-200 transition-all">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-bold text-ink">Kelola Opsi Jawaban</h2>
                        <p id="options-modal-prompt" class="text-xs text-slate-500 mt-0.5 line-clamp-1"></p>
                    </div>
                    <button type="button" onclick="closeOptionsModal()" class="grid size-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600">
                        <x-hugeicon name="Cancel01Icon" :size="18" />
                    </button>
                </div>

                <div class="mt-4 space-y-4">
                    {{-- Daftar Opsi --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-200">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 text-slate-500 font-bold uppercase tracking-wider border-b border-slate-200">
                                    <th class="py-2.5 px-3 text-center w-12">Urutan</th>
                                    <th class="py-2.5 px-3">Label Opsi</th>
                                    <th class="py-2.5 px-3 text-center w-20">Value</th>
                                    <th class="py-2.5 px-3 text-center w-24">Skor (0-100)</th>
                                    <th class="py-2.5 px-3 text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="options-modal-table-body" class="divide-y divide-slate-100">
                                {{-- Diisi via JavaScript --}}
                            </tbody>
                        </table>
                    </div>

                    {{-- Form Tambah Opsi Baru --}}
                    <div id="add-option-box" class="rounded-xl border border-dashed border-slate-300 p-4 bg-slate-50/50">
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tambah Opsi Baru</h3>
                        <form id="add-option-form" method="POST" action="">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                                <div class="sm:col-span-2">
                                    <input type="text" name="label" required placeholder="Label opsi (misal: Sering kali)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <input type="number" name="score" required min="0" max="100" placeholder="Skor (0-100)" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <input type="number" name="sort_order" required min="1" placeholder="Urutan" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white">
                                </div>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="submit" class="rounded-xl bg-brand px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-brand-hover">
                                    + Simpan Opsi
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Form Edit Opsi (Tersembunyi secara default) --}}
                    <div id="edit-option-box" class="hidden rounded-xl border border-brand-200 p-4 bg-brand-50/50">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-xs font-bold text-brand uppercase tracking-wider">Perbarui Opsi Jawaban</h3>
                            <button type="button" onclick="cancelEditOption()" class="text-xs text-slate-500 hover:underline">Batal</button>
                        </div>
                        <form id="edit-option-form" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                                <div class="sm:col-span-2">
                                    <input type="text" id="edit_opt_label" name="label" required placeholder="Label opsi" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <input type="number" id="edit_opt_score" name="score" required min="0" max="100" placeholder="Skor" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white">
                                </div>
                                <div>
                                    <input type="number" id="edit_opt_sort" name="sort_order" required min="1" placeholder="Urutan" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs bg-white">
                                </div>
                            </div>
                            <div class="mt-2 flex justify-end">
                                <button type="submit" class="rounded-xl bg-brand px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-brand-hover">
                                    Perbarui Opsi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-4 mt-4">
                    <button type="button" onclick="closeOptionsModal()" class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let singleChoiceCounter = 2;

    function openCreateModal() {
        const typeSelect = document.getElementById('create_type');
        toggleCreateType(typeSelect ? typeSelect.value : 'likert');
        document.getElementById('create-modal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }

    function toggleCreateType(type) {
        const likertNote = document.getElementById('likert-note');
        const builder = document.getElementById('single-choice-options-builder');
        const inputs = builder ? builder.querySelectorAll('input') : [];
        if (type === 'likert') {
            likertNote.classList.remove('hidden');
            builder.classList.add('hidden');
            inputs.forEach(input => input.disabled = true);
        } else {
            likertNote.classList.add('hidden');
            builder.classList.remove('hidden');
            inputs.forEach(input => input.disabled = false);
        }
    }

    function addSingleChoiceRow() {
        const container = document.getElementById('single-choice-rows');
        const index = singleChoiceCounter++;
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 single-choice-row';
        row.innerHTML = `
            <input type="text" name="options[${index}][label]" placeholder="Label Opsi ${index + 1}" required class="flex-1 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
            <input type="number" name="options[${index}][score]" placeholder="Skor (0-100)" min="0" max="100" required class="w-24 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
            <input type="number" name="options[${index}][value]" placeholder="Value" min="1" value="${index + 1}" class="w-16 rounded-xl border border-slate-200 px-3 py-1.5 text-xs">
        `;
        container.appendChild(row);
    }

    function openEditModal(button) {
        const id = button.getAttribute('data-question-id');
        const form = document.getElementById('edit-form');
        form.action = `/petugas/bank-soal/${id}`;

        document.getElementById('edit_category_id').value = button.getAttribute('data-question-category') || '';
        const type = button.getAttribute('data-question-type') || 'likert';
        document.getElementById('edit_type_display').value = type === 'likert' ? 'Likert (5 Skala)' : 'Pilihan Tunggal';
        document.getElementById('edit_prompt').value = button.getAttribute('data-question-prompt') || '';
        document.getElementById('edit_help_text').value = button.getAttribute('data-question-help') || '';
        document.getElementById('edit_weight').value = button.getAttribute('data-question-weight') || '1.00';
        document.getElementById('edit_sort_order').value = button.getAttribute('data-question-sort') || '0';
        document.getElementById('edit_is_reverse_scored').checked = button.getAttribute('data-question-reverse') === '1';
        document.getElementById('edit_is_active').checked = button.getAttribute('data-question-active') === '1';

        const answersCount = parseInt(button.getAttribute('data-answers-count') || '0', 10);
        const lockedNotice = document.getElementById('edit-locked-notice');
        const categorySelect = document.getElementById('edit_category_id');
        const reverseCheckbox = document.getElementById('edit_is_reverse_scored');

        if (answersCount > 0) {
            lockedNotice.classList.remove('hidden');
            categorySelect.disabled = true;
            reverseCheckbox.disabled = true;
        } else {
            lockedNotice.classList.add('hidden');
            categorySelect.disabled = false;
            reverseCheckbox.disabled = false;
        }

        document.getElementById('edit-modal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('edit-modal').classList.add('hidden');
    }

    function openOptionsModal(button) {
        let questionId, questionPrompt, options;
        if (button instanceof HTMLElement) {
            questionId = button.getAttribute('data-question-id');
            questionPrompt = button.getAttribute('data-question-prompt');
            const dataScript = document.getElementById(`options-data-${questionId}`);
            options = dataScript ? JSON.parse(dataScript.textContent || '[]') : [];
        } else {
            questionId = button.id;
            questionPrompt = button.prompt;
            options = arguments[1] || [];
        }

        document.getElementById('options-modal-prompt').textContent = questionPrompt;
        const addForm = document.getElementById('add-option-form');
        addForm.action = `/petugas/bank-soal/${questionId}/options`;

        const tbody = document.getElementById('options-modal-table-body');
        tbody.innerHTML = '';

        options.sort((a, b) => a.sort_order - b.sort_order);

        options.forEach(opt => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50 transition-colors';
            tr.innerHTML = `
                <td class="py-2.5 px-3 text-center font-bold text-slate-400">${opt.sort_order}</td>
                <td class="py-2.5 px-3 font-semibold text-ink">${opt.label}</td>
                <td class="py-2.5 px-3 text-center font-mono text-slate-500">${opt.value ?? '-'}</td>
                <td class="py-2.5 px-3 text-center">
                    <span class="inline-block rounded px-2 py-0.5 font-bold ${opt.score >= 70 ? 'bg-rose-50 text-rose-700' : (opt.score >= 40 ? 'bg-amber-50 text-amber-700' : 'bg-emerald-50 text-emerald-700')}">
                        ${opt.score}
                    </span>
                </td>
                <td class="py-2.5 px-3 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <button
                            type="button"
                            class="edit-opt-btn grid size-6 place-items-center rounded text-slate-600 hover:text-brand"
                            title="Edit Opsi"
                        >
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        </button>
                        <form method="POST" action="/petugas/bank-soal/options/${opt.id}" onsubmit="return confirm('Hapus opsi ini?');">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button
                                type="submit"
                                class="grid size-6 place-items-center rounded text-slate-600 hover:text-rose-600"
                                title="Hapus Opsi"
                            >
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            `;
            const editBtn = tr.querySelector('.edit-opt-btn');
            if (editBtn) {
                editBtn.addEventListener('click', () => startEditOption(opt));
            }
            tbody.appendChild(tr);
        });

        cancelEditOption();
        document.getElementById('options-modal').classList.remove('hidden');
    }

    function closeOptionsModal() {
        document.getElementById('options-modal').classList.add('hidden');
    }

    function startEditOption(opt) {
        document.getElementById('edit-option-box').classList.remove('hidden');
        document.getElementById('add-option-box').classList.add('hidden');

        const editForm = document.getElementById('edit-option-form');
        editForm.action = `/petugas/bank-soal/options/${opt.id}`;
        document.getElementById('edit_opt_label').value = opt.label;
        document.getElementById('edit_opt_score').value = opt.score;
        document.getElementById('edit_opt_sort').value = opt.sort_order;
    }

    function cancelEditOption() {
        document.getElementById('edit-option-box').classList.add('hidden');
        document.getElementById('add-option-box').classList.remove('hidden');
    }

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
            closeOptionsModal();
        }
    });
</script>
@endpush

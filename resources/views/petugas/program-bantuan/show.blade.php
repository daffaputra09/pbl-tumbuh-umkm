@extends('layouts.app-shell')

@section('title', $program->name . ' — Program Bantuan')

@section('content')
    <div class="space-y-6 max-w-5xl mx-auto">
        @if(session('success'))
            <div class="flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-emerald-600 shrink-0" aria-hidden="true">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <a href="{{ route('petugas.program-bantuan.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-brand transition-colors mb-2">
                    <x-hugeicon name="ArrowLeft02Icon" :size="16" />
                    Kembali ke Daftar Program
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">{{ $program->name }}</h1>
                    @php
                        $statusBadge = match($program->status) {
                            'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'draft' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'closed' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'archived' => 'bg-rose-50 text-rose-700 border-rose-200',
                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold border {{ $statusBadge }}">
                        @if($program->status === 'active')
                            <span class="size-2 rounded-full bg-emerald-500"></span>
                        @elseif($program->status === 'draft')
                            <span class="size-2 rounded-full bg-amber-500"></span>
                        @else
                            <span class="size-2 rounded-full bg-slate-400"></span>
                        @endif
                        {{ $program->statusLabel() }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-slate-600">Penyelenggara: <span class="font-semibold text-ink">{{ $program->provider }}</span></p>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a
                    href="{{ route('petugas.program-bantuan.edit', $program) }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-hover transition-colors"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                        <path d="m15 5 4 4"/>
                    </svg>
                    Edit Program
                </a>
                <button
                    type="button"
                    onclick="openDeleteModal('{{ $program->id }}', '{{ addslashes($program->name) }}')"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm font-semibold text-rose-600 hover:bg-rose-50 hover:border-rose-200 transition-colors"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- Kolom Kiri: Konten Utama --}}
            <div class="space-y-6 lg:col-span-2">
                {{-- Deskripsi Program --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold tracking-wider text-slate-400 uppercase">Deskripsi Program</h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $program->description }}
                    </div>
                </div>

                {{-- Persyaratan Program --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold tracking-wider text-slate-400 uppercase">Persyaratan Penerima</h2>
                    @if($program->requirements)
                        <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ $program->requirements }}
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic">Tidak ada persyaratan khusus yang dicantumkan.</p>
                    @endif
                </div>

                {{-- Kategori Kendala Terintegrasi --}}
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h2 class="text-base font-bold text-ink">Kategori Kendala Terhubung</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Program ini akan dipasangkan dengan kendala UMKM berikut pada rekomendasi program:</p>
                        </div>
                        <span class="rounded-full bg-teal-50 px-2.5 py-0.5 text-xs font-semibold text-teal-700 border border-teal-200">
                            {{ $program->obstacleCategories->count() }} Terhubung
                        </span>
                    </div>

                    <div class="space-y-3">
                        @forelse($program->obstacleCategories as $category)
                            <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-sm text-ink">{{ $category->name }}</span>
                                            <span class="rounded bg-slate-200/80 px-1.5 py-0.5 text-[10px] font-mono text-slate-600">
                                                {{ $category->slug }}
                                            </span>
                                        </div>
                                        @if($category->description)
                                            <p class="text-xs text-slate-500 mt-1">{{ $category->description }}</p>
                                        @endif
                                    </div>

                                    <div class="text-right shrink-0">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $category->pivot->minimum_level === 'high' ? 'bg-amber-100 text-amber-800' : 'bg-teal-100 text-teal-800' }}">
                                            Tingkat Min: {{ $category->pivot->minimum_level === 'high' ? 'Tinggi' : 'Sedang' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-rose-200 bg-rose-50/40 p-4 text-center text-xs text-rose-600">
                                Program ini belum terhubung dengan kategori kendala manapun. Silakan klik tombol Edit Program untuk menghubungkannya.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan & Metadata --}}
            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm space-y-4">
                    <h2 class="text-sm font-bold tracking-wider text-slate-400 uppercase">Informasi Tambahan</h2>

                    <div class="space-y-3.5 divide-y divide-slate-100 text-xs">
                        <div class="pt-2">
                            <span class="text-slate-500 block mb-1">Kuota Program:</span>
                            <span class="text-sm font-semibold text-ink">
                                {{ $program->quota ? number_format($program->quota) . ' penerima' : 'Tidak dibatasi / Belum ditentukan' }}
                            </span>
                        </div>

                        <div class="pt-3">
                            <span class="text-slate-500 block mb-1">Masa Berlaku Program:</span>
                            <span class="text-sm font-semibold text-ink">
                                @if($program->starts_on || $program->ends_on)
                                    {{ $program->starts_on ? $program->starts_on->format('d M Y') : '—' }}
                                    s.d.
                                    {{ $program->ends_on ? $program->ends_on->format('d M Y') : '—' }}
                                @else
                                    Berkelanjutan
                                @endif
                            </span>
                        </div>

                        <div class="pt-3">
                            <span class="text-slate-500 block mb-1">Tautan Resmi:</span>
                            @if($program->url)
                                <a
                                    href="{{ $program->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="text-sm font-medium text-brand hover:underline inline-flex items-center gap-1.5 break-all"
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true">
                                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                                        <polyline points="15 3 21 3 21 9"/>
                                        <line x1="10" y1="14" x2="21" y2="3"/>
                                    </svg>
                                    Buka Informasi Pendaftaran
                                </a>
                            @else
                                <span class="text-slate-400 italic">Tidak ada tautan terdaftar</span>
                            @endif
                        </div>

                        <div class="pt-3">
                            <span class="text-slate-500 block mb-1">Dibuat oleh:</span>
                            <div class="flex items-center gap-2">
                                <span class="grid size-6 place-items-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-700">
                                    {{ $program->createdBy ? $program->createdBy->initials() : '—' }}
                                </span>
                                <div>
                                    <span class="font-semibold text-ink block text-xs">{{ $program->createdBy?->name ?? 'Sistem' }}</span>
                                    <span class="text-[10px] text-slate-400">{{ $program->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        @if($program->updated_at->gt($program->created_at))
                            <div class="pt-3">
                                <span class="text-slate-500 block mb-1">Terakhir diperbarui:</span>
                                <span class="text-slate-600">{{ $program->updated_at->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Konfirmasi Hapus --}}
    <div id="deleteModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl space-y-4">
            <div class="flex items-center gap-3">
                <div class="grid size-10 place-items-center rounded-xl bg-rose-100 text-rose-600 shrink-0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-ink">Hapus Program Bantuan</h3>
                    <p class="text-xs text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <p class="text-sm text-slate-600">
                Apakah Anda yakin ingin menghapus program <span id="deleteProgramName" class="font-semibold text-ink"></span>? Hubungan dengan kategori kendala juga akan dilepas.
            </p>

            <form id="deleteForm" method="POST" action="" class="flex items-center justify-end gap-2.5 pt-2">
                @csrf
                @method('DELETE')
                <button
                    type="button"
                    onclick="closeDeleteModal()"
                    class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition-colors"
                >
                    Hapus Program
                </button>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openDeleteModal(id, name) {
                const modal = document.getElementById('deleteModal');
                const form = document.getElementById('deleteForm');
                const nameSpan = document.getElementById('deleteProgramName');

                form.action = `{{ url('/petugas/program-bantuan') }}/${id}`;
                nameSpan.textContent = `"${name}"`;
                modal.classList.remove('hidden');
            }

            function closeDeleteModal() {
                const modal = document.getElementById('deleteModal');
                modal.classList.add('hidden');
            }
        </script>
    @endpush
@endsection

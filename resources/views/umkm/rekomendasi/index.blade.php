@extends('layouts.app-shell')

@section('title', 'Rekomendasi Program — TUMBUH UMKM')

@section('content')
    <div class="mx-auto max-w-6xl space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Rekomendasi program</h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-600">
                Program bantuan dan pembinaan yang cocok dengan kendala usahamu. Daftarkan minat bila kamu ingin petugas desa menindaklanjutinya.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-border bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-slate-50 text-xs uppercase text-muted-foreground border-b border-border">
                        <tr>
                            <th class="px-5 py-3.5 font-semibold">Program</th>
                            <th class="px-5 py-3.5 font-semibold">Kategori kendala</th>
                            <th class="px-5 py-3.5 font-semibold">Syarat</th>
                            <th class="px-5 py-3.5 font-semibold">Periode program</th>
                            <th class="px-5 py-3.5 font-semibold">Minat</th>
                            <th class="px-5 py-3.5 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($recommendations as $recommendation)
                            @php
                                $program = $recommendation->assistanceProgram;
                                $startsOn = $program?->starts_on;
                                $endsOn = $program?->ends_on;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-5 py-4 align-top max-w-xs">
                                    <p class="font-semibold text-ink">{{ $program?->name ?? 'Program tanpa nama' }}</p>
                                    <p class="text-xs font-medium text-brand mt-0.5">{{ $program?->provider ?: 'Penyelenggara belum diisi' }}</p>
                                    @if (filled($program?->description))
                                        <p class="mt-1.5 text-xs leading-relaxed text-slate-600 line-clamp-2">{{ $program->description }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-top whitespace-nowrap">
                                    <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                        {{ $recommendation->obstacleCategory?->name ?? 'Belum teridentifikasi' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 align-top max-w-xs">
                                    @if (filled($program?->requirements))
                                        <p class="text-xs leading-relaxed text-slate-700 line-clamp-3">{{ $program->requirements }}</p>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Tidak ada syarat khusus</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-top whitespace-nowrap">
                                    @if ($startsOn || $endsOn)
                                        <div class="text-xs font-medium text-slate-700">
                                            {{ $startsOn ? $startsOn->translatedFormat('d M Y') : '—' }}
                                            <span class="text-slate-400">s.d.</span>
                                            {{ $endsOn ? $endsOn->translatedFormat('d M Y') : '—' }}
                                        </div>
                                    @else
                                        <span class="text-xs text-slate-500 italic">Berkelanjutan</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-top whitespace-nowrap">
                                    @if ($recommendation->applied_at)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 border border-emerald-200">
                                            <span class="size-1.5 rounded-full bg-emerald-600"></span>
                                            Sudah didaftarkan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">
                                            Belum didaftarkan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 align-top text-right whitespace-nowrap">
                                    <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end gap-2">
                                        <a href="{{ route('umkm.rekomendasi.show', $recommendation) }}" class="inline-flex items-center rounded-lg border border-border bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand transition-colors">
                                            Lihat detail
                                        </a>
                                        @unless ($recommendation->applied_at)
                                            <form method="post" action="{{ route('umkm.rekomendasi.apply', $recommendation) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center rounded-lg bg-brand px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-hover transition-colors">
                                                    Daftarkan minat
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="mx-auto max-w-md space-y-3">
                                        <div class="mx-auto grid size-12 place-items-center rounded-2xl bg-slate-100 text-slate-400">
                                            <x-hugeicon name="GiftIcon" :size="24" />
                                        </div>
                                        <div>
                                            <h3 class="text-base font-semibold text-ink">Belum ada rekomendasi program</h3>
                                            <p class="mt-1 text-sm text-slate-600 leading-relaxed">
                                                @if (! $hasCompletedAssessment)
                                                    Rekomendasi program bantuan dan pembinaan akan muncul setelah kamu menyelesaikan pengisian kuesioner kebutuhan dan kendala usaha.
                                                @else
                                                    Saat ini belum ada program bantuan aktif yang cocok dengan kategori kendala usahamu. Petugas desa akan menambahkan program baru secara berkala.
                                                @endif
                                            </p>
                                        </div>
                                        @if (! $hasCompletedAssessment)
                                            <div class="pt-2">
                                                <a href="{{ route('umkm.kebutuhan') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand px-4 py-2 text-xs font-semibold text-white hover:bg-brand-hover transition-colors">
                                                    <x-hugeicon name="CheckListIcon" :size="16" />
                                                    Isi Kebutuhan dan Kendala
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
    </div>
@endsection

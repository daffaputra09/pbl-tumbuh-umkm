@extends('layouts.app-shell')

@section('title', 'Rekomendasi — TUMBUH UMKM')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Rekomendasi</h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-600">
                Program yang cocok untuk setiap usaha, beserta minat yang sudah didaftarkan pelaku.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-border bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-slate-50 text-xs uppercase text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Usaha</th>
                            <th class="px-4 py-3 font-semibold">Kategori kendala</th>
                            <th class="px-4 py-3 font-semibold">Program</th>
                            <th class="px-4 py-3 font-semibold">Penyelenggara</th>
                            <th class="px-4 py-3 font-semibold">Minat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($recommendations as $recommendation)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $recommendation->business?->business_name ?? 'Usaha tanpa nama' }}</td>
                                <td class="px-4 py-3">{{ $recommendation->obstacleCategory?->name ?? 'Belum teridentifikasi' }}</td>
                                <td class="px-4 py-3">{{ $recommendation->assistanceProgram?->name ?? 'Program tanpa nama' }}</td>
                                <td class="px-4 py-3">{{ $recommendation->assistanceProgram?->provider ?: 'Belum diisi' }}</td>
                                <td class="px-4 py-3">
                                    @if ($recommendation->applied_at)
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">Sudah mendaftar</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">Belum mendaftar</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada rekomendasi program yang aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

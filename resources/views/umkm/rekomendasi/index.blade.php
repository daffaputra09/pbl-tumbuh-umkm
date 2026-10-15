@extends('layouts.app-shell')

@section('title', 'Rekomendasi Program — TUMBUH UMKM')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div>
            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">Rekomendasi program</h1>
            <p class="mt-1 max-w-2xl text-sm text-slate-600">
                Program yang cocok dengan kendala usahamu. Daftarkan minat bila kamu ingin petugas desa menindaklanjutinya.
            </p>
        </div>

        <div class="overflow-hidden rounded-2xl border border-border bg-white">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-ink">
                    <thead class="bg-slate-50 text-xs uppercase text-muted-foreground">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Program</th>
                            <th class="px-4 py-3 font-semibold">Kategori kendala</th>
                            <th class="px-4 py-3 font-semibold">Minat</th>
                            <th class="px-4 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse ($recommendations as $recommendation)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-semibold">{{ $recommendation->assistanceProgram?->name ?? 'Program tanpa nama' }}</p>
                                    <p class="text-xs text-slate-500">{{ $recommendation->assistanceProgram?->provider ?: 'Penyelenggara belum diisi' }}</p>
                                </td>
                                <td class="px-4 py-3">{{ $recommendation->obstacleCategory?->name ?? 'Belum teridentifikasi' }}</td>
                                <td class="px-4 py-3">
                                    @if ($recommendation->applied_at)
                                        <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-semibold text-emerald-700">Sudah didaftarkan</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600">Belum didaftarkan</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('umkm.rekomendasi.show', $recommendation) }}" class="text-sm font-semibold text-brand hover:text-brand-hover">Lihat detail</a>
                                        @unless ($recommendation->applied_at)
                                            <form method="post" action="{{ route('umkm.rekomendasi.apply', $recommendation) }}">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center rounded-xl bg-brand px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-hover">Daftarkan minat</button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-500">
                                    Belum ada program yang cocok. Isi kebutuhan dan kendala, atau tunggu petugas menambahkan program yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

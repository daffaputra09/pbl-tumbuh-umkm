@extends('layouts.app-shell')

@section('title', ($recommendation->assistanceProgram?->name ?? 'Program').' — Rekomendasi')

@section('content')
    <div class="mx-auto max-w-3xl space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                {{ session('success') }}
            </div>
        @endif

        <div>
            <a href="{{ route('umkm.rekomendasi') }}" class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-brand">
                <x-hugeicon name="ArrowLeft02Icon" :size="16" />
                Kembali ke daftar rekomendasi
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-ink sm:text-3xl">{{ $recommendation->assistanceProgram?->name ?? 'Program tanpa nama' }}</h1>
            <p class="mt-1 text-sm text-slate-600">Penyelenggara: <span class="font-semibold text-ink">{{ $recommendation->assistanceProgram?->provider ?: 'Belum diisi' }}</span></p>
        </div>

        <div class="space-y-5 rounded-2xl border border-border bg-white p-5 sm:p-6">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori kendala</p>
                <p class="mt-1 text-sm font-semibold text-ink">{{ $recommendation->obstacleCategory?->name ?? 'Belum teridentifikasi' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Penjelasan</p>
                <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $recommendation->assistanceProgram?->description ?: 'Belum ada penjelasan program.' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Syarat</p>
                <p class="mt-1 text-sm leading-relaxed text-slate-700">{{ $recommendation->assistanceProgram?->requirements ?: 'Belum ada syarat yang dicantumkan.' }}</p>
            </div>
            @if (filled($recommendation->assistanceProgram?->url))
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tautan program</p>
                    <a href="{{ $recommendation->assistanceProgram->url }}" class="mt-1 inline-flex text-sm font-semibold text-brand hover:text-brand-hover" target="_blank" rel="noopener noreferrer">Buka halaman program</a>
                </div>
            @endif
        </div>

        @if ($recommendation->applied_at)
            <p class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800" role="status">
                Sudah didaftarkan pada {{ $recommendation->applied_at->translatedFormat('d F Y') }}. Petugas desa yang melanjutkan prosesnya.
            </p>
        @else
            <form method="post" action="{{ route('umkm.rekomendasi.apply', $recommendation) }}" class="space-y-3">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-xl bg-brand px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-hover">Daftarkan minat</button>
                <p class="text-sm text-slate-600">Tidak perlu mengisi formulir. Proses bantuannya berlangsung di luar aplikasi, dan minat ini muncul di tindak lanjut petugas.</p>
            </form>
        @endif
    </div>
@endsection

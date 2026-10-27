@extends('layouts.app-shell')

@section('title', 'Profil UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-profile.jsx'])
@endpush

@section('content')
    @if ($mode === 'self' && ($business === null || ! $business->hasCompleteProfile()))
        <p class="mb-4 rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-900">
            Lengkapi data usaha ini dulu. Dashboard, produk, dan kebutuhan terbuka setelah data tersimpan.
        </p>
    @endif

    @if ($mode === 'self' && $business && $business->verification_status === 'needs_revision')
        <div class="mb-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-amber-900 shadow-sm">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-amber-900">Perhatian: Profil Usaha Memerlukan Perbaikan</h3>
                    <p class="mt-1 text-sm text-amber-800">
                        Petugas desa telah memeriksa data usaha Anda dan meminta perbaikan sebelum dapat diverifikasi.
                    </p>
                    @if(!empty($business->verification_note))
                        <div class="mt-2.5 rounded-xl border border-amber-200 bg-white/90 p-3 text-sm text-amber-950">
                            <span class="font-bold text-amber-900">Catatan Perbaikan dari Petugas:</span>
                            <p class="mt-1 whitespace-pre-line text-slate-700">{{ $business->verification_note }}</p>
                        </div>
                    @endif
                    <p class="mt-2.5 text-xs text-amber-700">
                        Silakan perbarui data pada formulir di bawah ini dan simpan ulang. Status profil Anda akan otomatis dikembalikan ke status <strong>Menunggu Verifikasi</strong> untuk ditinjau kembali oleh petugas desa.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div id="app" data-page="umkmProfile"></div>
    <script type="application/json" id="page-props">@json(['business' => $business, 'businessTypes' => $businessTypes, 'mode' => $mode])</script>
@endsection

@extends('layouts.app-shell')

@section('title', 'Profil UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-profile.jsx'])
@endpush

@section('content')
    @if ($business === null && $mode === 'self')
        <p class="mb-4 rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-900">
            Lengkapi data usaha ini dulu. Dashboard, produk, dan kebutuhan terbuka setelah data tersimpan.
        </p>
    @endif

    <div id="app" data-page="umkmProfile"></div>
    <script type="application/json" id="page-props">@json(['business' => $business, 'businessTypes' => $businessTypes, 'mode' => $mode])</script>
@endsection

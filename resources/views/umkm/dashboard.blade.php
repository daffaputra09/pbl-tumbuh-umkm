@extends('layouts.app-shell')

@section('title', 'Dashboard Usaha UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-dashboard.jsx'])
@endpush

@section('content')
    @if (session('status'))
        <p class="mb-4 rounded-2xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-900" role="status">{{ session('status') }}</p>
    @endif
    @php
        $pageProps = [
            'business' => $business,
            'ownerName' => $ownerName,
            'primaryObstacle' => $primaryObstacle,
            'recommendations' => $recommendations,
        ];
    @endphp
    <div id="app" data-page="umkmDashboard"></div>
    <script type="application/json" id="page-props">@json($pageProps)</script>
@endsection

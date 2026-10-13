@extends('layouts.app-shell')

@section('title', 'Dashboard Usaha UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-dashboard.jsx'])
@endpush

@section('content')
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

@extends('layouts.app-shell')

@section('title', 'Dashboard Usaha UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-dashboard.jsx'])
@endpush

@section('content')
    <div id="app" data-page="umkmDashboard"></div>
    <script type="application/json" id="page-props">@json(['business' => $business, 'ownerName'
    => $ownerName])</script>
@endsection

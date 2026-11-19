@extends('layouts.app-shell')

@section('title', 'Pendataan UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-profile.jsx'])
@endpush

@section('content')
    <div id="app" data-page="petugasPendataanUmkm"></div>
    <script type="application/json" id="page-props">{!! json_encode(['business' => $business, 'businessTypes' => $businessTypes, 'dusunList' => $dusunList, 'mode' => $mode]) !!}</script>
@endsection

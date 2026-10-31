@extends('layouts.app-shell')

@section('title', 'Riwayat Pembinaan - Tumbuh UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-riwayat-pembinaan.jsx'])
@endpush

@section('content')
    <div id="app" data-page="umkmRiwayatPembinaan"></div>
    <script id="page-props" type="application/json">
        {!! json_encode(compact('sessions', 'business')) !!}
    </script>
@endsection

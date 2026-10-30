@extends('layouts.app-shell')

@section('title', 'Riwayat Pembinaan')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/riwayat-pembinaan-pimpinan.jsx'])
@endpush

@section('content')
    <div id="app" data-page="pimpinanRiwayatPembinaan"></div>
    <script id="page-props" type="application/json">
        {!! json_encode(compact('history')) !!}
    </script>
@endsection

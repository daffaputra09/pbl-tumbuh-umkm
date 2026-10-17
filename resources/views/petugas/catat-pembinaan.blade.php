@extends('layouts.app-shell')

@section('title', 'Catat & Riwayat Pembinaan')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/catat-pembinaan.jsx'])
@endpush

@section('content')
    <div id="app" data-page="petugasCatatPembinaan"></div>
    <script id="page-props" type="application/json">
        {!! json_encode(compact('queue', 'history', 'businesses')) !!}
    </script>
@endsection


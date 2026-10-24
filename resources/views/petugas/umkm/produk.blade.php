@extends('layouts.app-shell')

@section('title', 'Kelola Produk UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/produk.jsx'])
@endpush

@section('content')
    <div id="app" data-page="petugasProduk"></div>
    <script type="application/json" id="page-props">@json(['business' => $business, 'mode' => $mode])</script>
@endsection

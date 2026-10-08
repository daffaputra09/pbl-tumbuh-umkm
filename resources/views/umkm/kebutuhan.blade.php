@extends('layouts.app-shell')

@section('title', 'Kebutuhan & Kendala Usaha UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-needs.jsx'])
@endpush

@section('content')
    <div id="app" data-page="umkmKebutuhan"></div>
    <script type="application/json" id="page-props">@json(['business' => $business, 'categories' => $categories])</script>
@endsection

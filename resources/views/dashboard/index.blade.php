@extends('layouts.app-shell')

@section('title', 'Dashboard - Tumbuh UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/dashboard.jsx'])
@endpush

@section('content')
    <div id="app" data-page="dashboard"></div>
    <script type="application/json" id="page-props">@json($dashboard)</script>
@endsection

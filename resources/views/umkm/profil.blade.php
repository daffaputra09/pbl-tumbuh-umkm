@extends('layouts.app-shell')

@section('title', 'Profil UMKM')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/umkm-profile.jsx'])
@endpush

@section('content')
    <div id="app" data-page="umkmProfile"></div>
@endsection

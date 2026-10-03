@extends('layouts.app-shell')

@section('title', 'Kelola Jenis Usaha')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/jenis-usaha.jsx'])
@endpush

@section('content')
    <div id="app" data-page="petugasJenisUsaha"></div>
@endsection

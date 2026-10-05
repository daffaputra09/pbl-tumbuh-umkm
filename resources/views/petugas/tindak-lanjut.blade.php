@extends('layouts.app-shell')

@section('title', 'Ajukan Tindak Lanjut')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/tindak-lanjut.jsx'])
@endpush

@section('content')
    <div id="app" data-page="petugasTindakLanjut"></div>
@endsection

@extends('layouts.app-shell')

@section('title', 'Persetujuan Tindak Lanjut')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/persetujuan-tindak-lanjut.jsx'])
@endpush

@section('content')
    <div id="app" data-page="pimpinanPersetujuanTindakLanjut"></div>
@endsection

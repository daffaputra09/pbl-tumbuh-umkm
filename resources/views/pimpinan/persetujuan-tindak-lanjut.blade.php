@extends('layouts.app-shell')

@section('title', 'Persetujuan Tindak Lanjut')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/persetujuan-tindak-lanjut.jsx'])
@endpush

@section('content')
    <script id="page-props" type="application/json">
        {!! json_encode(['queue' => $queue]) !!}
    </script>
    <div id="app" data-page="pimpinanPersetujuanTindakLanjut"></div>
@endsection

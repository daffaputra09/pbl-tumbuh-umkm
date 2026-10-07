@extends('layouts.app-shell')

@section('title', 'Ajukan Tindak Lanjut')

@push('head')
    @viteReactRefresh
    @vite(['resources/js/entries/tindak-lanjut.jsx'])
@endpush

@section('content')
    <div id="app" data-page="petugasTindakLanjut"></div>
    <script id="page-props" type="application/json">
        {!! json_encode(compact('queue')) !!}
    </script>
@endsection

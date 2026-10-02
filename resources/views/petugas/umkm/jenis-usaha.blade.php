<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Kelola daftar jenis usaha UMKM untuk Tumbuh UMKM.">
        <meta name="theme-color" content="#0F766E">

        <title>Kelola Jenis Usaha UMKM</title>

        @fonts

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body>
        <div id="app" data-page="petugas-jenis-usaha"></div>
    </body>
</html>

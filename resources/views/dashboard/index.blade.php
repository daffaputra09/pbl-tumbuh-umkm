<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Ringkasan UMKM aktif, status kendala dari asesmen, dan sebaran usaha di desa.">
        <meta name="theme-color" content="#0F766E">

        <title>Dashboard - Tumbuh UMKM</title>

        @fonts

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    </head>
    <body>
        <div id="app" data-page="dashboard"></div>
        <script type="application/json" id="page-props">@json($dashboard)</script>
    </body>
</html>

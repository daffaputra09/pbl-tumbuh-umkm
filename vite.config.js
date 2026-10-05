import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/petugas-umkm.css',
                'resources/js/app.jsx',
                'resources/js/entries/dashboard.jsx',
                'resources/js/entries/umkm-dashboard.jsx',
                'resources/js/entries/umkm-profile.jsx',
                'resources/js/entries/umkm-needs.jsx',
                'resources/js/entries/jenis-usaha.jsx',
                'resources/js/entries/produk.jsx',
                'resources/js/entries/tindak-lanjut.jsx',
                'resources/js/entries/persetujuan-tindak-lanjut.jsx'
            ],
            refresh: true,
            fonts: [
                bunny('Plus Jakarta Sans', {
                    weights: [400, 500, 600, 700, 800],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        react(),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});

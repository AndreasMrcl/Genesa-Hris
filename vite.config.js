import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],

    server: {
        host: true,               // auto detect IP / allow LAN
        port: 5173,               // default Vite port
        strictPort: true,         // jangan ganti port random
        hmr: {
            host: '192.168.18.207',   // IP laptop/PC kamu
            protocol: 'ws',
            port: 5173,
        },
    },
});

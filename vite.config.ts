import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel, { refreshPaths } from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.ts','resources/css/filament/admin/theme.css'],
            ssr: 'resources/js/ssr.ts',
            refresh: [
                ...refreshPaths,
                "app/Filament/**",
                "app/Livewire/**",
                "app/Providers/Filament/**",
                "app/Models/**",
            ],
        }),
        tailwindcss(),
        wayfinder(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});

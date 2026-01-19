import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin.css',
                'resources/js/filament/provinces.js',
                'resources/css/number-analytics.css',
                'resources/js/number-analytics.js'
            ],
            refresh: true,
        }),
    ],
});

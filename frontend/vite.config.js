import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { resolve } from 'path';

export default defineConfig({
    plugins: [vue()],
    define: {
        'process.env.NODE_ENV': JSON.stringify('production'),
    },
    build: {
        lib: {
            entry: resolve(__dirname, 'src/main.js'),
            name: 'CallbackForm',
            fileName: () => 'callback-form.js',
            formats: ['iife'],
        },
        outDir: resolve(__dirname, '../www/local/js/callback-form/dist'),
        emptyOutDir: true,
    },
});
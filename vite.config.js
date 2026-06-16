import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_URL || 'http://localhost';

    const isNgrok = appUrl.includes('ngrok-free.dev');

    return {
        plugins: [
            laravel({
                input: ['resources/sass/app.scss', 'resources/js/app.js','resources/js/pc-builder.js'],
                refresh: true,
            }),
        ],
        server: {
            host: isNgrok ? '0.0.0.0' : 'localhost',
            
            port: 5173,
            
            hmr: {
                host: isNgrok ? appUrl.replace(/^https?:\/\//, '') : 'localhost',
                protocol: isNgrok ? 'wss' : 'ws',
            },
        },
    };
});
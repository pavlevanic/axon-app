import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_URL;
    const appHost = new URL(appUrl).hostname;
    const isExternal = !['localhost', '127.0.0.1'].includes(appHost);

    return {
        plugins: [
            laravel({
                input: ['resources/sass/app.scss', 'resources/js/app.js', 'resources/js/pc-builder.js'],
                refresh: true,
            }),
        ],
        server: {
            host: isExternal ? '0.0.0.0' : 'localhost',
            port: 5173,
            hmr: {
                host: appHost,
                protocol: isExternal ? 'wss' : 'ws',
            },
        },
    };
});
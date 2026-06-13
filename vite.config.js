import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({ input: ['resources/css/app.scss', 'resources/js/app.js'] })
  ],
  server: {
    host: '0.0.0.0',
    hmr: {
      host: 'snowfall-craftwork-charging.ngrok-free.dev',
      protocol: 'wss',
    },
  },
});
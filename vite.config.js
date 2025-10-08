import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig(({ mode }) => {
    // Cargar variables de entorno
    const env = loadEnv(mode, process.cwd(), '');
    
    return {
        server: {
            https: false,
            host: '0.0.0.0',
            hmr: {
                host: 'localhost',
                protocol: 'ws',
            },
            cors: true,
            watch: {
                usePolling: true,
            },
            headers: {
                'Access-Control-Allow-Origin': '*',
                'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
                'Access-Control-Allow-Headers': 'X-Requested-With, content-type, Authorization',
            },
        },
        define: {
            'process.env': {
                VITE_PUSHER_APP_KEY: JSON.stringify(env.VITE_PUSHER_APP_KEY || ''),
                VITE_PUSHER_APP_CLUSTER: JSON.stringify(env.VITE_PUSHER_APP_CLUSTER || 'sa1'),
                VITE_PUSHER_HOST: JSON.stringify(env.VITE_PUSHER_HOST || `ws-${env.VITE_PUSHER_APP_CLUSTER || 'sa1'}.pusher.com`),
                VITE_PUSHER_PORT: JSON.stringify(env.VITE_PUSHER_PORT || 80),
                VITE_PUSHER_SCHEME: JSON.stringify(env.VITE_PUSHER_SCHEME || 'https'),
                MIX_PUSHER_APP_KEY: JSON.stringify(env.VITE_PUSHER_APP_KEY || env.MIX_PUSHER_APP_KEY || ''),
                MIX_PUSHER_APP_CLUSTER: JSON.stringify(env.VITE_PUSHER_APP_CLUSTER || env.MIX_PUSHER_APP_CLUSTER || 'sa1'),
            },
        },
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js'
                ],
                refresh: [
                    'resources/views/**',
                    'app/Http/Controllers/**',
                    'routes/**',
                ],
            }),
        ],
        resolve: {
            alias: {
                '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
                '~@fortawesome': path.resolve(__dirname, 'node_modules/@fortawesome'),
            }
        },
    };
});

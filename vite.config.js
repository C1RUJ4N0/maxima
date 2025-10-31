import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],

    // --- AÑADIR ESTA SECCIÓN COMPLETA ---
    // Esto soluciona los problemas de detección de cambios (file watching) 
    // y conexión HMR de Vite dentro de Docker en Windows.
    server: {
        // Escucha en todas las IPs dentro del contenedor (necesario para Docker)
        host: '0.0.0.0', 
        
        hmr: {
            // Le dice al navegador que se conecte a Vite a través de localhost
            host: 'localhost', 
        },
        watch: {
            // ¡ESTA ES LA LÍNEA CLAVE!
            // Fuerza a Vite a usar "polling" para detectar cambios de archivos.
            usePolling: true,
        }
    }
    // --- FIN DE LA SECCIÓN AÑADIDA ---
});
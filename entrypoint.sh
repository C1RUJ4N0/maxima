#!/bin/sh

# Navega al directorio de la aplicación
cd /var/www/html

# Optimiza la configuración de Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ¡SOLUCIÓN!
# migrate:fresh BORRA todas las tablas y ejecuta migraciones + seeders
# Esto soluciona el error 'Duplicate entry'
echo "Borrando, migrando y sembrando la base de datos..."
php artisan migrate:fresh --seed --force

# Inicia el proceso principal (PHP-FPM)
echo "Iniciando PHP-FPM..."
exec php-fpm

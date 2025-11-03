#!/bin/sh

# Navega al directorio de la aplicación
cd /var/www/html

# Optimiza la configuración de Laravel
# (Esto lee las variables de entorno de RDS del task-definition.json)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecuta las migraciones y los seeders
# (El --force es necesario para producción)
echo "Ejecutando migraciones y seeders..."
php artisan migrate --seed --force


echo "Iniciando PHP-FPM..."
exec php-fpm
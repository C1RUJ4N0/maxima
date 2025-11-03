# Stage 1: Instalar dependencias de Composer
FROM composer:2.7 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
RUN docker-php-ext-install bcmath
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Stage 3: Crear la imagen final de PHP-FPM
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema
RUN apk add --no-cache \
    mysql-client \
    zip \
    unzip \
    curl \
    libpng-dev \
    libzip-dev \
    jpeg-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    fcgi

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip dom xml

WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Copiar dependencias de Composer
COPY --from=vendor /app/vendor/ /var/www/html/vendor/

# --- ¡NUEVOS PASOS! ---
# Copia el script de entrypoint y dale permisos de ejecución
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

# --- ¡CAMBIO FINAL! ---
# Usa el script como punto de entrada en lugar de iniciar php-fpm directamente
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
# Stage 1: Instalar dependencias de Composer
FROM composer:2.7 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./

# --- ARREGLO: Instalar la extensión bcmath que falta ---
RUN docker-php-ext-install bcmath

# --no-scripts evita el error de "artisan not found"
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# --- ARREGLO 2: ELIMINAMOS EL "STAGE 2" DE NODE/VITE PORQUE NO SE USA ---

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
    fcgi # <-- ESTA ES LA CORRECCIÓN (era fcgi-bin)

# Instalar extensiones de PHP (bcmath también se necesita aquí)
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip dom xml

WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Copiar dependencias de Composer desde el stage anterior
COPY --from=vendor /app/vendor/ /var/www/html/vendor/

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
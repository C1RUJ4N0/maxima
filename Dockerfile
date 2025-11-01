# Stage 1: Instalar dependencias de Composer
FROM composer:2.7 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./
# --no-scripts evita el error de "artisan not found"
RUN composer install --no-interaction --no-dev --optimize-autoloader --no-scripts

# Stage 2: Construir los assets de Node.js
FROM node:20-alpine as node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 3: Crear la imagen final de PHP-FPM
FROM php:8.3-fpm-alpine

# Instalar dependencias del sistema, incluyendo "oniguruma-dev"
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
    oniguruma-dev

# Instalar extensiones de PHP
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip dom xml

WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Copiar dependencias de Composer y Node desde los stages anteriores
COPY --from=vendor /app/vendor/ /var/www/html/vendor/
COPY --from=node /app/public/build/ /var/www/html/public/build/

# Configurar permisos (CORRECCIÓN CLAVE)
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
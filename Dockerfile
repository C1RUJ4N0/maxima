# Usa una imagen oficial de PHP como base
FROM php:8.3-fpm-alpine

# Instala extensiones de PHP y dependencias del sistema
RUN apk add --no-cache \
    git \
    curl \
    mysql-client \
    zip \
    unzip \
    nodejs \
    npm

# Instala las extensiones de PHP requeridas por Laravel
RUN docker-php-ext-install pdo pdo_mysql

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Instala Composer de forma global
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Expone el puerto 8000 para el servidor de Artisan
EXPOSE 8000
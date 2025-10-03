# Usa una imagen oficial de PHP como base
FROM php:8.3-fpm-alpine

# Instala extensiones de PHP y dependencias necesarias
RUN apk add --no-cache \
    git \
    curl \
    mysql-client \
    zip \
    unzip \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_mysql

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Expone el puerto 80 para el servidor web
EXPOSE 80
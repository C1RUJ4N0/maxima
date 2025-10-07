
FROM php:8.3-fpm-alpine
RUN apk add --no-cache \
    git \
    curl \
    mysql-client \
    zip \
    unzip

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

EXPOSE 8000

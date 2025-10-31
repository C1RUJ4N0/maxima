
FROM composer:2.7 as vendor
WORKDIR /app
COPY database/ database/
COPY composer.json composer.lock ./

RUN composer install --no-interaction --no-dev --optimize-autoloader

FROM node:20-alpine as node
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm install
COPY . .

RUN npm run build 


FROM php:8.3-fpm-alpine


RUN apk add --no-cache \
    nginx \
    mysql-client \
    zip \
    unzip
    
RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www/html


COPY --from=vendor /app/vendor/ /var/www/html/vendor/
COPY --from=node /app/public/ /var/www/html/public/
COPY --from=node /app/ /var/www/html/

COPY nginx.conf /etc/nginx/nginx.conf


RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD sh -c "php-fpm & nginx -g 'daemon off;'"

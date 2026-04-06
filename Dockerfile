FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo_mysql bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 80

CMD php artisan migrate --force && nginx && php-fpm

FROM php:8.1-fpm

RUN apt-get update && apt-get install -y \
        procps\
        libzip-dev \
        zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

FROM php:8.1-fpm

ENV TZ=Europe/Warsaw

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
        procps\
        libzip-dev \
        zip \
    && docker-php-ext-install zip \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install iconv

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN useradd -u 1000 -ms /bin/bash app

COPY --chown=app:app . /var/www

USER app

EXPOSE 9000

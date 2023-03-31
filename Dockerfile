FROM php:8.1-zts-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MEMORY_LIMIT=-1 \
    WORKDIR=/var/www/html

WORKDIR $WORKDIR

RUN apk update && \
    apk add --no-cache libzip-dev sqlite-dev bash && \
    docker-php-ext-install zip pdo pdo_mysql pdo_sqlite && \
    docker-php-ext-configure zip && \
    apk add --update linux-headers && \
    pecl install xdebug

COPY --from=composer:2.0.7 /usr/bin/composer /usr/bin/composer

COPY ./.docker /





# Composer Depdendencies
FROM composer:latest as vendor

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

# Application Stage
FROM phpdockerio/php72-fpm:latest as php

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install  php7.2-intl

COPY . /application
COPY --from=vendor /app/vendor /application/vendor/

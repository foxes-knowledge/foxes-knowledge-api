FROM composer:2.3.5 AS build

WORKDIR /build

COPY composer.* ./

RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

COPY . .

RUN composer dump-autoload --optimize

FROM php:8.1.6-fpm-alpine3.15 AS production

RUN apk add --no-cache --virtual .production-deps \
    nginx libpq

RUN set -xe \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    libpq-dev \
    && pecl install -f apcu-5.1.21 \
    && docker-php-source extract \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install -j$(nproc) pdo_pgsql pgsql \
    && docker-php-ext-enable apcu \
    && docker-php-source delete \
    && pecl clear-cache \
    && rm -rf /tmp/* \
    && apk del .build-deps

COPY docker/etc /etc

ARG S6_OVERLAY_VERSION=v3.1.0.1
RUN S6_OVERLAY_RELEASE=https://github.com/just-containers/s6-overlay/releases/download/${S6_OVERLAY_VERSION} \
    && curl -sSL $S6_OVERLAY_RELEASE/s6-overlay-noarch.tar.xz | tar Jxpf - -C / \
    && curl -sSL $S6_OVERLAY_RELEASE/s6-overlay-x86_64.tar.xz | tar Jxpf - -C / \
    && chmod -R +x /etc/services.d
ENV S6_CMD_WAIT_FOR_SERVICES_MAXTIME=60000

COPY docker/local /usr/local
COPY --from=build --chown=www-data:www-data /build /var/www/knowledge

EXPOSE 80
WORKDIR /var/www/knowledge
ENTRYPOINT ["/init"]

FROM production AS development

RUN set -xe \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install -f xdebug-3.1.4 \
    && docker-php-ext-enable xdebug \
    && pecl clear-cache \
    && apk del .build-deps

COPY docker/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=build /usr/bin/composer /usr/local/bin/composer
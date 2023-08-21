ARG PHP_EXTS="mbstring xml exif pcntl bcmath gd zip pdo_pgsql"


## PHP CLI ##
FROM php:8.1-cli-alpine as php_cli

ARG PHP_EXTS

WORKDIR /var/www/html

# Add user with group
RUN addgroup -S composer \
    && adduser -S composer -G composer \
    && chown -R composer /var/www/html

# Install system dependencies
RUN apk add --no-cache libpng-dev libzip-dev libpq-dev

# Install build dependencies and then install php extensions
RUN apk add --virtual build-dependencies --no-cache ${PHPIZE_DEPS} \
    oniguruma-dev libxml2-dev \
    && docker-php-ext-install -j$(nproc) ${PHP_EXTS} \
    && apk del build-dependencies

# Get composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Set system user
USER composer

# Install only composer dependencies
COPY --chown=composer composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy code base and run composer again for running any install scripts
COPY --chown=composer . .
RUN composer install --no-dev --optimize-autoloader --prefer-dist



## PHP FPM ##
FROM php:8.1-fpm-alpine as php_fpm

ARG PHP_EXTS

WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache libpng-dev libzip-dev libpq-dev

# Install build dependencies and then install php extensions
RUN apk add --virtual build-dependencies --no-cache ${PHPIZE_DEPS} \
    oniguruma-dev libxml2-dev \
    && docker-php-ext-install -j$(nproc) ${PHP_EXTS} \
    && apk del build-dependencies

# Set system user
USER www-data

# Copy code base with vendor from php_cli build
COPY --from=php_cli --chown=www-data:www-data /var/www/html /var/www/html

RUN chmod -R 755 /var/www/html/storage

# Cache laravel events, routes and views
RUN php artisan event:cache && \
    php artisan route:cache && \
    php artisan view:cache



## Nginx ##
FROM nginx:1.21.0-alpine as nginx

WORKDIR /var/www/html

COPY --from=php_cli /var/www/html/public /var/www/html/public

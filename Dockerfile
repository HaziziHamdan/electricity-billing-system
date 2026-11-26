# syntax=docker/dockerfile:1

############################################
# Stage 1: PHP dependencies (no dev packages)
############################################
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
# Extensions (pgsql, bcmath) are verified in the final stage instead.
RUN composer install \
        --no-dev --no-scripts --no-autoloader \
        --prefer-dist --no-interaction --no-progress \
        --ignore-platform-reqs

COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

############################################
# Stage 2: Front-end assets (Vite + Tailwind)
############################################
FROM node:22-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
# Tailwind scans Laravel's pagination views in vendor/ for class names.
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

############################################
# Stage 3: Runtime (PHP + Apache)
############################################
FROM php:8.4-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql bcmath opcache \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

# Production php.ini + OPcache tuning
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY docker/opcache.ini "$PHP_INI_DIR/conf.d/opcache.ini"

# Apache: serve /public, listen on Render's $PORT
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i 's/^Listen 80$/Listen ${PORT}/' /etc/apache2/ports.conf
ENV PORT=8080

WORKDIR /var/www/html

COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build

# Fail the build early if a required PHP extension is missing.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN composer check-platform-reqs --no-dev \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

EXPOSE 8080
CMD ["start"]
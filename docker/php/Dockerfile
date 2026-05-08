FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    libpq-dev \
    oniguruma-dev \
    zip \
    unzip \
    libzip-dev \
    shadow

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG USER_ID=1000
ARG GROUP_ID=1000

RUN addgroup -g ${GROUP_ID} -S www && \
    adduser -u ${USER_ID} -S www -G www

WORKDIR /var/www/html/backend

RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache && \
    chmod -R 777 storage bootstrap/cache

COPY backend/composer.json backend/composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader --no-dev

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

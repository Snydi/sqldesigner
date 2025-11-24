FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    postgresql-dev \
    oniguruma-dev \
    zip \
    unzip \
    libzip-dev

RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html/backend

RUN chown -R www-data:www-data /var/www/html/backend

USER www-data

CMD ["sh", "-c", "composer install --no-interaction && php-fpm"]

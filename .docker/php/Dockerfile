FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    git \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath zip


RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer


WORKDIR /var/www/html


COPY . .


RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache


EXPOSE 9000
CMD ["php-fpm"]

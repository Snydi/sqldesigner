#!/bin/sh
chown -R www:www /var/www/html/backend/storage /var/www/html/backend/bootstrap/cache
exec su-exec www php-fpm

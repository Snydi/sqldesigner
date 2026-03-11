#!/bin/sh
chmod -R 777 /var/www/html/backend/storage /var/www/html/backend/bootstrap/cache

if [ "$#" -gt 0 ]; then
    exec "$@"
else
    exec php-fpm
fi

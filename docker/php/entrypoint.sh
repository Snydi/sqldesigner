#!/bin/sh
  chmod -R 777 /var/www/html/backend/storage /var/www/html/backend/bootstrap/cache
  exec php-fpm

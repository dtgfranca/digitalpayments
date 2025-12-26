#!/bin/sh
set -e

echo "Starting Laravel container..."

mkdir -p /var/www/storage/framework/{cache,sessions,views} /var/www/storage/logs /var/www/bootstrap/cache
chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database || true


php /var/www/artisan key:generate --force || true
php /var/www/artisan migrate --force || true

exec php-fpm -F

#!/bin/bash

# Install dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    sudo -u www-data composer install --no-interaction --optimize-autoloader
fi

# Copy .env if it doesn't exist
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Set permissions for storage and bootstrap/cache
mkdir -p /var/www/storage/framework/cache /var/www/storage/framework/sessions /var/www/storage/framework/views /var/www/storage/logs /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# If .env exists, make sure it's writable
if [ -f ".env" ]; then
    chown www-data:www-data .env
    chmod 664 .env
fi

# Generate app key
sudo -u www-data php artisan key:generate --no-interaction --force

# Generate JWT secret
sudo -u www-data php artisan jwt:secret --no-interaction --force

# Run migrations
sudo -u www-data php artisan migrate --force

# Start php-fpm
exec php-fpm

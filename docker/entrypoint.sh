#!/bin/bash

# Install dependencies if vendor directory doesn't exist
if [ ! -d "vendor" ]; then
    composer install --no-interaction --optimize-autoloader
fi

# Copy .env if it doesn't exist
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

# Generate app key
php artisan key:generate --no-interaction --force

# Generate JWT secret
php artisan jwt:secret --no-interaction --force

# Run migrations
php artisan migrate --force

# Start php-fpm
php-fpm

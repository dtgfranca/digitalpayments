FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git unzip libsqlite3-dev sqlite3 \
    libpng-dev libonig-dev libxml2-dev zip curl \
    && docker-php-ext-install pdo pdo_sqlite mbstring bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

RUN cp .env.example .env || true \
    && php artisan key:generate \
    && touch database/database.sqlite

EXPOSE 9000
CMD ["php-fpm"]

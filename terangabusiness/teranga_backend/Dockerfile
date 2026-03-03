FROM php:8.2-cli

# Dépendances système
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring opcache \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Dépendances PHP
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Code source
COPY . .

# Génération autoload final
RUN composer dump-autoload --optimize

# Permissions storage
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000

# Migrations + démarrage
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

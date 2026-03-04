FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git curl zip unzip ca-certificates \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# .env temporaire pour que php artisan package:discover fonctionne pendant le build
RUN printf "APP_NAME=Teranga\nAPP_ENV=local\nAPP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=\nAPP_DEBUG=false\nDB_CONNECTION=sqlite\n" > .env

RUN composer install --no-dev --optimize-autoloader --no-interaction

# Supprimer le .env temporaire (les vraies vars viennent de Render)
RUN rm .env

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

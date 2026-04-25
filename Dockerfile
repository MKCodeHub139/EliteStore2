FROM php:8.2-cli

# Install system deps
RUN apt-get update && apt-get install -y \
    git unzip curl libpq-dev libzip-dev zip nodejs npm

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Install PHP deps
RUN composer install --no-dev --optimize-autoloader

# 🔥 IMPORTANT: Install node deps & build assets
RUN npm install && npm run build
RUN php artisan migrate:fresh --seed

EXPOSE 10000

CMD php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=10000
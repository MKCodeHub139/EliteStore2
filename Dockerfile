FROM php:8.2-cli

# System dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libpq-dev \
    libzip-dev \
    zip

# Install Node.js 20 (IMPORTANT)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy only package files first (OPTIMIZATION)
COPY package*.json ./

# Install frontend deps
RUN npm ci

# Copy full project
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Build frontend
RUN npm run build

# Safety check
RUN ls -la public/build

EXPOSE 10000

# Start Laravel
CMD php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=10000    
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-client \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --no-interaction --prefer-dist

# Install Node dependencies and build assets
RUN npm install && npm run build

# Create storage directory
RUN mkdir -p storage/logs && chmod -R 755 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Run Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
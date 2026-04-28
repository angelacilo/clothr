FROM php:8.1-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev sqlite3 \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

RUN chown -R www-data:www-data /var/www/html

RUN mkdir -p /var/www/html/bootstrap/cache /var/www/html/storage && \
    chown -R www-data:www-data /var/www/html/bootstrap/cache /var/www/html/storage && \
    chmod -R 755 /var/www/html/bootstrap/cache /var/www/html/storage

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "/var/www/html/public"]

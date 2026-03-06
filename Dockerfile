FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libpq-dev libpng-dev libjpeg-dev \
    libfreetype6-dev libonig-dev libxml2-dev libzip-dev libicu-dev \
    nodejs npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath zip
RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN npm install && npm run build

RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

EXPOSE 8080

CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=8080
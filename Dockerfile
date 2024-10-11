FROM php:8.1-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql bcmath mbstring

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /var/www
RUN composer install --optimize-autoloader --no-dev
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

USER www-data

EXPOSE 9000
CMD ["php-fpm"]

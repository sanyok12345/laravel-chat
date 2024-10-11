FROM php:8.2-apache as build

RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libpq-dev \
    git \
    unzip

RUN a2enmod rewrite

RUN docker-php-ext-install pdo_mysql pdo_pgsql zip

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

FROM php:8.2-apache

COPY --from=build /var/www/html /var/www/html

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

RUN php artisan key:generate --force
RUN php artisan migrate
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

CMD ["apache2-foreground"]
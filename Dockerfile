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
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql pdo_pgsql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

COPY . /var/www

COPY --chown=www-data:www-data . /var/www

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
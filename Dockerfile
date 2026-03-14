FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    freetype-dev \
    libjpeg-turbo-dev \
    libzip-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN if ! id -u www-data >/dev/null 2>&1; then addgroup -g 1000 -S www-data; fi && \
    if ! id -u www-data >/dev/null 2>&1; then adduser -u 1000 -S www-data -G www-data; fi
RUN chown -R www-data:www-data /var/www/html
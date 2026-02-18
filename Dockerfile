FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    libzip-dev \
    zip \
    unzip \
    git \
    icu-dev \
    libpq-dev \
    oniguruma-dev \
    linux-headers

RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    zip \
    intl \
    opcache \
    bcmath

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock* /var/www/

RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist 2>/dev/null || true

COPY . /var/www

RUN composer dump-autoload --optimize && \
    chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/storage /var/www/bootstrap/cache

COPY .docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]

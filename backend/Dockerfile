FROM composer:2 AS composer

FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libssl-dev pkg-config \
    && docker-php-ext-install zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

# Install Composer binary
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app/backend

# Expose Laravel dev server port
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

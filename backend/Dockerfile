FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libssl-dev pkg-config \
    && docker-php-ext-install zip \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app/backend

# Expose Laravel dev server port
EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]


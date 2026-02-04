FROM php:8.3-cli

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_pgsql

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

FROM php:8.3-fpm

RUN apt update && apt install -y zlib1g-dev g++ git libicu-dev zip libzip-dev zip

RUN curl -fsSL --retry 5 \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    -o /usr/local/bin/install-php-extensions

RUN chmod +x /usr/local/bin/install-php-extensions && sync && install-php-extensions decimal opcache pdo_pgsql pdo_mysql pcntl mysqli bcmath intl zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
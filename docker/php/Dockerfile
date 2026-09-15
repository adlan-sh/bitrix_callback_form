FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libicu-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j$(nproc) \
        gd \
        zip \
        xml \
        curl \
        mbstring \
        mysqli \
        pdo \
        pdo_mysql \
        opcache \
        bcmath \
        intl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ARG HOST_UID=1000
ARG HOST_GID=1000

RUN usermod -u ${HOST_UID} www-data && groupmod -g ${HOST_GID} www-data

WORKDIR /var/www/html

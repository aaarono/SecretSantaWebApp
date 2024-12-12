# parent-directory/Dockerfile

# Используем официальный образ PHP с FPM
FROM php:8.2-fpm

# Установка зависимостей системы и необходимых расширений PHP
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    zlib1g-dev \
    libfreetype6-dev \
    libjpeg-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www/html

# Настройка прав доступа (при необходимости)
RUN mkdir -p var && chown -R www-data:www-data var

# Запуск PHP-FPM
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
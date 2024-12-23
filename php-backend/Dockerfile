FROM php:8.2-apache

# Установим необходимые пакеты
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Рабочая директория (DocumentRoot уже /var/www/html)
WORKDIR /var/www/html

# Скопируем код вашего бэкенда в контейнер
COPY . /var/www/html/

# Установим зависимости PHP через Composer (если composer.json есть в этой же папке)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# Открываем 80 порт (Apache)
EXPOSE 80

CMD ["apache2-foreground"]

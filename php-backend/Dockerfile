FROM php:7.4-apache

# Установка необходимых системных зависимостей и библиотек для pdo_pgsql
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        && docker-php-ext-install pdo pdo_pgsql \
        && apt-get clean && rm -rf /var/lib/apt/lists/*

# Включение модуля Apache mod_rewrite (если необходимо)
RUN a2enmod rewrite

# Копирование исходного кода приложения
COPY . /var/www/html/

# Настройка рабочей директории
WORKDIR /var/www/html

# Установка прав (опционально, если необходимо)
# RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]

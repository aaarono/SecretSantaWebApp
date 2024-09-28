FROM php:7.4-apache

# # Установка необходимых расширений PHP
# RUN docker-php-ext-install pdo pdo_pgsql

# Копирование исходного кода приложения
COPY . /var/www/html/

# Включение модуля Apache mod_rewrite (если необходимо)
RUN a2enmod rewrite

EXPOSE 80
CMD ["apache2-foreground"]

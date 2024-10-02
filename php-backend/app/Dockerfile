# Используем официальный образ PHP 8.2 с Apache
FROM php:8.2-apache

# Установка необходимых системных зависимостей и библиотек
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        git \
        unzip \
        libzip-dev \
        wget && \
    docker-php-ext-install pdo pdo_pgsql zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Включение модуля Apache mod_rewrite
RUN a2enmod rewrite

# Настройка DocumentRoot
RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

# Разрешение использования .htaccess
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony && \
    rm -rf /root/.symfony5

# Создание нового пользователя и группы
RUN groupadd -g 1000 appuser && \
    useradd -r -u 1000 -g appuser appuser

# Копирование всего приложения из папки skeleton в контейнер
COPY skeleton/ /var/www/html/

# Изменение прав доступа для нового пользователя
RUN chown -R appuser:appuser /var/www/html

# Переход на пользователя "appuser"
USER appuser

# Установка зависимостей через Composer и Symfony Runtime
WORKDIR /var/www/html
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts && \
    composer require symfony/runtime

# Очистка и прогрев кэша Symfony
RUN php bin/console cache:clear --env=prod --no-debug && \
    php bin/console cache:warmup --env=prod --no-debug

# Переход обратно к пользователю root для выполнения системных команд
USER root

# Установка прав на директории var и vendor
RUN chown -R www-data:www-data var vendor && \
    chmod -R 775 var vendor

# Экспонирование порта Apache
EXPOSE 80

# Запуск Apache в переднем плане
CMD ["apache2-foreground"]

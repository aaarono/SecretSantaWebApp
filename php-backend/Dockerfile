# Используем официальный образ PHP 8.2 без Apache
FROM php:8.2-fpm

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

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony && \
    rm -rf /root/.symfony5

# Создание директории для кеша Composer и установка прав
RUN mkdir -p /home/appuser/.composer/cache && chown -R www-data:www-data /home/appuser/.composer

# Создание нового пользователя и группы
RUN groupadd -g 1000 appuser && \
    useradd -r -u 1000 -g appuser appuser

# Копирование всего приложения из папки skeleton в контейнер
COPY . /var/www/html/

# Изменение прав доступа для нового пользователя
RUN chown -R appuser:appuser /var/www/html

# Переход на пользователя "appuser"
USER appuser

# Установка зависимостей через Composer и Symfony Runtime без выполнения скриптов
WORKDIR /var/www/html
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts --no-cache && \
    composer require symfony/runtime --no-cache

# Установка прав на директории var и vendor
USER root
RUN chown -R www-data:www-data var vendor && \
    chmod -R 775 var vendor

# Экспонирование порта PHP-FPM
EXPOSE 9000

# Запуск PHP-FPM в переднем плане
CMD ["php-fpm"]

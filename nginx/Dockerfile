# Базовый образ Nginx
FROM nginx:alpine

# Копируем наш конфиг в папку, где Nginx ищет настройки
COPY default.conf /etc/nginx/conf.d/default.conf

# Стандартная команда запуска
CMD ["nginx", "-g", "daemon off;"]

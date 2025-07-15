# Stage 1: Сборка React-приложения
FROM node:18 AS build

# Установите рабочую директорию
WORKDIR /app

# Копируйте только package.json и package-lock.json для установки зависимостей
COPY package*.json ./

# Установите зависимости
RUN npm install

# Копируйте остальные файлы проекта
COPY . .

# Соберите приложение
RUN npm run build

# Stage 2: Запуск с помощью Nginx
FROM nginx:alpine

# Копируйте собранные файлы из предыдущего этапа
COPY --from=build /app/build/ /usr/share/nginx/html/

# Копируйте конфигурацию nginx для SPA
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Откройте порт 80
EXPOSE 80

# Запустите Nginx
CMD ["nginx", "-g", "daemon off;"]

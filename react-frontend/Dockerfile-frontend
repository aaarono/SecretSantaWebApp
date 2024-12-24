# Stage 1: Сборка React-приложения
FROM node:14 AS build

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Запуск с помощью Nginx
FROM nginx:alpine

# Удаляем дефолтный конфиг, если хотим свой
# RUN rm /etc/nginx/conf.d/default.conf

# Копируем собранные файлы в html-папку
COPY --from=build /app/build /usr/share/nginx/html

# (Если нужно кастомное конфиг-файл Nginx, можно COPY-нуть:
# COPY ./nginx.conf /etc/nginx/conf.d/default.conf
# )

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]

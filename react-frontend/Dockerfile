# Stage 1: Сборка React-приложения
FROM node:14 AS build

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: Запуск с помощью Apache
FROM httpd:2.4
COPY --from=build /app/build/ /usr/local/apache2/htdocs/

EXPOSE 80
CMD ["httpd-foreground"]

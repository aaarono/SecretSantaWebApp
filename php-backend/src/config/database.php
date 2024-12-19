<?php

namespace Secret\Santa\Config;

use PDO;

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Получаем параметры подключения из переменных окружения
            $host = getenv('PGHOST');
            $port = getenv('PGPORT');
            $db_name = getenv('PGDATABASE');
            $username = getenv('PGUSER');
            $password = getenv('PGPASSWORD');

            // Формируем строку подключения для PostgreSQL
            $dsn = "pgsql:host=$host;port=$port;dbname=$db_name";
            $this->conn = new PDO($dsn, $username, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            // Выводим сообщение об ошибке, если не удалось подключиться
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

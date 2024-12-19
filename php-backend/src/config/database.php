<?php

namespace Secret\Santa\Config;

use PDO;

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            // Получаем строку подключения из переменной окружения
            $databaseUrl = getenv('DATABASE_URL');

            if (!$databaseUrl) {
                throw new \Exception('DATABASE_URL переменная окружения не найдена');
            }

            // Парсим строку подключения
            $dbParts = parse_url($databaseUrl);

            $host = $dbParts['host'];
            $port = $dbParts['port'];
            $user = $dbParts['user'];
            $password = $dbParts['pass'];
            $dbname = ltrim($dbParts['path'], '/');

            // Формируем строку подключения для PostgreSQL
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            $this->conn = new PDO($dsn, $user, $password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            echo "Ошибка подключения: " . $exception->getMessage();
        } catch (\Exception $exception) {
            echo "Общая ошибка: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

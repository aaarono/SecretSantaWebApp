<?php

namespace Secret\Santa\Config;

use PDO;

class Database {
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        // Получение параметров из переменных окружения
        $this->host = getenv('DB_HOST');
        $this->port = getenv('DB_PORT') ?: '5432';
        $this->db_name = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');
    }

    public function getConnection() {
        $this->conn = null;

        try {
            // Формируем строку подключения для PostgreSQL
            $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode=require";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            // Логирование ошибки вместо прямого вывода
            error_log("Connection error: " . $exception->getMessage());
            // Можно выбросить исключение или обработать ошибку другим способом
        }

        return $this->conn;
    }
}

<?php

namespace Secret\Santa\Config;

use PDO;

class Database {

    private $host = 'db'; // Хост базы данных (например, имя сервиса Docker или IP-адрес)
    private $port = '5432'; // Порт PostgreSQL
    private $db_name = 'mydb'; // Имя базы данных
    private $username = 'user'; // Имя пользователя базы данных
    private $password = 'password'; // Пароль базы данных

    public $conn;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->port = $_ENV['DB_PORT'] ?? '5432';
        $this->db_name = $_ENV['DB_NAME'] ?? 'mydb';
        $this->username = $_ENV['DB_USER'] ?? 'user';
        $this->password = $_ENV['DB_PASSWORD'] ?? 'password';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            // Correct DSN for PostgreSQL in PHP
            $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->db_name";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            // Handle connection errors
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

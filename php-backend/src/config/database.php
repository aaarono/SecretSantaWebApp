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

    public function getConnection() {
        $this->conn = null;

        try {
            // Формируем строку подключения для PostgreSQL
            $dsn = "pgsql:host=$this->host;port=$this->port;dbname=$this->db_name";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $exception) {
            // Выводим сообщение об ошибке, если не удалось подключиться
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

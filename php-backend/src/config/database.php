<?php

namespace Secret\Santa\Config;

use PDO;

class Database {
    private $host = '20.126.41.73'; // Host
    private $port = '65433'; // PostgreSQL Port
    private $db_name = 'postgres'; // Database Name
    private $username = 'santa'; // DB Username
    private $password = 'santa_password!'; // DB Password
    public $conn;

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

<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Config\Database;

class AuthController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();

        // Настройка CORS
        $allowed_origins = [
            "http://localhost:3000",
            // Добавьте другие разрешённые домены при необходимости
        ];

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        if (in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true"); // Разрешаем отправку кукисов
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
            header("Access-Control-Max-Age: 86400"); // 24 часа
        }

        // Обработка предварительных запросов OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        header('Content-Type: application/json');
    }

    public function register($data) {
        // Проверка обязательных полей
        $requiredFields = ['email', 'username', 'phone', 'name', 'surname', 'gender', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return json_encode(['status' => 'error', 'message' => "Field $field is required"]);
            }
        }
    
        // Хеширование пароля
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    
        // SQL-запрос для вставки пользователя с ролью regular
        $query = 'INSERT INTO "User" (login, email, password_hash, first_name, last_name, phone, gender, role, created_at)
                  VALUES (:username, :email, :password_hash, :name, :surname, :phone, :gender, :role, NOW())';
    
        $stmt = $this->conn->prepare($query);
    
        // Привязка данных
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password_hash', $hashedPassword);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':surname', $data['surname']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindValue(':role', 'regular'); // Установка роли по умолчанию
    
        try {
            $stmt->execute();
            return json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } catch (\PDOException $e) {
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function login($data) {
        // Установка пути для сохранения сессий
        $this->setSessionSavePath();

        // Настройка параметров куки сессии
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            // 'domain' => 'localhost', // Удалено или заменено на корректный домен
            'secure' => false, // Установите true, если используете HTTPS
            'httponly' => true,
            'samesite' => 'Lax' // Или 'Strict', если необходимо
        ]);

        // Запуск сессии
        session_start();

        // Проверка обязательных полей
        if (empty($data['username']) || empty($data['password'])) {
            error_log('Login attempt with missing fields: ' . json_encode($data));
            return json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        }

        // Проверка пользователя в базе данных
        $query = 'SELECT * FROM "User" WHERE login = :username';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            error_log("Login attempt failed: Invalid username '{$data['username']}'");
            return json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }

        // Проверка пароля
        if (!password_verify($data['password'], $user['password_hash'])) {
            error_log("Login attempt failed: Invalid password for username '{$data['username']}'");
            return json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }

        // Успешный логин, создание сессии
        $_SESSION['user'] = [
            'username' => $user['login'],
            'email' => $user['email'],
            'name' => $user['first_name'],
            'surname' => $user['last_name'],
            'role' => $user['role']
        ];

        error_log("User '{$user['login']}' logged in successfully");

        return json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $_SESSION['user']
        ]);
    }

    public function check() {
        // Установка пути для сохранения сессий
        $this->setSessionSavePath();

        // Настройка параметров куки сессии
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            // 'domain' => 'localhost', // Удалено или заменено на корректный домен
            'secure' => false, // Установите true, если используете HTTPS
            'httponly' => true,
            'samesite' => 'Lax' // Или 'Strict', если необходимо
        ]);

        // Запуск сессии
        session_start();

        if (isset($_SESSION['user'])) {
            error_log("Auth check successful for user '{$_SESSION['user']['username']}'");
            return json_encode(['status' => 'success', 'user' => $_SESSION['user']]);
        } else {
            error_log("Auth check failed: Not authenticated");
            return json_encode(['status' => 'error', 'message' => 'Not authenticated']);
        }
    }

    public function logout() {
        // Установка пути для сохранения сессий
        $this->setSessionSavePath();

        // Настройка параметров куки сессии (если необходимо)
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            // 'domain' => 'localhost', // Удалено или заменено
            'secure' => false, // Установите true, если используете HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        // Запуск сессии
        session_start();
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        error_log("User logged out successfully");

        return json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
    }

    public function changePassword($data) {
        // Проверка обязательных полей
        if (empty($data['current_password']) || empty($data['new_password']) || empty($data['username'])) {
            return json_encode(['status' => 'error', 'message' => 'All fields are required']);
        }

        // Проверка пользователя в базе данных
        $query = 'SELECT * FROM "User" WHERE login = :username';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($data['current_password'], $user['password_hash'])) {
            return json_encode(['status' => 'error', 'message' => 'Invalid current password']);
        }

        // Хеширование нового пароля
        $newPasswordHash = password_hash($data['new_password'], PASSWORD_DEFAULT);

        // Обновление пароля в базе данных
        $updateQuery = 'UPDATE "User" SET password_hash = :new_password WHERE login = :username';
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(':new_password', $newPasswordHash);
        $updateStmt->bindParam(':username', $data['username']);

        try {
            $updateStmt->execute();
            return json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
        } catch (\PDOException $e) {
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Устанавливает путь для сохранения сессий в зависимости от ОС
     */
    private function setSessionSavePath() {
        if (defined('PHP_OS_FAMILY')) {
            // PHP 7.2 и выше
            switch (PHP_OS_FAMILY) {
                case 'Windows':
                    $path = 'C:\\Windows\\Temp';
                    break;
                case 'Linux':
                case 'Darwin':
                default:
                    $path = '/tmp';
                    break;
            }
        } else {
            // Для PHP версий ниже 7.2
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $path = 'C:\\Windows\\Temp';
            } else {
                $path = '/tmp'; // Исправлено с '\\tmp' на '/tmp'
            }
        }

        // Логирование выбранного пути
        error_log("Setting session save path to: $path");

        // Проверка существования директории
        if (!is_dir($path)) {
            // Если директория не существует, попытаться создать её
            if (!mkdir($path, 0777, true)) {
                error_log("Не удалось создать директорию для сессий: $path");
                // Можно выбрать другую директорию или обработать ошибку иначе
                $path = sys_get_temp_dir(); // Использовать системную временную директорию
                error_log("Используем системную временную директорию: $path");
            } else {
                error_log("Директория для сессий успешно создана: $path");
            }
        } else {
            error_log("Директория для сессий существует: $path");
        }

        // Установка пути для сохранения сессий
        session_save_path($path);

        // Логирование текущего пути
        error_log("Текущий session.save_path: " . session_save_path());
    }
}

?>

<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Config\Database;

class AuthController
{
    private $conn;
    private $sessionTimeout = 50;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();

        // Настройка CORS
        $allowed_origins = [
            "http://localhost:3000",
        ];

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        if (in_array($origin, $allowed_origins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
            header("Access-Control-Max-Age: 86400");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        header('Content-Type: application/json');

        $secure = false;
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        $this->setSessionSavePath();

        session_start();

        $currentRoute = $_SERVER['REQUEST_URI'] ?? '';
        if(strpos($currentRoute, '/auth/login') === false && strpos($currentRoute, '/auth/register') === false) {
            $this-> checkSessionTimeout();
        }
    }

    private function checkSessionTimeout() {
        if (isset($_SESSION['last_activity'])) {
            $inactive = time() - $_SESSION['last_activity'];
            if ($inactive > $this->sessionTimeout) {
                $this->forceLogout();
                echo json_encode(['status' => 'error', 'message' => 'Session timed out. Please log in again.']);
                http_response_code(401);
                exit();
            } else {
                $_SESSION['last_activity'] = time();
            }
        } else {
            if (isset($_SESSION['user'])) {
                $_SESSION['last_activity'] = time();
            }
        }
    }

    private function forceLogout() {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"] ?? '',
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }

    // ----------------------------------- CSRF ---------------------------------------
    private function checkCsrfToken() {
        $headers = getallheaders();
        $clientToken = $headers['X-CSRF-Token'] ?? '';

        if (!isset($_SESSION['csrf_token']) || $clientToken !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
            exit();
        }
    }
    // ------------------------------------------------------------------------------

    public function register($data)
    {
        $requiredFields = ['email', 'username', 'phone', 'name', 'surname', 'gender', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return json_encode(['status' => 'error', 'message' => "Field $field is required"]);
            }
        }

        $username = filter_var($data['username'], FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL) ? $data['email'] : null;
        $phone = filter_var($data['phone'], FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_var($data['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $surname = filter_var($data['surname'], FILTER_SANITIZE_SPECIAL_CHARS);
        $gender = filter_var($data['gender'], FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$email) {
            return json_encode(['status' => 'error', 'message' => 'Invalid email format']);
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        $query = 'INSERT INTO "User" (login, email, password_hash, first_name, last_name, phone, gender, role, created_at)
                  VALUES (:username, :email, :password_hash, :name, :surname, :phone, :gender, :role, NOW())';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password_hash', $hashedPassword);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':surname', $data['surname']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindValue(':role', 'regular');

        try {
            $stmt->execute();
            return json_encode(['status' => 'success', 'message' => 'User registered successfully']);
        } catch (\PDOException $e) {
            return json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function login($data)
    {
        if (empty($data['username']) || empty($data['password'])) {
            error_log('Login attempt with missing fields: ' . json_encode($data));
            return json_encode(['status' => 'error', 'message' => 'Username and password are required']);
        }

        $username = filter_var($data['username'], FILTER_SANITIZE_SPECIAL_CHARS);

        $query = 'SELECT * FROM "User" WHERE login = :username';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user) {
            error_log("Login attempt failed: Invalid username '{$data['username']}'");
            return json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }

        if (!password_verify($data['password'], $user['password_hash'])) {
            error_log("Login attempt failed: Invalid password for username '{$data['username']}'");
            return json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }

        $_SESSION['user'] = [
            'username' => $user['login'],
            'email' => $user['email'],
            'name' => $user['first_name'],
            'surname' => $user['last_name'],
            'role' => $user['role']
        ];

        $_SESSION['last_activity'] = time();

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        session_regenerate_id(true);
        

        error_log("User '{$user['login']}' logged in successfully");

        return json_encode([
            'status' => 'success',
            'message' => 'Login successful',
            'user' => $_SESSION['user'],
            'csrf_token' => $_SESSION['csrf_token']
        ]);
    }

    public function check()
    {
        if (isset($_SESSION['user'])) {
            error_log("Auth check successful for user '{$_SESSION['user']['username']}'");
            return json_encode(['status' => 'success', 'user' => $_SESSION['user']]);
        }

        error_log("Auth check failed: Not authenticated");
        return json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    }

    public function logout() {
        $this->forceLogout();
        error_log("User logged out successfully");
        return json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
    }

    public function changePassword($data)
    {
        $this->checkCsrfToken();

        if (empty($data['current_password']) || empty($data['new_password']) || empty($data['username'])) {
            return json_encode(['status' => 'error', 'message' => 'All fields are required']);
        }

        $username = filter_var($data['username'], FILTER_SANITIZE_SPECIAL_CHARS);

        $query = 'SELECT * FROM "User" WHERE login = :username';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$user || !password_verify($data['current_password'], $user['password_hash'])) {
            return json_encode(['status' => 'error', 'message' => 'Invalid current password']);
        }

        $newPasswordHash = password_hash($data['new_password'], PASSWORD_DEFAULT);

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

    private function setSessionSavePath()
    {
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
                $path = '/tmp';
            }
        }

        // Логирование выбранного пути
        error_log("Setting session save path to: $path");

        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true)) {
                error_log("Не удалось создать директорию для сессий: $path");
                $path = sys_get_temp_dir();
                error_log("Используем системную временную директорию: $path");
            } else {
                error_log("Директория для сессий успешно создана: $path");
            }
        } else {
            error_log("Директория для сессий существует: $path");
        }

        session_save_path($path);

        error_log("Текущий session.save_path: " . session_save_path());
    }
}

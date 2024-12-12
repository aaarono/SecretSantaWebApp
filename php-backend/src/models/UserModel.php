<?php

namespace Secret\Santa\Models;

use Secret\Santa\Config\Database;
use PDO;

class UserModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllUsers() {
        $query = 'SELECT login, name, email FROM "User"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserImage($userId, $imageData) {
        try {
            // Обновляем картинку
            $query = 'UPDATE "User" SET profile_photo = :image WHERE login = :user_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR); // Исправлено на PDO::PARAM_STR
            $stmt->execute();
    
            // Получаем обновлённую картинку
            $query = 'SELECT profile_photo FROM "User" WHERE login = :user_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR); // Исправлено на PDO::PARAM_STR
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($result && $result['profile_photo']) {
                return $result['profile_photo']; // Возвращаем картинку
            } else {
                return null; // Картинка не найдена
            }
        } catch (\PDOException $e) {
            error_log('Failed to update user image: ' . $e->getMessage());
            return null; // Обрабатываем ошибку
        }
    }
    
    public function getUserImage($userId) {
        $query = 'SELECT profile_photo FROM "User" WHERE login = :user_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['profile_photo'] : null;
    }

    public function deleteUserImage($userId) {
        $query = 'UPDATE "User" SET profile_photo = NULL WHERE login = :user_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

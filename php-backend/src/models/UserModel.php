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
        $query = 'SELECT login, email, first_name AS firstName, last_name AS lastName, phone, gender, role, created_at AS createdAt, updated_at AS updatedAt, language FROM "User"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserImage($userId, $imageData) {
        try {
            $query = 'UPDATE "User" SET profile_photo = :image WHERE login = :user_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':image', $imageData, PDO::PARAM_LOB);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $stmt->execute();

            $query = 'SELECT profile_photo FROM "User" WHERE login = :user_id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result && $result['profile_photo']) {
                return $result['profile_photo'];
            } else {
                return null;
            }
        } catch (\PDOException $e) {
            error_log('Failed to update user image: ' . $e->getMessage());
            return null;
        }
    }

    public function getUserImage($userId) {
        $query = 'SELECT profile_photo FROM "User" WHERE login = :user_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['profile_photo'] : null;
    }

    public function deleteUserImage($userId) {
        $query = 'UPDATE "User" SET profile_photo = NULL WHERE login = :user_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getUserData($login)
{
    $query = "SELECT first_name AS firstName, last_name AS lastName, phone AS phoneNumber, email, gender FROM \"User\" WHERE login = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$login]);
    $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $userData;
}

    public function updateUserData($userId, $data) {
        $query = 'UPDATE "User" SET first_name = :first_name, last_name = :last_name, phone = :phone, email = :email, gender = :gender, updated_at = NOW() WHERE login = :user_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':first_name', $data['firstName'], PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $data['lastName'], PDO::PARAM_STR);
        $stmt->bindParam(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':gender', $data['gender'], PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
        return $stmt->execute();
    }
}
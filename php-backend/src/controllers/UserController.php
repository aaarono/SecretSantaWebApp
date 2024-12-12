<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\UserModel;

class UserController {
    private $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    private function getUserIdFromSession($userId) {
        // Если userId не передан, берём из сессии
        if (empty($userId)) {
            session_start();
            if (!isset($_SESSION['user']['username'])) {
                return null;
            }
            $userId = $_SESSION['user']['username'];
        }
        return $userId;
    }

    public function getAllUsers() {
        return json_encode($this->model->getAllUsers());
    }

    public function updateUserImage($userId, $file) {
        $userId = $this->getUserIdFromSession($userId);
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        // Проверка загруженного файла
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return json_encode(['status' => 'error', 'message' => 'File upload error']);
        }
    
        // Проверка MIME-типа
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid file type']);
        }
    
        // Чтение файла в бинарный формат
        $fileData = file_get_contents($file['tmp_name']);
    
        // Обновление изображения в базе данных
        $imageData = $this->model->updateUserImage($userId, $fileData);
    
        if ($imageData) {
            if (is_resource($imageData)) {
                $imageData = stream_get_contents($imageData);
            }
            // Возвращаем успешный ответ с изображением
            $imageBase64 = base64_encode($imageData); // Кодируем изображение в Base64
            return json_encode([
                'status' => 'success',
                'message' => 'Image updated successfully',
                'image' => 'data:image/jpeg;base64,' . $imageBase64 // Добавляем изображение в формате Base64
            ]);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update image']);
        }
    }
    

    public function getUserImage($userId) {
        $userId = $this->getUserIdFromSession($userId);
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        $imageData = $this->model->getUserImage($userId);
    
        if ($imageData) {
            // Преобразование изображения в Base64
            if (is_resource($imageData)) {
                $imageData = stream_get_contents($imageData);
            }
    
            $imageBase64 = base64_encode($imageData);
            return json_encode([
                'status' => 'success',
                'image' => "data:image/jpeg;base64,$imageBase64"
            ]);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Image not found']);
        }
    }    
    

    public function deleteUserImage($userId) {
        $userId = $this->getUserIdFromSession($userId);
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $deleteSuccess = $this->model->deleteUserImage($userId);

        if ($deleteSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Image deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete image']);
        }
    }
}

<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\UserModel;

class UserController
{
    private $model;

    public function __construct()
    {
        $this->model = new UserModel();
    }

    private function checkCsrfToken()
    {
        $headers = getallheaders();
        $clientToken = $headers['X-CSRF-Token'] ?? '';

        if (!isset($_SESSION['csrf_token']) || $clientToken !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
            exit();
        }
    }

    private function getUserIdFromSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']['username'])) {
            return null;
        }
        return $_SESSION['user']['username'];
    }

    public function getAllUsers()
    {
        return json_encode($this->model->getAllUsers());
    }

    public function updateUserImage($login, $file)
    {
        $this->checkCsrfToken();
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }

        if (!$login) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return json_encode(['status' => 'error', 'message' => 'File upload error']);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowedTypes)) {
            return json_encode(['status' => 'error', 'message' => 'Invalid file type']);
        }

        $fileData = file_get_contents($file['tmp_name']);

        $imageData = $this->model->updateUserImage($login, $fileData);

        if ($imageData) {
            if (is_resource($imageData)) {
                $imageData = stream_get_contents($imageData);
            }
            $imageBase64 = base64_encode($imageData);
            return json_encode([
                'status' => 'success',
                'message' => 'Image updated successfully',
                'image' => 'data:image/jpeg;base64,' . $imageBase64
            ]);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update image']);
        }
    }

    public function getUserImage($login)
    {
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }

        if (!$login) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $imageData = $this->model->getUserImage($login);

        if ($imageData) {
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

    public function deleteUserImage($login)
    {
        $this->checkCsrfToken();
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }

        if (!$login) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $deleteSuccess = $this->model->deleteUserImage($login);

        if ($deleteSuccess) {
            return json_encode(['status' => 'success', 'message' => 'Image deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete image']);
        }
    }

    public function getUserData()
    {
        $login = $this->getUserIdFromSession();
        error_log("User login: $login");

        if (!$login) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            return;
        }

        $userData = $this->model->getUserData($login);
        error_log('User data from model: ' . print_r($userData, true));

        if ($userData) {
            echo json_encode(['status' => 'success', 'data' => $userData]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to retrieve user data']);
        }
    }

    public function updateUserData($data)
    {
        $this->checkCsrfToken();
        error_log('CSRF token verified');

        $login = $this->getUserIdFromSession();
        error_log("User: $login");

        if (!$login) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            return;
        }

        // Validate and sanitize data
        $sanitizedData = [
            'firstName'   => filter_var($data['firstName'] ?? '', FILTER_SANITIZE_STRING),
            'lastName'    => filter_var($data['lastName'] ?? '', FILTER_SANITIZE_STRING),
            'phoneNumber' => filter_var($data['phoneNumber'] ?? '', FILTER_SANITIZE_STRING),
            'email'       => filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL),
            'gender'      => filter_var($data['gender'] ?? '', FILTER_SANITIZE_STRING)
        ];

        error_log('Sanitized data: ' . print_r($sanitizedData, true));

        if (!$sanitizedData['email']) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
            return;
        }

        $updateSuccess = $this->model->updateUserData($login, $sanitizedData);

        if ($updateSuccess) {
            echo json_encode(['status' => 'success', 'message' => 'User data updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update user data']);
        }
    }
}
<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\SmsModel;

class SmsController
{
    private $model;

    public function __construct()
    {
        $this->model = new SmsModel();
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

    private function getUserLoginFromSession()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user']['login'] ?? null;
    }

    public function getAllSms($gameId)
    {
        $sms = $this->model->getAllSmsByGameId($gameId);
        
        if (php_sapi_name() === 'cli') {
            return $sms;
        } else {
            echo json_encode(['status' => 'success', 'messages' => $sms]);
        }
    }

    public function getSms($id)
    {
        $sms = $this->model->getSmsById($id);
        if ($sms) {
            echo json_encode(['status' => 'success', 'sms' => $sms]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'SMS not found']);
        }
    }

    public function createSms($gameId, $message, $login = null)
    {
        if (!$login) {
            $login = $this->getUserLoginFromSession();
        }

        if (!$login) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            return;
        }

        $success = $this->model->createSms($gameId, $login, $message);

        if ($success) {
            if (php_sapi_name() !== 'cli') {
                echo json_encode(['status' => 'success', 'message' => 'SMS created']);
            }
        } else {
            if (php_sapi_name() !== 'cli') {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create SMS']);
            }
        }
    }

    public function updateSms($id, $message)
    {
        $this->checkCsrfToken();
        $login = $this->getUserLoginFromSession();
        if (!$login) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            return;
        }

        $sms = $this->model->getSmsById($id);
        if (!$sms) {
            echo json_encode(['status' => 'error', 'message' => 'SMS not found']);
            return;
        }

        if ($sms['login'] !== $login) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $success = $this->model->updateSms($id, $message);
        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'SMS updated']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update SMS']);
        }
    }

    public function deleteSms($id)
    {
        $this->checkCsrfToken();
        $login = $this->getUserLoginFromSession();
        if (!$login) {
            echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
            return;
        }

        $sms = $this->model->getSmsById($id);
        if (!$sms) {
            echo json_encode(['status' => 'error', 'message' => 'SMS not found']);
            return;
        }

        if ($sms['login'] !== $login) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }

        $success = $this->model->deleteSms($id);
        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'SMS deleted']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete SMS']);
        }
    }
}
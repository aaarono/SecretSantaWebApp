<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\GameModel;
use Secret\Santa\websockets\WebSocketBroadcaster;

class GameController {
    private $model;
    private $playerGameController;

    public function __construct() {
        $this->model = new GameModel();
        $this->playerGameController = new PlayerGameController();
    }

    private function checkCsrfToken() {
        $headers = getallheaders();
        $clientToken = $headers['X-CSRF-Token'] ?? '';
    
        if (!isset($_SESSION['csrf_token']) || $clientToken !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']);
            exit();
        }
    }

    private function getUserIdFromSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user']['username'])) {
            return null;
        }
        return $_SESSION['user']['username'];
    }

    public function getAllGames() {
        return json_encode($this->model->getAllGames());
    }

    public function getGameById($uuid) {
        $game = $this->model->getGameById($uuid);
        if ($game) {
            return json_encode(['status' => 'success', 'game' => $game]);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Game not found']);
        }
    }

    private function generateUuid() {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    public function createGame($name, $description, $budget, $endsAt) {
        $this->checkCsrfToken();
        $userId = $this->getUserIdFromSession();
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        $uuid = $this->generateUuid();
    
        $success = $this->model->createGame($uuid, $name, $description, $budget, $endsAt, $userId);
        if ($success) {
            $playerSuccess = $this->playerGameController->addPlayerToGame($userId, $uuid);
            $response = json_decode($playerSuccess, true);
    
            if ($response['status'] === 'success') {
                // После того, как пользователь добавлен в игру в БД, добавляем его соединения в лобби по WebSocket:
                // WebSocketBroadcaster::getInstance()->joinUserToGame($userId, $uuid);
    
                // // Можно оповестить всех в лобби (сейчас кроме создателя никого нет) о создании игры:
                // WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
                //     'type' => 'game_created',
                //     'uuid' => $uuid,
                //     'creator' => $userId,
                // ]);
    
                return json_encode(['status' => 'success', 'message' => 'Game created', 'uuid' => $uuid]);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Game created, but failed to add player']);
            }
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create game']);
        }
    }
    public function updateGame($uuid, $name, $description, $budget, $endsAt, $status) {
        $this->checkCsrfToken();
        $success = $this->model->updateGame($uuid, $name, $description, $budget, $endsAt, $status);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Game updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update game']);
        }
    }

    public function deleteGame($uuid) {
        $this->checkCsrfToken();
        $success = $this->model->deleteGame($uuid);
        if ($success) {
            // Оповестим только тех, кто в этом лобби
            // \Secret\Santa\WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
            //     'type' => 'game_deleted',
            //     'uuid' => $uuid
            // ]);
            return json_encode(['status' => 'success', 'message' => 'Game deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete game']);
        }
    }

    public function startGame($uuid) {
        $this->checkCsrfToken();
        $userId = $this->getUserIdFromSession();
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
        
        $game = $this->model->getGameById($uuid);
        if (!$game) {
            return json_encode(['status' => 'error', 'message' => 'Game not found']);
        }

        if ($game['creator_login'] !== $userId) {
            return json_encode(['status' => 'error', 'message' => 'Only the creator can start the game']);
        }

        $success = $this->model->startGame($uuid);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Game started successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to start game']);
        }
    }

}

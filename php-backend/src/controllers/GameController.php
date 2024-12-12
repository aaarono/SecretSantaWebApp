<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\GameModel;

class GameController {
    private $model;
    private $playerGameController;

    public function __construct() {
        $this->model = new GameModel();
        $this->playerGameController = new PlayerGameController();
    }

    private function getUserIdFromSession() {
        session_start();
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
        $userId = $this->getUserIdFromSession();
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $uuid = $this->generateUuid();

        $success = $this->model->createGame($uuid, $name, $description, $budget, $endsAt);
        if ($success) {
            $playerSuccess = $this->playerGameController->addPlayerToGame($userId, $uuid, $userId);
            if ($playerSuccess) {
                return json_encode(['status' => 'success', 'message' => 'Game created and author added', 'uuid' => $uuid]);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Game created, but failed to add author to Player_Game']);
            }
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to create game']);
        }
    }

    public function updateGame($uuid, $name, $description, $budget, $endsAt, $status) {
        $success = $this->model->updateGame($uuid, $name, $description, $budget, $endsAt, $status);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Game updated successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to update game']);
        }
    }

    public function deleteGame($uuid) {
        $success = $this->model->deleteGame($uuid);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Game deleted successfully']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to delete game']);
        }
    }

    public function startGame($uuid) {
        $userId = $this->getUserIdFromSession();
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
        
        $game = $this->model->getGameById($uuid);
        if (!$game) {
            return json_encode(['status' => 'error', 'message' => 'Game not found']);
        }

        if (!$this->playerGameController->isUserCreator($uuid, $userId)) {
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

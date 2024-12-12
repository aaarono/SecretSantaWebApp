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

    public function createGame($uuid, $name, $description, $budget, $endsAt) {
        $userId = $this->getUserIdFromSession();
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'Користувач не автентифікований']);
        }

        $success = $this->model->createGame($uuid, $name, $description, $budget, $endsAt);
        if ($success) {
            $playerSuccess = $this->playerGameController->addPlayerToGame($userId, $uuid, $userId);
            if ($playerSuccess) {
                return json_encode(['status' => 'success', 'message' => 'Гру створено та додано автора']);
            } else {
                return json_encode(['status' => 'error', 'message' => 'Гру створено, але не вдалося додати автора до Player_Game']);
            }
        } else {
            return json_encode(['status' => 'error', 'message' => 'Не вдалося створити гру']);
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
}
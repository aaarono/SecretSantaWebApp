<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\PlayerGameModel;

class PlayerGameController {
    private $model;

    public function __construct() {
        $this->model = new PlayerGameModel();
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

    public function addPlayerToGame($login, $uuid) {
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
    
        if ($login === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $success = $this->model->addPlayerToGame($login, $uuid);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Player added to the game']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to add player to the game']);
        }
    }

    public function getPlayersByGameId($uuid) {
        $players = $this->model->getPlayersByGameId($uuid);
        return json_encode(['status' => 'success', 'players' => $players]);
    }

    /*
    public function isUserCreator($uuid, $login) {
        // Якщо все ж потрібна, треба через GameModel:
        $game = (new \Secret\Santa\Models\GameModel())->getGameById($uuid);
        if (!$game) {
            return json_encode(['status' => 'error', 'message' => 'Game not found']);
        }
        
        return json_encode(['status' => 'success', 'is_creator' => ($game['creator_login'] === $login)]);
    }
    */

    public function removePlayerFromGame($uuid, $login) {
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
    
        if ($login === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $success = $this->model->removePlayerFromGame($uuid, $login);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Player removed from the game']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to remove player from the game']);
        }
    }
}

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

    public function addPlayerToGame($login, $uuid, $creatorLogin) {
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
    
        if ($login === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $success = $this->model->addPlayerToGame($login, $uuid, $creatorLogin);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Гравець доданий до гри']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Не вдалося додати гравця до гри']);
        }
    }

    public function getPlayersByGameId($uuid) {
        $players = $this->model->getPlayersByGameId($uuid);
        return json_encode(['status' => 'success', 'players' => $players]);
    }

    public function isUserCreator($uuid, $login) {
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
    
        if ($login === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        $isCreator = $this->model->isUserCreator($uuid, $login);
        return json_encode(['status' => 'success', 'is_creator' => $isCreator]);
    }
    
    public function removePlayerFromGame($uuid, $login)
    {
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
    
        if ($login === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }

        $success = $this->model->removePlayerFromGame($uuid, $login);
        if ($success) {
            return json_encode(['status' => 'success', 'message' => 'Гравець видалений з гри']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Не вдалося видалити гравця з гри']);
        }
    }
}
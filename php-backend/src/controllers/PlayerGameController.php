<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\PlayerGameModel;

class PlayerGameController {
    private $model;

    public function __construct() {
        $this->model = new PlayerGameModel();
    }

    public function addPlayerToGame($login, $uuid, $creatorLogin = null) {
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

}
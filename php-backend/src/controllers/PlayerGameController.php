<?php

namespace Secret\Santa\Controllers;

use Secret\Santa\Models\PlayerGameModel;
use Secret\Santa\websockets\WebSocketBroadcaster;

class PlayerGameController {
    private $model;

    public function __construct() {
        $this->model = new PlayerGameModel();
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

    public function getUserGames($login = null) {
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
        
        if (!$login) {
            http_response_code(401);
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        $games = $this->model->getGamesByLogin($login);
    
        return json_encode(['status' => 'success', 'games' => $games]);
    }    

    public function addPlayerToGame($login, $uuid) {
        $this->checkCsrfToken();
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
    
        if ($login === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        $success = $this->model->addPlayerToGame($login, $uuid);
        if ($success) {
            // // Оповестим всех в данном лобби
            // WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
            //     'type' => 'player_joined',
            //     'game_uuid' => $uuid,
            //     'login' => $login
            // ]);
    
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
        $game = (new \Secret\Santa\Models\GameModel())->getGameById($uuid);
        if (!$game) {
            return json_encode(['status' => 'error', 'message' => 'Game not found']);
        }
        
        return json_encode(['status' => 'success', 'is_creator' => ($game['creator_login'] === $login)]);
    }
    */

    public function removePlayerFromGame($uuid, $login) {
        $this->checkCsrfToken();
        if ($login === null) {
            $login = $this->getUserIdFromSession();
        }
    
        if ($login === null) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        $success = $this->model->removePlayerFromGame($uuid, $login);
        if ($success) {
            // // Оповестим всех в данном лобби
            // WebSocketBroadcaster::getInstance()->broadcastToGame($uuid, [
            //     'type' => 'player_left',
            //     'game_uuid' => $uuid,
            //     'login' => $login
            // ]);
    
            return json_encode(['status' => 'success', 'message' => 'Player removed from the game']);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Failed to remove player from the game']);
        }
    }
    
    public function markGiftPresented($uuid, $receiver) {
        $this->checkCsrfToken();
        $userId = $this->getUserIdFromSession();
        if (!$userId) {
            return json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        }
    
        // Обновляем статус подарка для receiver
        $updated = $this->model->updatePlayerGame($receiver, $uuid, true);
        if (!$updated) {
            return json_encode(['status' => 'error', 'message' => 'Failed to update gift status']);
        }
    
        // Проверяем, есть ли еще непреподнесённые подарки
        $notGiftedCount = $this->model->countNotGiftedInGame($uuid);
    
        $gameEnded = false;
    
        if ($notGiftedCount === 0) {
            // Все подарки подарены, завершаем игру
            $gameModel = new \Secret\Santa\Models\GameModel();
            $gameModel->updateGameStatus($uuid, 'ended');
            $gameEnded = true;
        }
    
        return json_encode(['status' => 'success', 'game_ended' => $gameEnded]);
    }    
}

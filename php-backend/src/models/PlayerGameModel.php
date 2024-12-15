<?php

namespace Secret\Santa\Models;

use Secret\Santa\Config\Database;
use PDO;

class PlayerGameModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

        
    public function getGamesByLogin($login) {
        $query = 'SELECT g.* FROM "Player_Game" pg 
                  JOIN "Game" g ON pg.uuid = g.uuid
                  WHERE pg.login = :login';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePlayerGame($login, $uuid, $isGifted)
    {
        $query = 'UPDATE "Player_Game" SET is_gifted = :isGifted WHERE login = :login AND UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':isGifted', $isGifted, PDO::PARAM_BOOL);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function addPlayerToGame($login, $uuid)
    {
        $query = 'INSERT INTO "Player_Game" (login, UUID) VALUES (:login, :uuid)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':uuid', $uuid);
        return $stmt->execute();
    }

    public function getPlayersByGameId($uuid)
    {
        $query = 'SELECT login FROM "Player_Game" WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function removePlayerFromGame($uuid, $login)
    {
        $query = 'DELETE FROM "Player_Game" WHERE login = :login AND UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':uuid', $uuid);
        return $stmt->execute();
    }

    public function getAllPlayerGame () {
        $query = 'SELECT * FROM "Player_Game"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

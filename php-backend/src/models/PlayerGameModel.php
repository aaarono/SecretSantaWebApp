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
}

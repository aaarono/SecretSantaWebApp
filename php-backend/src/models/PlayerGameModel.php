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

    public function addPlayerToGame($login, $uuid, $creatorLogin = null)
    {
        $query = 'INSERT INTO "Player_Game" (login, UUID, is_creator) VALUES (:login, :uuid, :is_creator)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':is_creator', $creatorLogin);
        return $stmt->execute();
    }

    public function getPlayersByGameId($uuid)
    {
        $query = 'SELECT login, is_creator FROM "Player_Game" WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isUserCreator($uuid, $userId)
    {
        $query = 'SELECT 1 FROM "Player_Game" WHERE UUID = :uuid AND login = :userId AND is_creator = :userId LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchColumn() !== false;
    }

}
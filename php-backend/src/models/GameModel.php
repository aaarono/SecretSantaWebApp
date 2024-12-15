<?php

namespace Secret\Santa\Models;

use Secret\Santa\Config\Database;
use PDO;

class GameModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllGames()
    {
        $query = 'SELECT * FROM "Game"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGameById($uuid)
    {
        $query = 'SELECT * FROM "Game" WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createGame($uuid, $name, $description, $budget, $endsAt, $creatorLogin, $status = 'pending')
    {
        $query = 'INSERT INTO "Game" (UUID, Name, Description, Budget, EndsAt, Status, creator_login) 
                  VALUES (:uuid, :name, :description, :budget, :endsAt, :status, :creator_login)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':endsAt', $endsAt);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':creator_login', $creatorLogin);
        return $stmt->execute();
    }

    public function updateGame($uuid, $name, $description, $budget, $endsAt, $status)
    {
        $query = 'UPDATE "Game" 
                  SET Name = :name, Description = :description, Budget = :budget, EndsAt = :endsAt, Status = :status
                  WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':endsAt', $endsAt);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteGame($uuid)
    {
        $query = 'DELETE FROM "Game" WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function startGame($uuid)
    {
        $query = 'UPDATE "Game" SET Status = :status WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $status = 'running';
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getGameCreator($creatorLogin, $uuid)
    {
        $query = 'SELECT 1 FROM "Game" WHERE creator_login = :creator_login AND UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':creator_login', $creatorLogin, PDO::PARAM_STR);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

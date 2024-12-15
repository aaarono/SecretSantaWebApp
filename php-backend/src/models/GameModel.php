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
        $query = '
            SELECT 
                g.UUID AS uuid,
                g.Name AS name,
                g.Description AS description,
                g.Budget AS budget,
                g.CreatedAt AS createdat,
                g.EndsAt AS endsat,
                g.Status AS status,
                g.creator_login,
                COALESCE(
                    jsonb_agg(
                        jsonb_build_object(
                            \'login\', pg.login,
                            \'is_gifted\', pg.is_gifted
                        )
                    ) FILTER (WHERE pg.login IS NOT NULL), \'[]\'::jsonb
                ) AS players
            FROM 
                "Game" g
            LEFT JOIN 
                "Player_Game" pg ON g.UUID = pg.UUID
            WHERE 
                g.UUID = :uuid
            GROUP BY 
                g.UUID
        ';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Преобразуем поле "players" из строки JSON в массив
        if ($result && isset($result['players'])) {
            $result['players'] = json_decode($result['players'], true);
        }
    
        return $result;
    }
    
    public function createGame($uuid, $name, $description, $budget, $endsAt, $creatorLogin, $status = 'pending')
    {
        $endsAtFormatted = date('Y-m-d H:i:s', strtotime($endsAt));

        $query = 'INSERT INTO "Game" (UUID, Name, Description, Budget, EndsAt, Status, creator_login) 
                  VALUES (:uuid, :name, :description, :budget, :endsAt, :status, :creator_login)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':uuid', $uuid);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':endsAt', $endsAtFormatted);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':creator_login', $creatorLogin);
        return $stmt->execute();
    }

    public function updateGame($uuid, $name, $description, $budget, $endsAt, $status)
    {
        $endsAtFormatted = date('Y-m-d H:i:s', strtotime($endsAt));

        $query = 'UPDATE "Game" 
                  SET Name = :name, Description = :description, Budget = :budget, EndsAt = :endsAt, Status = :status
                  WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':budget', $budget);
        $stmt->bindParam(':endsAt', $endsAtFormatted);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':uuid', $uuid, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateGameStatus($uuid, $status)
    {
        $query = 'UPDATE "Game" SET Status = :status WHERE UUID = :uuid';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
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

<?php

namespace Secret\Santa\Models;

use Secret\Santa\Config\Database;
use PDO;

class PairModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createPair($gameId, $gifterId, $receiverId)
    {
        $query = 'INSERT INTO "Pair" (game_id, gifter_id, receiver_id) VALUES (:game_id, :gifter_id, :receiver_id)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':game_id', $gameId);
        $stmt->bindParam(':gifter_id', $gifterId);
        $stmt->bindParam(':receiver_id', $receiverId);
        return $stmt->execute();
    }

    public function getPairsByGameId($gameId)
    {
        $query = 'SELECT * FROM "Pair" WHERE game_id = :game_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':game_id', $gameId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPairById($pairId)
    {
        $query = 'SELECT * FROM "Pair" WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $pairId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deletePair($pairId)
    {
        $query = 'DELETE FROM "Pair" WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $pairId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPlayersByGameId($gameId)
    {
        $query = 'SELECT login FROM "Player_Game" WHERE UUID = :game_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':game_id', $gameId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function deletePairsByGameId($gameId)
    {
        $query = 'DELETE FROM "Pair" WHERE game_id = :game_id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':game_id', $gameId);
        return $stmt->execute();
    }
}

?>
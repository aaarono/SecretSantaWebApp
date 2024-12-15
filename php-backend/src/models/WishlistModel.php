<?php

namespace Secret\Santa\Models;

use Secret\Santa\Config\Database;
use PDO;


class WishlistModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllWishlists()
    {
        $query = 'SELECT * FROM "Wishlist"';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWishlistById($id)
    {
        $query = 'SELECT * FROM "Wishlist" WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createWishlist($name, $description, $url, $login)
    {
        $query = 'INSERT INTO "Wishlist" (Name, Description, URL, Login) VALUES (:name, :description, :url, :login)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':login', $login);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Возвращаем ID последней вставленной записи
        } else {
            return false;
        }
    }
    
    public function updateWishlist($id, $name, $description, $url, $login)
    {
        $query = 'UPDATE "Wishlist" SET Name = :name, Description = :description, URL = :url, Login = :login WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':login', $login);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteWishlist($id)
{
    $query = 'DELETE FROM "Wishlist" WHERE id = :id';
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        error_log('Error executing DELETE query: ' . implode(', ', $stmt->errorInfo()));
        return false;
    }

    return true;
}

    public function getAllWishlistsByUserId($userId)
    {
        $query = 'SELECT * FROM "Wishlist" WHERE Login = :login';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $userId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

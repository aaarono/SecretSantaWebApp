<?php
class Wishlist {
    private $conn;
    private $table = '"Wishlist"';

    // Properties
    public $id;
    public $name;
    public $description;
    public $url;
    public $login;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new wishlist item
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' (name, description, url, login)
                  VALUES (:name, :description, :url, :login) RETURNING id';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->sanitize();

        // Binding parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':login', $this->login);

        if ($stmt->execute()) {
            $this->id = $stmt->fetchColumn();
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Read wishlist item by ID
    public function read() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 0,1';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->url = $row['url'];
            $this->login = $row['login'];
            return true;
        }

        return false;
    }

    // Update wishlist item
    public function update() {
        $query = 'UPDATE ' . $this->table . '
            SET name = :name, description = :description, url = :url
            WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->sanitize();

        // Binding parameters
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':url', $this->url);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Delete wishlist item
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Get all wishlist items for a user
    public function readAllByUser() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE login = :login ORDER BY id DESC';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':login', $this->login);

        $stmt->execute();

        return $stmt;
    }

    // Data sanitization
    private function sanitize() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->login = htmlspecialchars(strip_tags($this->login));
    }
}
?>

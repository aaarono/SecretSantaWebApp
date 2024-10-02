<?php
class Game {
    private $conn;
    private $table = '"Game"';

    // Game properties
    public $uuid;
    public $name;
    public $description;
    public $budget;
    public $theme;
    public $created_at;
    public $ends_at;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new game
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
            (name, description, budget, theme, ends_at, status)
            VALUES (:name, :description, :budget, :theme, :ends_at, :status)
            RETURNING uuid';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->sanitize();

        // Binding parameters
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':budget', $this->budget);
        $stmt->bindParam(':theme', $this->theme);
        $stmt->bindParam(':ends_at', $this->ends_at);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            $this->uuid = $stmt->fetchColumn();
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Read game by UUID
    public function read() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE uuid = :uuid LIMIT 0,1';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':uuid', $this->uuid);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->budget = $row['budget'];
            $this->theme = $row['theme'];
            $this->created_at = $row['created_at'];
            $this->ends_at = $row['ends_at'];
            $this->status = $row['status'];
            return true;
        }

        return false;
    }

    // Update game
    public function update() {
        $query = 'UPDATE ' . $this->table . '
            SET name = :name, description = :description, budget = :budget, theme = :theme, ends_at = :ends_at, status = :status
            WHERE uuid = :uuid';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->sanitize();

        // Binding parameters
        $stmt->bindParam(':uuid', $this->uuid);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':budget', $this->budget);
        $stmt->bindParam(':theme', $this->theme);
        $stmt->bindParam(':ends_at', $this->ends_at);
        $stmt->bindParam(':status', $this->status);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Delete game
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE uuid = :uuid';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':uuid', $this->uuid);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Get all games
    public function readAll() {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    // Data sanitization
    private function sanitize() {
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->budget = htmlspecialchars(strip_tags($this->budget));
        $this->theme = htmlspecialchars(strip_tags($this->theme));
        $this->ends_at = htmlspecialchars(strip_tags($this->ends_at));
        $this->status = htmlspecialchars(strip_tags($this->status));
    }
}
?>

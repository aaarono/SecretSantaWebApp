<?php
class PlayerGame {
    private $conn;
    private $table = '"Player_Game"';

    // Properties
    public $login;
    public $uuid;
    public $is_gifted;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Add a player to the game
    public function joinGame() {
        $query = 'INSERT INTO ' . $this->table . ' (login, uuid, is_gifted)
                  VALUES (:login, :uuid, :is_gifted)';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->uuid = htmlspecialchars(strip_tags($this->uuid));
        $this->is_gifted = $this->is_gifted ? true : false;

        // Binding parameters
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':uuid', $this->uuid);
        $stmt->bindParam(':is_gifted', $this->is_gifted, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Update gift status
    public function updateGiftStatus() {
        $query = 'UPDATE ' . $this->table . '
                  SET is_gifted = :is_gifted
                  WHERE login = :login AND uuid = :uuid';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->is_gifted = $this->is_gifted ? true : false;

        // Binding parameters
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':uuid', $this->uuid);
        $stmt->bindParam(':is_gifted', $this->is_gifted, PDO::PARAM_BOOL);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Check player's participation in the game
    public function checkParticipation() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE login = :login AND uuid = :uuid';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':uuid', $this->uuid);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->is_gifted = $row['is_gifted'];
            return true;
        }

        return false;
    }

    // Remove a player from the game
    public function leaveGame() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE login = :login AND uuid = :uuid';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':uuid', $this->uuid);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Get all players in the game
    public function getPlayersInGame() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE uuid = :uuid';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':uuid', $this->uuid);

        $stmt->execute();

        return $stmt;
    }
}
?>

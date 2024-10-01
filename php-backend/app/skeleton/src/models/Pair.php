<?php
class Pair {
    private $conn;
    private $table = '"Pair"';

    // Properties
    public $game_uuid;
    public $gifter_login;
    public $receiver_login;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a pair
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' (game_uuid, gifter_login, receiver_login)
                  VALUES (:game_uuid, :gifter_login, :receiver_login)';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->game_uuid = htmlspecialchars(strip_tags($this->game_uuid));
        $this->gifter_login = htmlspecialchars(strip_tags($this->gifter_login));
        $this->receiver_login = htmlspecialchars(strip_tags($this->receiver_login));

        // Binding parameters
        $stmt->bindParam(':game_uuid', $this->game_uuid);
        $stmt->bindParam(':gifter_login', $this->gifter_login);
        $stmt->bindParam(':receiver_login', $this->receiver_login);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Get receiver by gifter
    public function getReceiver() {
        $query = 'SELECT receiver_login FROM ' . $this->table . '
                  WHERE game_uuid = :game_uuid AND gifter_login = :gifter_login';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':game_uuid', $this->game_uuid);
        $stmt->bindParam(':gifter_login', $this->gifter_login);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->receiver_login = $row['receiver_login'];
            return true;
        }

        return false;
    }

    // Get gifter by receiver
    public function getGifter() {
        $query = 'SELECT gifter_login FROM ' . $this->table . '
                  WHERE game_uuid = :game_uuid AND receiver_login = :receiver_login';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':game_uuid', $this->game_uuid);
        $stmt->bindParam(':receiver_login', $this->receiver_login);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->gifter_login = $row['gifter_login'];
            return true;
        }

        return false;
    }

    // Get all pairs in the game
    public function getAllPairsInGame() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE game_uuid = :game_uuid';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':game_uuid', $this->game_uuid);

        $stmt->execute();

        return $stmt;
    }
}
?>

<?php
class User {
    private $conn;
    private $table = '"User"';

    // User properties
    public $login;
    public $email;
    public $password_hash;
    public $first_name;
    public $last_name;
    public $phone;
    public $gender;
    public $profile_photo;
    public $role;
    public $created_at;
    public $updated_at;
    public $language;
    public $theme;

    // Constructor with database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new user
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
            (login, email, password_hash, first_name, last_name, phone, gender, profile_photo, role, language, theme)
            VALUES (:login, :email, :password_hash, :first_name, :last_name, :phone, :gender, :profile_photo, :role, :language, :theme)';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->sanitize();

        // Binding parameters
        $this->bindParams($stmt);

        // Executing query
        if ($stmt->execute()) {
            return true;
        }

        // Outputting error
        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Read user by login
    public function read() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE login = :login LIMIT 0,1';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':login', $this->login);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Setting properties
            $this->email = $row['email'];
            $this->password_hash = $row['password_hash'];
            $this->first_name = $row['first_name'];
            $this->last_name = $row['last_name'];
            $this->phone = $row['phone'];
            $this->gender = $row['gender'];
            $this->profile_photo = $row['profile_photo'];
            $this->role = $row['role'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->language = $row['language'];
            $this->theme = $row['theme'];
            return true;
        }

        return false;
    }

    // Update user data
    public function update() {
        $query = 'UPDATE ' . $this->table . '
            SET email = :email, password_hash = :password_hash, first_name = :first_name, last_name = :last_name,
                phone = :phone, gender = :gender, profile_photo = :profile_photo, role = :role, language = :language, theme = :theme
            WHERE login = :login';

        $stmt = $this->conn->prepare($query);

        // Data sanitization
        $this->sanitize();

        // Binding parameters
        $this->bindParams($stmt);

        // Additionally binding login
        $stmt->bindParam(':login', $this->login);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Delete user
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE login = :login';

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':login', $this->login);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->errorInfo()[2]);
        return false;
    }

    // Get list of all users
    public function readAll() {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY created_at DESC';

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    // Helper methods

    // Data sanitization
    private function sanitize() {
        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password_hash = htmlspecialchars(strip_tags($this->password_hash));
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->role = htmlspecialchars(strip_tags($this->role));
        $this->language = htmlspecialchars(strip_tags($this->language));
        $this->theme = htmlspecialchars(strip_tags($this->theme));
    }

    // Binding parameters
    private function bindParams($stmt) {
        $stmt->bindParam(':login', $this->login);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password_hash', $this->password_hash);
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name', $this->last_name);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':profile_photo', $this->profile_photo, PDO::PARAM_LOB);
        $stmt->bindParam(':role', $this->role);
        $stmt->bindParam(':language', $this->language);
        $stmt->bindParam(':theme', $this->theme);
    }
}
?>

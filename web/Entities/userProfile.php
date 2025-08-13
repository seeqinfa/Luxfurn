<?php

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db_config.php';

class UserProfile {
    private $conn;
    private $table = 'users';

    public function __construct() {
        $this->initializeConnection();
    }

    private function initializeConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public function getAllProfiles() {
        $query = "SELECT username, email FROM " . $this->table . " ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addUserProfile($username, $email, $password) {
        if ($this->userProfileExists($username)) {
            throw new Exception("Username already exists. Please choose another.");
        }

        $query = "INSERT INTO " . $this->table . " (username, email, password)
                  VALUES (:username, :email, :password)";

        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $email = htmlspecialchars(strip_tags($email));
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Security

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);

        return $stmt->execute();
    }

    private function userProfileExists($username) {
        $query = "SELECT username FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function deleteUserProfile($username) {
        $query = "DELETE FROM " . $this->table . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        return $stmt->execute();
    }

    public function searchProfiles($criteria) {
        $query = "SELECT username, email FROM " . $this->table . " WHERE 1=1";
        $params = [];

        if (!empty($criteria['username'])) {
            $query .= " AND username LIKE :username";
            $params[':username'] = '%' . $criteria['username'] . '%';
        }

        if (!empty($criteria['email'])) {
            $query .= " AND email LIKE :email";
            $params[':email'] = '%' . $criteria['email'] . '%';
        }

        $query .= " ORDER BY username";

        try {
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search error: " . $e->getMessage());
            return false;
        }
    }
}
?>

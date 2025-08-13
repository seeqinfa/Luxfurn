<?php

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db_config.php';
require_once dirname(__DIR__) . '/db_connect.php';

class UserAccount {
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

    public function login($username, $password) {
        $query = "SELECT id, username, password, status, role
                  FROM " . $this->table . "
                  WHERE username = :username LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            throw new Exception("User not found");
        }

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ((int)$user['status'] === 0) {
            throw new Exception("Unable to login. Account is suspended.");
        }

        if (!password_verify($password, $user['password'])) {
            throw new Exception("Incorrect password. Please try again.");
        }

        return $user;
    }

    public function register($username, $password, $email) {
        if ($this->usernameExists($username)) {
            throw new Exception("Username is already taken. Please try again.");
        }

        $query = "INSERT INTO " . $this->table . "
                 (username, password, email, status, role)
                 VALUES (:username, :password, :email, 1, 'user')";

        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $password = password_hash($password, PASSWORD_DEFAULT);
        $email = !empty($email) ? htmlspecialchars(strip_tags($email)) : null;

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email, $email === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function update($id, $data) {
        $updateFields = [];
        $params = [];

        if (isset($data['email'])) {
            $updateFields[] = "email = :email";
            $params[':email'] = htmlspecialchars(strip_tags($data['email']));
        }

        if (isset($data['password'])) {
            $updateFields[] = "password = :password";
            $params[':password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        if (empty($updateFields)) return false;

        $query = "UPDATE " . $this->table . "
                  SET " . implode(", ", $updateFields) . "
                  WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update error: " . $e->getMessage());
            return false;
        }
    }

    public function suspendUser($id) {
        $query = "UPDATE " . $this->table . " SET status = 0 WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Suspension error: " . $e->getMessage());
            return false;
        }
    }

    public function activateUser($id) {
        $query = "UPDATE " . $this->table . " SET status = 1 WHERE id = :id";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Activation error: " . $e->getMessage());
            return false;
        }
    }

    public function listUsers() {
        $query = "SELECT id, username, email, status, role
                  FROM " . $this->table . "
                  ORDER BY username DESC";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("List users error: " . $e->getMessage());
            return false;
        }
    }

    public function searchUsers($criteria) {
        $query = "SELECT id, username, email, status, role
                  FROM " . $this->table . "
                  WHERE 1=1";
        $params = [];

        if (!empty($criteria['username'])) {
            $query .= " AND username LIKE :username";
            $params[':username'] = '%' . $criteria['username'] . '%';
        }

        if (!empty($criteria['email'])) {
            $query .= " AND email LIKE :email";
            $params[':email'] = '%' . $criteria['email'] . '%';
        }

        if (isset($criteria['status']) && $criteria['status'] !== '') {
            $query .= " AND status = :status";
            $params[':status'] = $criteria['status'];
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

    private function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}

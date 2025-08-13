<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/db_config.php';

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Check for empty fields
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['message'] = "All fields are required.";
        header("Location: " . BOUNDARY_URL . "/index.php");
        exit();
    }

    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Check for duplicate username or email
        $stmt = $conn->prepare("SELECT 1 FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Username or email already exists.";
            header("Location: " . BOUNDARY_URL . "/index.php");
            exit();
        }

        // Hash password and insert
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users (username, email, password, status, role)
            VALUES (:username, :email, :password, 1, 'user')
        ");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->execute();

        $_SESSION['message'] = "Account created successfully. Please log in.";
        header("Location: " . BOUNDARY_URL . "/index.php");
        exit();
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        $_SESSION['message'] = "Server error. Please try again later.";
        header("Location: " . BOUNDARY_URL . "/index.php");
        exit();
    }
} else {
    // Not a POST request
    header("Location: " . BOUNDARY_URL . "/index.php");
    exit();
}

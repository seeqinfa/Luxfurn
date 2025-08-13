<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}

require_once dirname(__DIR__) . '/config.php';
require_once ENTITIES_PATH . '/userAccount.php';

class LoginController {
    private $userAccount;

    public function __construct() {
        $this->userAccount = new UserAccount();
    }

    public function login($username, $password) {
        try {
            $user = $this->userAccount->login($username, $password);

            if ($user) {
                // Check if account is banned/suspended
                if ((int)$user['status'] === 0) {
                    $_SESSION['message'] = "Your account has been suspended.";
                    header("Location: " . BOUNDARY_URL . "/index.php");
                    exit();
                }

                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['is_logged_in'] = true;

                // Redirect based on role
                $this->redirectUser($user['role']);
                return true;
            }

        } catch (Exception $e) {
            $_SESSION['message'] = $e->getMessage();
            header("Location: " . BOUNDARY_URL . "/index.php");
            exit();
        }

        $_SESSION['message'] = "Invalid login attempt, please try again.";
        header("Location: " . BOUNDARY_URL . "/index.php");
        return false;
    }

    private function redirectUser($role) {
        $redirect = $redirectMap[$role] ?? BOUNDARY_URL . "/index.php";
        header("Location: $redirect");
        exit();
    }
}

// Process the login request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $controller = new LoginController();
    $controller->login($_POST['username'], $_POST['password']);
}

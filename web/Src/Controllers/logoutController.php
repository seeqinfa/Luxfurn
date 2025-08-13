<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}
require_once dirname(__DIR__) . '/config.php';
class LogoutController {

    public function __construct() {
        session_destroy();
        session_start();
        $_SESSION['message'] = "You have logged out successfully.";
        header("Location:". BOUNDARY_URL."/index.php");
        return false;

    }

}

// Process the login request
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $controller = new LogoutController();
}

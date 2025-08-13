<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Start session if not already started
}
require_once dirname(__DIR__) . '/config.php';
require_once ENTITIES_PATH . '/userProfile.php';

class ViewUserProfileController {
    private $userProfile;

    public function __construct() {
        $this->userProfile = new UserProfile();
    }

    // Get list of profiles
    public function listProfiles() {
        return $this->userProfile->getAllProfiles();
    }

 // Handle AJAX request
    public function handleAjaxRequest() {
        $responses = [
            'profiles' => $this->userProfile->getAllProfiles()
        ];

        header('Content-Type: application/json');
        echo json_encode($responses);
        exit();
    }
    }


if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
    $controller = new ViewUserProfileController();
    if (isset($_GET['ajax'])) {
        $controller->handleAjaxRequest();
    }
}

?>
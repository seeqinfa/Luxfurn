<?php
// Define the project root as the Src directory
define('PROJECT_ROOT', dirname(__FILE__));

// Define other important paths relative to PROJECT_ROOT
define('CONTROLLERS_PATH', PROJECT_ROOT . '\Controllers');
define('ENTITIES_PATH', PROJECT_ROOT . '\Entities');
define('BOUNDARY_PATH', PROJECT_ROOT . '\Boundary');



define('BASE_URL', '/FYP-25-S2-34-Chatbot/Src');

// Define other URL paths relative to BASE_URL
define('BOUNDARY_URL', BASE_URL . '/Boundary');
define('CONTROLLERS_URL',  BASE_URL . '/Controllers');
define('ADMIN_CONTROLLERS_URL',  BASE_URL . '/Controllers/admin');
define('IMAGE_PATH',  BASE_URL . '/img');
define('JAVASCRIPT_PATH',  BASE_URL . '/Javascripts');
define('CSS_PATH',  BASE_URL . '/CSS');

// Helper function for requiring files
function requireOnce($path) {
    $absolutePath = PROJECT_ROOT . '/' . ltrim($path, '/');
    if (!file_exists($absolutePath)) {
        throw new Exception("Required file not found: " . $absolutePath);
    }
    require_once $absolutePath;
}

// Error reporting (set to 0 in production)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dsn = 'mysql:host=localhost;dbname=luxfurn;charset=utf8mb4';
$user = 'root';            // ← adjust
$pass = '';                // ← adjust

$pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

?>
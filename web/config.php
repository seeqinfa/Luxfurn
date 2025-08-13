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

// Database configuration using environment variables
$host = getenv('MYSQLHOST') ?: '127.0.0.1';
$port = getenv('MYSQLPORT') ?: '3306';
$user = getenv('MYSQLUSER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') ?: '';
$db   = getenv('MYSQLDATABASE') ?: 'luxfurn';

// Create PDO connection
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die('PDO Connection failed: ' . $e->getMessage());
}

// Create MySQLi connection for legacy code
$conn = new mysqli($host, $user, $pass, $db, (int)$port);
if ($conn->connect_error) { 
    die('mysqli connect error: ' . $conn->connect_error); 
}
$conn->set_charset("utf8mb4");

// Rasa server URL
define('RASA_SERVER_URL', getenv('RASA_SERVER_URL') ?: 'http://localhost:5005');

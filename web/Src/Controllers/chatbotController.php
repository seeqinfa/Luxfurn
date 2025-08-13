<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Entities/ChatbotEntity.php';

if (empty($_SESSION['is_logged_in'])) {
    echo json_encode(['error'=>'not_logged_in']);
    exit;
}

$username = $_SESSION['username'];
$userMsg  = $_POST['user'] ?? '';
$botMsg   = $_POST['bot'] ?? '';

$bot = new ChatbotEntity();
if ($userMsg !== '') $bot->saveMessage($username, 'user', $userMsg);
if ($botMsg  !== '') $bot->saveMessage($username, 'bot',  $botMsg);

echo json_encode(['status'=>'saved']);

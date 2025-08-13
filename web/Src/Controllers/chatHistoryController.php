<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['is_logged_in'])) {
    echo json_encode([]);
    exit;
}

require_once __DIR__ . '/../Entities/ChatbotEntity.php';

$bot = new ChatbotEntity();
$history = $bot->fetchRecentMessages($_SESSION['username'], 100);

echo json_encode($history);

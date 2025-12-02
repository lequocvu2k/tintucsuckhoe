<?php
session_start();
require '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('no-login');
}

$id_kh = (int) $_SESSION['user_id'];
$message = trim($_POST['message'] ?? '');
$reply_to = isset($_POST['reply_to']) ? (int) $_POST['reply_to'] : null;

if ($message === '') {
    exit('empty');
}

// Gửi tin nhắn
$stmt = $pdo->prepare("
    INSERT INTO chat_messages(id_kh, message, reply_to, created_at)
    VALUES(?, ?, ?, NOW())
");

$stmt->execute([$id_kh, $message, $reply_to]);

echo 'ok';

<?php
session_start();
require '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('no-login');
}

$id_kh = (int) $_SESSION['user_id'];
$message = trim($_POST['message'] ?? '');

if ($message === '') {
    exit('empty');
}

$stmt = $pdo->prepare("INSERT INTO chat_messages(id_kh, message, created_at) VALUES(?, ?, NOW())");
$stmt->execute([$id_kh, $message]);

echo 'ok';

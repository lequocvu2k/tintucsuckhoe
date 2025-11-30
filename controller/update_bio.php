<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    die("Bạn phải đăng nhập!");
}

$id = $_SESSION['user_id'];
$bio = trim($_POST['bio'] ?? '');

$stmt = $pdo->prepare("UPDATE khachhang SET bio=? WHERE id_kh=?");
$stmt->execute([$bio, $id]);

$_SESSION['success'] = "✔ Bio đã được cập nhật!";

header("Location: ../view/user.php");
exit;

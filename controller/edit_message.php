<?php
session_start();
require_once '../php/db.php';

// ==== KIỂM TRA ĐĂNG NHẬP ====
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Chưa đăng nhập!"
    ]);
    exit;
}

$id_kh = $_SESSION['user_id'];

// ==== LẤY DỮ LIỆU ====
$id = $_POST['id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (!$id || $message === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Dữ liệu không hợp lệ!"
    ]);
    exit;
}

// ==== KIỂM TRA TIN NHẮN CÓ PHẢI CỦA USER NÀY KHÔNG ====
$stmt = $pdo->prepare("SELECT id_kh FROM chat_messages WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode([
        "status" => "error",
        "message" => "Tin nhắn không tồn tại!"
    ]);
    exit;
}

if ($row['id_kh'] != $id_kh) {
    echo json_encode([
        "status" => "error",
        "message" => "Không có quyền sửa tin nhắn!"
    ]);
    exit;
}

// ==== CẬP NHẬT TIN NHẮN ====
$stmt = $pdo->prepare("
    UPDATE chat_messages
    SET message = ?, updated_at = NOW()
    WHERE id = ?
");
$ok = $stmt->execute([$message, $id]);

if ($ok) {
    echo json_encode([
        "status" => "success",
        "message" => "Đã sửa!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Sửa thất bại!"
    ]);
}

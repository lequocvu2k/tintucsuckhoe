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

// ==== LẤY ID TIN NHẮN ====
$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode([
        "status" => "error",
        "message" => "ID không hợp lệ!"
    ]);
    exit;
}

// ==== KIỂM TRA TIN NHẮN CÓ TỒN TẠI KHÔNG ====
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

// ==== KIỂM TRA QUYỀN XÓA (CHỈ CHỦ TIN NHẮN ĐƯỢC XÓA) ====
if ($row['id_kh'] != $id_kh) {
    echo json_encode([
        "status" => "error",
        "message" => "Bạn không có quyền xóa tin nhắn này!"
    ]);
    exit;
}

// ==== XÓA TIN NHẮN ====
$stmt = $pdo->prepare("DELETE FROM chat_messages WHERE id = ?");
$ok = $stmt->execute([$id]);

if ($ok) {
    echo json_encode([
        "status" => "success",
        "message" => "Đã xóa!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Xóa thất bại!"
    ]);
}

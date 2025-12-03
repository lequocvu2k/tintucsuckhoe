<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "NO_LOGIN";
    exit;
}

// id câu hỏi từ AJAX
$id_hoi = intval($_POST['id_hoi']);
$id_kh = $_SESSION['user_id']; // chuyên gia đang đăng nhập

// kiểm tra câu hỏi có tồn tại không
$stmt = $pdo->prepare("SELECT id_chuyen_gia FROM hoi_dap WHERE id = ?");
$stmt->execute([$id_hoi]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo "NOT_FOUND";
    exit;
}

// chỉ chuyên gia của câu hỏi được xóa
if ($row['id_chuyen_gia'] != $id_kh) {
    echo "NO_PERMISSION";
    exit;
}

// xóa câu hỏi
$stmt = $pdo->prepare("DELETE FROM hoi_dap WHERE id = ?");
$stmt->execute([$id_hoi]);

echo "OK";

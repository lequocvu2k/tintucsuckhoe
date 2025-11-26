<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$id_kh = $_SESSION['user_id'];
$ma_bai_viet = $_POST['ma_bai_viet'] ?? 0;
$slug = $_POST['slug'] ?? '';  // Lấy slug từ form

// Kiểm tra hợp lệ
if ($ma_bai_viet == 0 || empty($slug)) {
    header("Location: index.php");
    exit;
}

// Kiểm tra bài viết đã lưu chưa
$stmt = $pdo->prepare("SELECT COUNT(*) FROM saved_posts WHERE id_kh = ? AND ma_bai_viet = ?");
$stmt->execute([$id_kh, $ma_bai_viet]);
$exists = $stmt->fetchColumn();

if ($exists == 0) {
    // Lưu bài
    $stmt = $pdo->prepare("INSERT INTO saved_posts (id_kh, ma_bai_viet) VALUES (?, ?)");
    $stmt->execute([$id_kh, $ma_bai_viet]);
    $_SESSION['msg'] = "📌 Đã lưu bài viết!";
} else {
    // Hủy lưu
    $stmt = $pdo->prepare("DELETE FROM saved_posts WHERE id_kh = ? AND ma_bai_viet = ?");
    $stmt->execute([$id_kh, $ma_bai_viet]);
    $_SESSION['msg'] = "❌ Đã bỏ lưu bài viết!";
}

// Quay lại đúng bài viết
header("Location: ../view/post.php?slug=" . urlencode($slug));
exit;
?>

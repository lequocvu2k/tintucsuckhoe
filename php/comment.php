<?php
session_start();
require_once './db.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "Vui lòng đăng nhập để bình luận.";
    exit;
}

// Lấy ID người dùng
$id_kh = $_SESSION['user_id'];

// Lấy slug bài viết
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    echo "Bài viết không tồn tại.";
    exit;
}

// Lấy nội dung bình luận
$comment_text = trim($_POST['comment_text']);
if (empty($comment_text)) {
    echo "Bình luận không được để trống.";
    exit;
}

// Lấy ID bài viết theo slug
$stmt_post = $pdo->prepare("SELECT ma_bai_viet FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt_post->execute([$slug]);
$post = $stmt_post->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Bài viết không tồn tại.";
    exit;
}

$post_id = $post['ma_bai_viet'];

// Lưu bình luận
$stmt = $pdo->prepare("INSERT INTO binhluan (ma_bai_viet, id_kh, noi_dung) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $id_kh, $comment_text]);

// ⬆️ ⭐ CỘNG ĐIỂM KHI BÌNH LUẬN (10 điểm)
$pdo->prepare("UPDATE khachhang SET so_diem = so_diem + 10 WHERE id_kh = ?")
    ->execute([$id_kh]);

// 💾 GHI LỊCH SỬ ĐIỂM
$pdo->prepare("
    INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
    VALUES (?, ?, 10, 'binh_luan', NOW())
")->execute([$id_kh, $post_id]);

// Quay lại bài viết
header("Location: post.php?slug=" . urlencode($slug));
exit;
?>

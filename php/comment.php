<?php
session_start();
require_once './db.php';

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "Vui lòng đăng nhập để bình luận.";
    exit;
}

// Lấy ID bài viết từ slug
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

// Lấy ID bài viết từ cơ sở dữ liệu
$stmt_post = $pdo->prepare("SELECT ma_bai_viet FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt_post->execute([$slug]);
$post = $stmt_post->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Bài viết không tồn tại.";
    exit;
}

$post_id = $post['ma_bai_viet'];

// Lưu bình luận vào cơ sở dữ liệu
$stmt = $pdo->prepare("INSERT INTO binhluan (ma_bai_viet, id_kh, noi_dung) VALUES (?, ?, ?)");
$stmt->execute([$post_id, $_SESSION['user_id'], $comment_text]);

// Chuyển hướng về bài viết sau khi gửi bình luận
header("Location: post.php?slug=" . urlencode($slug));
exit;
?>
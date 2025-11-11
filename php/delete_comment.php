<?php
session_start();
require_once './db.php';

// Kiểm tra nếu người dùng đã đăng nhập và có quyền xóa
if (!isset($_SESSION['user_id'])) {
    echo "Vui lòng đăng nhập để xóa bình luận.";
    exit;
}

// Lấy ID bình luận và slug từ URL
$comment_id = $_GET['id'] ?? '';
$slug = $_GET['slug'] ?? '';

if (empty($comment_id) || empty($slug)) {
    echo "Không tìm thấy bình luận hoặc bài viết.";
    exit;
}

// Truy vấn lấy thông tin bài viết
$stmt_post = $pdo->prepare("SELECT ma_bai_viet FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt_post->execute([$slug]);
$post = $stmt_post->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Bài viết không tồn tại.";
    exit;
}

$post_id = $post['ma_bai_viet'];

// Kiểm tra quyền xóa bình luận
$stmt = $pdo->prepare("SELECT * FROM binhluan WHERE id_binhluan = ? AND id_kh = ?");
$stmt->execute([$comment_id, $_SESSION['user_id']]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    echo "Bình luận không tồn tại hoặc bạn không có quyền xóa.";
    exit;
}

// Xóa bình luận
$stmt_delete = $pdo->prepare("DELETE FROM binhluan WHERE id_binhluan = ?");
$stmt_delete->execute([$comment_id]);

// Trả về thông báo thành công
echo "Bình luận đã được xóa";
?>

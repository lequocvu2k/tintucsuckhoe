<?php
session_start();
require_once './db.php';

// Kiểm tra nếu người dùng đã đăng nhập và có quyền sửa
if (!isset($_SESSION['user_id'])) {
    echo "Vui lòng đăng nhập để sửa bình luận.";
    exit;
}

// Lấy ID bình luận và nội dung mới từ POST
$comment_id = $_POST['id'] ?? '';
$new_comment_text = $_POST['comment_text'] ?? '';

if (empty($comment_id) || empty($new_comment_text)) {
    echo "Không tìm thấy bình luận hoặc nội dung bình luận không hợp lệ.";
    exit;
}

// Kiểm tra quyền sửa bình luận
$stmt = $pdo->prepare("SELECT * FROM binhluan WHERE id_binhluan = ? AND id_kh = ?");
$stmt->execute([$comment_id, $_SESSION['user_id']]);
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$comment) {
    echo "Bình luận không tồn tại hoặc bạn không có quyền sửa.";
    exit;
}

// Cập nhật bình luận trong cơ sở dữ liệu
$stmt_update = $pdo->prepare("UPDATE binhluan SET noi_dung = ? WHERE id_binhluan = ?");
$stmt_update->execute([$new_comment_text, $comment_id]);

// Trả về thông báo thành công
echo "Bình luận đã được sửa";
?>

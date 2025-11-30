<?php
session_start();
require_once '../php/db.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo "Vui lòng đăng nhập để bình luận.";
    exit;
}

$id_kh = $_SESSION['user_id'];

// Lấy thông tin user
$stmt_user = $pdo->prepare("SELECT is_banned, is_muted, muted_until FROM khachhang WHERE id_kh = ?");
$stmt_user->execute([$id_kh]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// ======================
// 1. CHẶN USER BỊ BAN
// ======================
if ($user['is_banned'] == 1) {
    echo "⛔ Tài khoản của bạn đã bị BAN — không thể bình luận.";
    exit;
}

// ======================
// 2. TỰ ĐỘNG GỠ MUTE NẾU HẾT HẠN
// ======================
if ($user['is_muted'] == 1 && $user['muted_until'] !== null) {
    if (strtotime($user['muted_until']) <= time()) {

        // Gỡ mute do hết hạn
        $pdo->prepare("
            UPDATE khachhang 
            SET is_muted = 0, muted_until = NULL 
            WHERE id_kh = ?
        ")->execute([$id_kh]);

        $user['is_muted'] = 0;
        $user['muted_until'] = null;
    }
}

// ======================
// 3. CHẶN USER ĐANG BỊ MUTE
// ======================
if ($user['is_muted'] == 1) {

    // Nếu có thời hạn → tính thời gian còn lại
    if ($user['muted_until'] !== null) {
        $remaining = strtotime($user['muted_until']) - time();

        $minutes = floor($remaining / 60);
        $hours = floor($remaining / 3600);
        $days = floor($remaining / 86400);

        if ($days > 0) {
            echo "⛔ Bạn đang bị cấm chat. Còn $days ngày nữa mới có thể bình luận.";
        } elseif ($hours > 0) {
            echo "⛔ Bạn đang bị cấm chat. Còn $hours giờ nữa mới có thể bình luận.";
        } else {
            echo "⛔ Bạn đang bị cấm chat. Còn $minutes phút nữa mới có thể bình luận.";
        }

    } else {
        // Mute vĩnh viễn
        echo "⛔ Bạn đang bị cấm chat vĩnh viễn — không thể bình luận.";
    }

    exit;
}


// ======================
// 4. Lấy slug bài viết
// ======================
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

// Lấy ID bài viết
$stmt_post = $pdo->prepare("SELECT ma_bai_viet FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt_post->execute([$slug]);
$post = $stmt_post->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Bài viết không tồn tại.";
    exit;
}

$post_id = $post['ma_bai_viet'];

// ======================
// 5. Lưu bình luận
// ======================
$stmt = $pdo->prepare("INSERT INTO binhluan (ma_bai_viet, id_kh, noi_dung, ngay_binhluan) VALUES (?, ?, ?, NOW())");
$stmt->execute([$post_id, $id_kh, $comment_text]);


// ======================
// 6. Cộng điểm bình luận
// ======================
$pdo->prepare("UPDATE khachhang SET so_diem = so_diem + 10 WHERE id_kh = ?")
    ->execute([$id_kh]);

// Lưu lịch sử điểm
$pdo->prepare("
    INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
    VALUES (?, ?, 10, 'binh_luan', NOW())
")->execute([$id_kh, $post_id]);

// ======================
// 7. Quay lại bài viết
// ======================
header("Location: ../view/post.php?slug=" . urlencode($slug));
exit;
?>
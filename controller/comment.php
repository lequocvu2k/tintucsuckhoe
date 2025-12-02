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
function getUser($pdo, $id_kh)
{
    $stmt = $pdo->prepare("SELECT is_banned, is_muted, muted_until FROM khachhang WHERE id_kh = ?");
    $stmt->execute([$id_kh]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$user = getUser($pdo, $id_kh);

// ======================
// 1. CHẶN USER BỊ BAN
// ======================
if ($user['is_banned'] == 1) {
    echo "⛔ Tài khoản của bạn đã bị BAN — không thể bình luận.";
    exit;
}

// ======================
// 2. AUTO UNMUTE
// ======================
if ($user['is_muted'] == 1 && $user['muted_until'] !== null) {

    if (strtotime($user['muted_until']) <= time()) {

        // Cập nhật DB
        $pdo->prepare("
            UPDATE khachhang
            SET is_muted = 0, muted_until = NULL
            WHERE id_kh = ?
        ")->execute([$id_kh]);

        // Reload lại user để cập nhật trạng thái mới
        $user = getUser($pdo, $id_kh);
    }
}

// ======================
// 3. CHẶN USER ĐANG BỊ MUTE (SAU KHI AUTO-UNMUTE)
// ======================
if ($user['is_muted'] == 1) {

    if (!empty($user['muted_until'])) {

        $remaining = strtotime($user['muted_until']) - time();
        if ($remaining < 0)
            $remaining = 0;

        $minutes = floor($remaining / 60);
        $hours = floor($remaining / 3600);
        $days = floor($remaining / 86400);

        if ($days > 0)
            echo "⛔ Bạn đang bị cấm chat. Còn $days ngày nữa.";
        elseif ($hours > 0)
            echo "⛔ Bạn đang bị cấm chat. Còn $hours giờ nữa.";
        else
            echo "⛔ Bạn đang bị cấm chat. Còn $minutes phút nữa.";

    } else {
        echo "⛔ Bạn đang bị cấm chat vĩnh viễn.";
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

// Nội dung bình luận
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
$stmt = $pdo->prepare("INSERT INTO binhluan (ma_bai_viet, id_kh, noi_dung, ngay_binhluan) 
                       VALUES (?, ?, ?, NOW())");
$stmt->execute([$post_id, $id_kh, $comment_text]);

// +10 điểm
$pdo->prepare("UPDATE khachhang SET so_diem = so_diem + 10 WHERE id_kh = ?")
    ->execute([$id_kh]);

$pdo->prepare("
    INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
    VALUES (?, ?, 10, 'binh_luan', NOW())
")->execute([$id_kh, $post_id]);

// Redirect lại bài viết
header("Location: ../view/post.php?slug=" . urlencode($slug));
exit;
?>
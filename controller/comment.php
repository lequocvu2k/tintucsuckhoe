<?php
session_start();
require_once '../php/db.php';

/* ======================
   FIX TIMEZONE CHUẨN
====================== */
date_default_timezone_set("Asia/Ho_Chi_Minh");
$pdo->exec("SET time_zone = '+07:00'");

/* ======================
   1. KIỂM TRA ĐĂNG NHẬP
====================== */
if (!isset($_SESSION['user_id'])) {
    echo "Vui lòng đăng nhập để bình luận.";
    exit;
}

$id_kh = $_SESSION['user_id'];

/* ======================
   2. HÀM LẤY USER
====================== */
function getUser($pdo, $id_kh)
{
    $stmt = $pdo->prepare("
        SELECT is_banned, is_muted, muted_until 
        FROM khachhang 
        WHERE id_kh = ?
    ");
    $stmt->execute([$id_kh]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$user = getUser($pdo, $id_kh);

/* ======================
   3. CHẶN USER BỊ BAN
====================== */
if ($user['is_banned'] == 1) {
    echo "⛔ Tài khoản của bạn đã bị BAN — không thể bình luận.";
    exit;
}

/* ======================
   4. AUTO UNMUTE
====================== */
if ($user['is_muted'] == 1 && !empty($user['muted_until'])) {

    if (strtotime($user['muted_until']) <= time()) {

        // Gỡ mute
        $pdo->prepare("
            UPDATE khachhang
            SET is_muted = 0, muted_until = NULL
            WHERE id_kh = ?
        ")->execute([$id_kh]);

        // Reload user
        $user = getUser($pdo, $id_kh);
    }
}

/* ======================
   5. CHẶN USER ĐANG BỊ MUTE
====================== */
if ($user['is_muted'] == 1) {

    if (!empty($user['muted_until'])) {

        $remaining = strtotime($user['muted_until']) - time();
        if ($remaining < 0)
            $remaining = 0;

        $days = floor($remaining / 86400);
        $hours = floor(($remaining % 86400) / 3600);
        $minutes = floor(($remaining % 3600) / 60);

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

/* ======================
   6. SPAM PROTECT – 30 GIÂY
====================== */

$cooldown = 30;

$stmt_last = $pdo->prepare("SELECT last_comment_at FROM khachhang WHERE id_kh = ?");
$stmt_last->execute([$id_kh]);
$last = $stmt_last->fetchColumn();

if (!empty($last)) {

    $last_time = strtotime($last);
    $now = time();

    if (($now - $last_time) < $cooldown) {

        $remaining = $cooldown - ($now - $last_time);

        echo "⏳ Vui lòng chờ thêm $remaining giây để bình luận tiếp.";
        exit;
    }
}

/* ======================
   7. LẤY SLUG BÀI VIẾT
====================== */
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    echo "Bài viết không tồn tại.";
    exit;
}

/* ======================
   8. NỘI DUNG BÌNH LUẬN
====================== */
$comment_text = trim($_POST['comment_text']);
if (empty($comment_text)) {
    echo "Bình luận không được để trống.";
    exit;
}

/* ======================
   9. LẤY ID BÀI VIẾT
====================== */
$stmt_post = $pdo->prepare("
    SELECT ma_bai_viet 
    FROM baiviet 
    WHERE duong_dan = ? AND trang_thai = 'published'
");
$stmt_post->execute([$slug]);
$post = $stmt_post->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Bài viết không tồn tại.";
    exit;
}

$post_id = $post['ma_bai_viet'];

/* ======================
   10. LƯU BÌNH LUẬN
====================== */
$pdo->prepare("
    INSERT INTO binhluan (ma_bai_viet, id_kh, noi_dung, ngay_binhluan)
    VALUES (?, ?, ?, NOW())
")->execute([$post_id, $id_kh, $comment_text]);

/* ======================
   11. CẬP NHẬT last_comment_at (KHÔNG CONVERT_TZ NỮA)
====================== */
$pdo->prepare("
    UPDATE khachhang 
    SET last_comment_at = NOW()
    WHERE id_kh = ?
")->execute([$id_kh]);

/* ======================
   12. CỘNG ĐIỂM
====================== */
$pdo->prepare("
    UPDATE khachhang 
    SET so_diem = so_diem + 10 
    WHERE id_kh = ?
")->execute([$id_kh]);

$pdo->prepare("
    INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
    VALUES (?, ?, 10, 'binh_luan', NOW())
")->execute([$id_kh, $post_id]);

/* ======================
   13. QUAY LẠI BÀI VIẾT
====================== */
header("Location: ../view/post.php?slug=" . urlencode($slug));
exit;
?>
<?php
session_start();
require_once '../php/db.php';

// Bắt buộc đăng nhập
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Lấy thông tin user (avatar + tên)
$stmt = $pdo->prepare("
    SELECT ho_ten, avatar_url, avatar_frame
    FROM khachhang
    WHERE id_kh = ?
");
$stmt->execute([$user_id]);
$userBasic = $stmt->fetch(PDO::FETCH_ASSOC);

$displayName = $userBasic['ho_ten'] ?? 'Người dùng';

// ====================== LẤY THÔNG TIN NGƯỜI DÙNG ======================
$user = null;
$tier = "Member";

$stmt = $pdo->prepare("
    SELECT kh.*, tk.ngay_tao
    FROM khachhang kh
    LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
    WHERE kh.id_kh = :id
");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include '../partials/menu.php';

// Avatar
if (!empty($user['avatar_url'])) {
    // avatar_url đã lưu dạng uploads/avatars/xxx.png
    $myAvatar = '../' . ltrim($user['avatar_url'], '/');
} else {
    $myAvatar = '../img/avt.jpg';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phòng chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/popup.css">
    <link rel="stylesheet" href="../css/chat_messages.css">
    <link rel="stylesheet" href="../css/menu.css">
    <?php include '../partials/logo.php'; ?>
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/messages.js" defer></script>
</head>

<body data-user-id="<?= (int) $user_id ?>">

    <?php include '../partials/header.php'; ?>
    <?php include '../partials/login.php'; ?>

    <div class="chat-wrapper">
        <div class="chat-card">

            <div class="chat-header">
                <div class="chat-header-left">
                    <img src="<?= htmlspecialchars($myAvatar) ?>" alt="Avatar">
                    <div class="chat-header-title">
                        <span>Phòng chat Health News</span>
                        <span>Đăng nhập như: <?= htmlspecialchars($displayName) ?></span>
                    </div>
                </div>
                <div class="chat-header-right">
                    <i class="fa-solid fa-shield-heart"></i>
                    <span>Vui lòng trao đổi văn minh, không spam 💬</span>
                </div>
            </div>

            <!-- KHÔNG ĐƯỢC ĐỂ $msg Ở ĐÂY -->
            <div id="chatBody" class="chat-body">
                <!-- Messages sẽ được JS đổ vào đây -->
            </div>

            <!-- Form gửi tin nhắn -->
            <form id="chatForm" class="chat-input-wrap">
                <textarea id="chatInput" class="chat-input" placeholder="Nhập tin nhắn của bạn..."
                    maxlength="500"></textarea>
                <div id="editNotice" class="edit-notice" style="display:none;">
                    Đang sửa tin nhắn...
                    <button id="cancelEdit" class="cancel-edit">Hủy</button>
                </div>

                <!-- ⭐ KHUNG TRẢ LỜI TIN NHẮN (THÊM VÀO Ở ĐÂY) ⭐ -->
                <div id="replyBox" class="reply-box" style="display:none;">
                    <span id="replyText"></span>
                    <button id="cancelReply" type="button" class="cancel-reply">Hủy</button>
                </div>
                <!-- ⭐ HẾT ⭐ -->


                <button type="submit" id="btnSend" class="btn-send">
                    <i class="fa-solid fa-paper-plane"></i> Gửi
                </button>

            </form>

        </div>
    </div>

    <?php include '../partials/footer.php'; ?>

</body>

</html>
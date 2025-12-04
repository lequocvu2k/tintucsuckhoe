<?php
session_start();
require_once '../php/db.php';

// Chỉ admin được xem
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'QuanTri') {
    die("<h2 style='color:red;text-align:center;margin-top:50px;'>🚫 Bạn không có quyền xem trang này!</h2>");
}

$id = $_GET['id'] ?? 0;
include '../partials/menu.php';
$stmt = $pdo->prepare("
    SELECT b.*, k.ho_ten
    FROM baiviet b
    JOIN khachhang k ON b.id_kh = k.id_kh
    WHERE ma_bai_viet = ?
");
$stmt->execute([$id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("<h2 style='text-align:center;margin-top:50px;color:red;'>❌ Không tìm thấy bài viết</h2>");
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xem bài viết - <?= htmlspecialchars($post['tieu_de']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/hethongduyetbai.css">
    <link rel="stylesheet" href="../css/menu.css">
    <?php include '../partials/logo.php'; ?>
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="preview-container">
        <h1><?= htmlspecialchars($post['tieu_de']) ?></h1>

        <p class="meta">
            ✍️ Tác giả: <b><?= htmlspecialchars($post['ho_ten']) ?></b> |
            📅 Ngày đăng: <?= date("d/m/Y", strtotime($post['ngay_dang'])) ?>
        </p>

        <?php if (!empty($post['anh_bv'])): ?>
            <img src="/php/<?= $post['anh_bv'] ?>" class="preview-image">
        <?php endif; ?>

        <div class="preview-content">
            <?= $post['noi_dung'] ?>
        </div>

        <div class="actions">
            <a class="btn approve" href="hethongduyetbai.php?action=approve&id=<?= $post['ma_bai_viet'] ?>">✔️ Duyệt</a>
            <a class="btn reject" href="hethongduyetbai.php?action=reject&id=<?= $post['ma_bai_viet'] ?>">❌ Từ chối</a>
        </div>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>

</html>
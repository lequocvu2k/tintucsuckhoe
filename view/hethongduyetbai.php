<?php
session_start();
require_once '../php/db.php';

// ❌ Chặn người không phải ADMIN
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'QuanTri') {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>🚫 Bạn không có quyền truy cập trang này!</h2>";
    exit;
}

include '../partials/menu.php';

// 📝 Xử lý duyệt hoặc từ chối
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];
    if ($action == "approve") {

        // Lấy thông tin bài viết để biết id_kh và tiêu đề
        $stmt = $pdo->prepare("SELECT id_kh, tieu_de FROM baiviet WHERE ma_bai_viet = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            // Cập nhật trạng thái published
            $stmt = $pdo->prepare("UPDATE baiviet SET trang_thai = 'published' WHERE ma_bai_viet = ?");
            $stmt->execute([$id]);

            // Gửi thông báo
            $msg = "🎉 Bài viết <b>" . $post['tieu_de'] . "</b> của bạn đã được duyệt!";
            $notify = $pdo->prepare("
            INSERT INTO thongbao (id_kh, noi_dung, da_doc, created_at)
            VALUES (:id_kh, :noi_dung, 0, NOW())
        ");
            $notify->execute([
                ':id_kh' => $post['id_kh'],
                ':noi_dung' => $msg
            ]);
        }

        $_SESSION['msg'] = "✔️ Đã duyệt bài viết!";
    } elseif ($action == "reject") {

        // Lấy thông tin bài viết trước khi từ chối
        $stmt = $pdo->prepare("SELECT id_kh, tieu_de FROM baiviet WHERE ma_bai_viet = ?");
        $stmt->execute([$id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            // Cập nhật trạng thái rejected
            $stmt = $pdo->prepare("UPDATE baiviet SET trang_thai = 'rejected' WHERE ma_bai_viet = ?");
            $stmt->execute([$id]);

            // Gửi thông báo
            $msg = "⚠️ Bài viết <b>" . $post['tieu_de'] . "</b> của bạn đã bị từ chối.";
            $notify = $pdo->prepare("
            INSERT INTO thongbao (id_kh, noi_dung, da_doc, created_at)
            VALUES (:id_kh, :noi_dung, 0, NOW())
        ");
            $notify->execute([
                ':id_kh' => $post['id_kh'],
                ':noi_dung' => $msg
            ]);
        }

        $_SESSION['msg'] = "❌ Đã từ chối bài viết!";
    }

    header("Location: hethongduyetbai.php");
    exit;
}

// 📝 Lấy tất cả bài viết đang chờ duyệt
$stmt = $pdo->query("
    SELECT b.*, k.ho_ten 
    FROM baiviet b 
    JOIN khachhang k ON b.id_kh = k.id_kh
    WHERE b.trang_thai = 'pending'
    ORDER BY b.ngay_dang DESC
");
$pending_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hệ thống duyệt bài</title>
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
    <h1 class="page-title"><i class="fas fa-check-circle"></i> HỆ THỐNG DUYỆT BÀI</h1>

    <?php if (isset($_SESSION['msg'])): ?>
        <p style="color:green; font-weight:bold; text-align:center;">
            <?= $_SESSION['msg'];
            unset($_SESSION['msg']); ?>
        </p>
    <?php endif; ?>

    <table class="approve-table">
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Tác giả</th>
                <th>Ngày đăng</th>
                <th>Ảnh</th>
                <th>Hành động</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($pending_posts as $post): ?>
                <tr>
                    <td><?= htmlspecialchars($post['tieu_de']) ?></td>
                    <td><?= htmlspecialchars($post['ho_ten']) ?></td>
                    <td><?= date("d/m/Y", strtotime($post['ngay_dang'])) ?></td>
                    <td>
                        <img src="/php/<?= htmlspecialchars($post['anh_bv']) ?>" alt="Ảnh bài viết">
                    </td>
                    <td>
                        <a class="btn approve"
                            href="hethongduyetbai.php?action=approve&id=<?= $post['ma_bai_viet'] ?>">Duyệt</a>
                        <a class="btn reject" href="hethongduyetbai.php?action=reject&id=<?= $post['ma_bai_viet'] ?>">Từ
                            chối</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (count($pending_posts) === 0): ?>
        <div class="no-posts">
            <i class="fas fa-folder-open"></i>
            <p>Hiện không có bài viết nào đang chờ duyệt.</p>
        </div>
    <?php endif; ?>

    <?php include '../partials/footer.php'; ?>
</body>

</html>
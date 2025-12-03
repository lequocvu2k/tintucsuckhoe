<?php
session_start();
require_once '../php/db.php';

// chỉ admin hoặc nhân viên
if (
    !isset($_SESSION['username']) ||
    ($_SESSION['username'] !== 'admin' && $_SESSION['user_role'] !== 'NhanVien')
) {

    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>🚫 Bạn không có quyền truy cập trang này!</h2>";
    exit;
}

/* ============================
    LẤY THỐNG KÊ TỔNG QUAN
=============================== */

// Tổng bài viết
$totalPosts = $pdo->query("SELECT COUNT(*) FROM baiviet")->fetchColumn();

// Bài viết đã đăng
$published = $pdo->query("SELECT COUNT(*) FROM baiviet WHERE trang_thai='published'")->fetchColumn();

// Bài viết từ chối
$hidden = $pdo->query("SELECT COUNT(*) FROM baiviet WHERE trang_thai='hidden'")->fetchColumn();

// Tổng lượt xem
$totalViews = $pdo->query("SELECT SUM(luot_xem) FROM baiviet")->fetchColumn();
$totalViews = $totalViews ?? 0;

/* ============================
    TOP 5 BÀI NHIỀU LƯỢT XEM
=============================== */

$topViews = $pdo->query("
    SELECT ma_bai_viet, tieu_de, duong_dan, luot_xem, anh_bv
    FROM baiviet
    WHERE trang_thai='published'
    ORDER BY luot_xem DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

/* ============================
    TOP 5 BÀI NHIỀU BÌNH LUẬN
=============================== */

$topComments = $pdo->query("
    SELECT b.ma_bai_viet, b.tieu_de, b.duong_dan, b.anh_bv,
           COUNT(c.id_binhluan) AS total_cmt
    FROM baiviet b
    LEFT JOIN binhluan c ON b.ma_bai_viet = c.ma_bai_viet
    GROUP BY b.ma_bai_viet
    ORDER BY total_cmt DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

/* ============================
    TOP 5 TÁC GIẢ ĐĂNG NHIỀU BÀI
=============================== */
$topAuthors = $pdo->query("
    SELECT kh.ho_ten, kh.avatar_url, kh.avatar_frame, COUNT(b.ma_bai_viet) AS total_post
    FROM baiviet b
    JOIN khachhang kh ON b.ma_tac_gia = kh.id_kh
    WHERE kh.vai_tro IN ('QuanTri', 'NhanVien')
    GROUP BY b.ma_tac_gia
    ORDER BY total_post DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

include '../partials/menu.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>📊 Thống kê bài viết</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/thongke.css">
    <link rel="stylesheet" href="../css/menu.css">
    <?php include '../partials/logo.php'; ?>
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
</head>

<body>
    <?php include '../partials/header.php'; ?>

    <h2 class="page-title">📊 THỐNG KÊ HỆ THỐNG BÀI VIẾT</h2>

    <!-- ======================= THỐNG KÊ NHANH =========================== -->
    <div class="stats-box">
        <div class="box">
            <h3><i class="fas fa-newspaper"></i> Tổng bài viết</h3>
            <span><?= $totalPosts ?></span>
        </div>

        <div class="box">
            <h3><i class="fas fa-check-circle"></i> Đã đăng</h3>
            <span><?= $published ?></span>
        </div>

        <div class="box">
            <h3><i class="fas fa-times-circle"></i> Bị từ chối</h3>
            <span><?= $hidden ?></span>
        </div>

        <div class="box">
            <h3><i class="fas fa-fire"></i> Tổng lượt xem</h3>
            <span><?= $totalViews ?></span>
        </div>
    </div>


    <div class="table-container">

        <!-- TOP VIEW -->
        <div class="stat-section"><i class="fas fa-chart-line"></i> Top 5 bài viết nhiều lượt xem</div>
        <div class="card-list">
            <?php foreach ($topViews as $v): ?>
                <a class="card-item" href="../view/post.php?slug=<?= urlencode($v['duong_dan']) ?>">

                    <img src="/php/<?= htmlspecialchars($v['anh_bv'] ?? 'default.jpg') ?>" class="card-thumb">

                    <div class="card-content">
                        <h4><?= htmlspecialchars($v['tieu_de']) ?></h4>
                        <div class="meta">
                            <i class="fas fa-eye"></i> <?= number_format($v['luot_xem']) ?> lượt xem
                        </div>
                    </div>

                </a>
            <?php endforeach; ?>
        </div>

        <!-- TOP COMMENTS -->
        <div class="stat-section"><i class="fas fa-comments"></i> Top 5 bài viết nhiều bình luận</div>

        <div class="card-list">
            <?php foreach ($topComments as $c): ?>
                <a class="card-item" href="../view/post.php?slug=<?= urlencode($c['duong_dan']) ?>">

                    <img src="/php/<?= htmlspecialchars($c['anh_bv'] ?? 'default.jpg') ?>" class="card-thumb">

                    <div class="card-content">
                        <h4><?= htmlspecialchars($c['tieu_de']) ?></h4>
                        <div class="meta">
                            <i class="fas fa-comment-dots"></i> <?= $c['total_cmt'] ?> bình luận
                        </div>
                    </div>

                </a>
            <?php endforeach; ?>
        </div>



        <!-- TOP AUTHORS -->
        <div class="stat-section"><i class="fas fa-user-edit"></i> Top 5 tác giả đăng nhiều bài</div>

        <div class="card-list">
            <?php foreach ($topAuthors as $a): ?>
                <div class="card-item">

                    <div class="avatar-wrapper">
                        <img src="/uploads/<?= htmlspecialchars($a['avatar_url'] ?? 'avt.jpg') ?>" class="avatar-img">

                        <?php if (!empty($a['avatar_frame'])): ?>
                            <img class="avatar-frame" src="/php/frames/<?= htmlspecialchars($a['avatar_frame']) ?>.png"
                                onerror="this.style.display='none'">
                        <?php endif; ?>
                    </div>

                    <div class="card-content">
                        <h4><?= htmlspecialchars($a['ho_ten']) ?></h4>
                        <div class="meta">
                            <i class="fas fa-pen-nib"></i> <?= $a['total_post'] ?> bài viết
                        </div>
                    </div>

                </div>

            <?php endforeach; ?>
        </div>


    </div>

    <?php include '../partials/footer.php'; ?>
</body>

</html>
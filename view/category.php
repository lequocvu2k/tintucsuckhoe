<?php
session_start();
require_once '../php/db.php';

// Lấy ID danh mục
$cat_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($cat_id <= 0) {
    header("Location: ../index.php");
    exit;
}

// Popular posts (5 bài thuộc danh mục POPULAR POSTS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'POPULAR POSTS'
    ORDER BY ngay_dang DESC
    LIMIT 5
");
$popular = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ====================== LẤY THÔNG TIN NGƯỜI DÙNG ====================== */
$user = null;
$tier = "Member";

if (isset($_SESSION['user_id'])) {
    $id_kh = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT kh.*, tk.ngay_tao
        FROM khachhang kh
        LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
        WHERE kh.id_kh = :id
    ");
    $stmt->bindParam(':id', $id_kh);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        function xacDinhCapDo($so_diem)
        {
            if ($so_diem >= 10000)
                return 'Siêu Kim Cương';
            if ($so_diem >= 5000)
                return 'Kim Cương';
            if ($so_diem >= 1000)
                return 'Vàng';
            if ($so_diem >= 500)
                return 'Bạc';
            return 'Member';
        }
        $tier = xacDinhCapDo($user['so_diem'] ?? 0);
    }
}

/* ====================== LẤY THÔNG TIN DANH MỤC ====================== */
$stmt = $pdo->prepare("SELECT * FROM chuyenmuc WHERE ma_chuyen_muc = ?");
$stmt->execute([$cat_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("<h2>❌ Danh mục không tồn tại.</h2>");
}

/* ====================== LẤY BÀI VIẾT THEO DANH MỤC ====================== */
/* ====================== PHÂN TRANG ====================== */
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

/* Tổng số bài trong danh mục */
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM baiviet 
    WHERE ma_chuyen_muc = :cat_id 
      AND trang_thai = 'published'
");
$stmt->execute(['cat_id' => $cat_id]);
$totalPosts = $stmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);

/* Lấy bài theo trang — đã sửa lỗi */
$stmt = $pdo->prepare("
    SELECT bv.*, kh.ho_ten, kh.avatar_url
    FROM baiviet bv
    LEFT JOIN khachhang kh ON bv.ma_tac_gia = kh.id_kh
    WHERE bv.ma_chuyen_muc = :cat_id 
      AND bv.trang_thai = 'published'
    ORDER BY bv.ngay_dang DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':cat_id', $cat_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);


$page_title = $category['ten_chuyen_muc'] . " | Tin tức Sức khỏe";
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/category.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/popup.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/popup.js"></script>
</head>

<body>

    <?php include '../partials/header.php'; ?>

    <div class="category-container">

        <div class="breadcrumb">
            <a href="../view/index.php">Home</a>
            <span class="breadcrumb-sep">›</span>

            <a href="./news.php">News</a>

            <span class="breadcrumb-sep">›</span>

            <span><?= htmlspecialchars($category['ten_chuyen_muc']) ?></span>
        </div>

        <div class="main-content">

            <!-- CỘT TRÁI -->
            <div class="left-column">

                <h2 class="cat-title">
                    CATEGORY:
                    <span><?= mb_strtoupper($category['ten_chuyen_muc'], 'UTF-8') ?></span>
                </h2>

                <p class="cat-description"><?= htmlspecialchars($category['mo_ta']) ?></p>

                <!-- LIST BÀI VIẾT -->
                <?php foreach ($posts as $p): ?>
                    <div class="article-item">

                        <!-- TRUYỀN SLUG ĐÚNG -->
                        <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>" class="article-thumb">
                            <img src="/php/<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                        </a>

                        <div class="article-content">
                            <h3>
                                <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                    <?= htmlspecialchars($p['tieu_de']) ?>
                                </a>
                            </h3>

                            <p class="meta">
                                by <?= htmlspecialchars($p['ho_ten']) ?> •
                                <?= date("F d, Y", strtotime($p['ngay_dang'])) ?>
                            </p>

                            <p class="excerpt">
                                <?= htmlspecialchars(mb_substr(strip_tags($p['noi_dung']), 0, 150)) ?>...
                            </p>
                        </div>

                    </div>

                    <hr class="divider">
                <?php endforeach; ?>
                <!-- PHÂN TRANG KIỂU MINIMAL -->
                <div class="pagination-minimal">

                    <?php if ($page > 1): ?>
                        <a class="pag-btn" href="?id=<?= $cat_id ?>&page=<?= $page - 1 ?>">
                            ‹ NEWER POSTS
                        </a>
                    <?php else: ?>
                        <span class="pag-btn disabled">‹ NEWER POSTS</span>
                    <?php endif; ?>

                    <span class="separator">/</span>

                    <?php if ($page < $totalPages): ?>
                        <a class="pag-btn" href="?id=<?= $cat_id ?>&page=<?= $page + 1 ?>">
                            OLDER POSTS ›
                        </a>
                    <?php else: ?>
                        <span class="pag-btn disabled">OLDER POSTS ›</span>
                    <?php endif; ?>

                </div>

            </div>

            <!-- CỘT PHẢI -->
            <aside class="right-column">
                <h3 class="sidebar-title">POPULAR POSTS</h3>

                <ul class="popular-list">
                    <?php
             
                    foreach ($popular as $p):
                        ?>
                        <li class="popular-item">


                            <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>" class="pop-thumb">
                                <img src="/php/<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                            </a>

                            <div class="pop-info">
                                <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>" class="pop-title">
                                    <?= htmlspecialchars($p['tieu_de']) ?>
                                </a>

                                <p class="pop-date"><?= date("F d, Y", strtotime($p['ngay_dang'])) ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </aside>

        </div>
    </div>

</body>

</html>
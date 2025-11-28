<?php
session_start();
require_once '../php/db.php';

/* ====================== LOẠI XẾP HẠNG ====================== */
$type = $_GET['type'] ?? 'likes';

switch ($type) {

    case 'likes':
        $title = "Top bài viết được like nhiều nhất";
        $orderSQL = "
            SELECT b.*, COUNT(l.id_like) AS score, kh.ho_ten, kh.avatar_url
            FROM baiviet b
            LEFT JOIN likes l ON b.ma_bai_viet = l.ma_bai_viet
            LEFT JOIN khachhang kh ON b.ma_tac_gia = kh.id_kh
            WHERE b.trang_thai = 'published'
            GROUP BY b.ma_bai_viet
            ORDER BY score DESC, b.ngay_dang DESC
        ";
        break;
    case 'weekview':
        $title = "Top bài viết xem nhiều nhất";

        $orderSQL = "
        SELECT b.*, b.luot_xem AS score, kh.ho_ten, kh.avatar_url
        FROM baiviet b
        LEFT JOIN khachhang kh ON b.ma_tac_gia = kh.id_kh
        WHERE b.trang_thai = 'published'
        ORDER BY b.luot_xem DESC
    ";
        break;


    case 'comments':
        $title = "Top bài viết có nhiều bình luận nhất";
        $orderSQL = "
            SELECT b.*, COUNT(bl.id_binhluan) AS score, kh.ho_ten, kh.avatar_url
            FROM baiviet b
            LEFT JOIN binhluan bl ON b.ma_bai_viet = bl.ma_bai_viet
            LEFT JOIN khachhang kh ON b.ma_tac_gia = kh.id_kh
            WHERE b.trang_thai = 'published'
            GROUP BY b.ma_bai_viet
            ORDER BY score DESC, b.ngay_dang DESC
        ";
        break;
}

/* ====================== PHÂN TRANG ====================== */
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

/* Tổng bài */
$totalStmt = $pdo->query("SELECT COUNT(*) FROM ($orderSQL) AS temp");
$totalPosts = $totalStmt->fetchColumn();
$totalPages = ceil($totalPosts / $limit);

/* Lấy bài theo Ranking */
$stmt = $pdo->prepare("$orderSQL LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ====================== POPULAR POSTS ====================== */
$popularStmt = $pdo->query("
    SELECT * FROM baiviet 
    WHERE trang_thai='published' 
      AND danh_muc='POPULAR POSTS'
    ORDER BY ngay_dang DESC
    LIMIT 5
");
$popular = $popularStmt->fetchAll(PDO::FETCH_ASSOC);


// ====================== LẤY THÔNG TIN NGƯỜI DÙNG ======================
$user = null; // Mặc định là khách
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
        function tinhDiem($so_diem)
        {
            return floor($so_diem / 10000);
        }
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
        $so_diem = is_numeric($user['so_diem']) ? $user['so_diem'] : 0;
        $tier = xacDinhCapDo($so_diem);
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $title ?> | Rankings</title>
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

        <!-- BREADCRUMB -->
        <div class="breadcrumb">
            <a href="../view/index.php">Home</a>
            <span class="breadcrumb-sep">›</span>
            <span>Rankings</span>
            <span class="breadcrumb-sep">›</span>
            <b><?= $title ?></b>
        </div>

        <div class="main-content">

            <!-- CỘT TRÁI -->
            <div class="left-column">

                <h2 class="cat-title">
                    RANKING:
                    <span><?= $title ?></span>
                </h2>

                <!-- LIST RANKING -->
                <?php foreach ($posts as $p): ?>
                    <div class="article-item">

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

                                <?php if ($type == 'likes'): ?>
                                    <button class="btn-icon like-btn" data-id="<?= $p['ma_bai_viet'] ?>">
                                        <i class="fa-solid fa-heart"></i>
                                        <?= $p['score'] ?>
                                    </button>
                                <?php endif; ?>

                                <?php if ($type == 'weekview'): ?>
                                    <button class="btn-icon view-btn">
                                        <i class="fa-solid fa-eye"></i>
                                        <?= $p['score'] ?>
                                    </button>
                                <?php endif; ?>

                                <?php if ($type == 'comments'): ?>
                                    <button class="btn-icon cmt-btn">
                                        <i class="fa-solid fa-comment"></i>
                                        <?= $p['score'] ?>
                                    </button>
                                <?php endif; ?>
                            </p>


                            <p class="excerpt">
                                <?= htmlspecialchars(mb_substr(strip_tags($p['noi_dung']), 0, 150)) ?>...
                            </p>
                        </div>

                    </div>
                    <hr class="divider">
                <?php endforeach; ?>


                <!-- PHÂN TRANG -->
                <div class="pagination-minimal">
                    <?php if ($page > 1): ?>
                        <a class="pag-btn" href="?type=<?= $type ?>&page=<?= $page - 1 ?>">
                            ‹ NEWER
                        </a>
                    <?php else: ?>
                        <span class="pag-btn disabled">‹ NEWER</span>
                    <?php endif; ?>

                    <span class="separator">/</span>

                    <?php if ($page < $totalPages): ?>
                        <a class="pag-btn" href="?type=<?= $type ?>&page=<?= $page + 1 ?>">
                            OLDER ›
                        </a>
                    <?php else: ?>
                        <span class="pag-btn disabled">OLDER ›</span>
                    <?php endif; ?>
                </div>

            </div>

            <!-- CỘT PHẢI -->
            <aside class="right-column">
                <h3 class="sidebar-title">POPULAR POSTS</h3>

                <ul class="popular-list">
                    <?php
                    $i = 1;
                    foreach ($popular as $p):
                        ?>
                        <li class="popular-item">
                            <span class="rank"><?= $i++ ?></span>

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
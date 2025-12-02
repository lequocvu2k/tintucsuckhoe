<?php
session_start();              // 🔥 BẮT BUỘC — bạn đang dùng SESSION nhưng chưa bật

require_once '../php/db.php';

/* ===========================
   HÀM ĐỊNH DẠNG NGÀY TIẾNG VIỆT
=========================== */
function formatDateVN($dateString)
{
    if (!$dateString)
        return "";

    $ts = strtotime($dateString);

    $map = [
        "January" => "Tháng 1",
        "February" => "Tháng 2",
        "March" => "Tháng 3",
        "April" => "Tháng 4",
        "May" => "Tháng 5",
        "June" => "Tháng 6",
        "July" => "Tháng 7",
        "August" => "Tháng 8",
        "September" => "Tháng 9",
        "October" => "Tháng 10",
        "November" => "Tháng 11",
        "December" => "Tháng 12"
    ];

    $thang_en = date("F", $ts);
    $thang_vi = $map[$thang_en];

    return date("d ", $ts) . $thang_vi . date(", Y", $ts);
}

/* ===========================
    LẤY KẾT QUẢ TÌM KIẾM + TÁC GIẢ
=========================== */
/* ============================
    PHÂN TRANG KẾT QUẢ TÌM KIẾM
============================ */
$q = trim($_GET['q'] ?? '');

$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$keyword = "%$q%";

/* Đếm tổng bài */
$stmtCount = $pdo->prepare("
    SELECT COUNT(*) 
    FROM baiviet 
    WHERE trang_thai='published'
      AND (tieu_de LIKE :kw OR ma_chuyen_muc LIKE :kw)
");
$stmtCount->bindValue(':kw', $keyword, PDO::PARAM_STR);
$stmtCount->execute();

$totalResults = $stmtCount->fetchColumn();
$totalPages = ceil($totalResults / $limit);

/* Lấy bài theo trang */
$stmt = $pdo->prepare("
    SELECT b.*, kh.ho_ten AS author_name
    FROM baiviet b
    LEFT JOIN khachhang kh ON b.id_kh = kh.id_kh
    WHERE b.trang_thai = 'published'
      AND (b.tieu_de LIKE :kw OR b.ma_chuyen_muc LIKE :kw)
    ORDER BY b.ngay_dang DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':kw', $keyword, PDO::PARAM_STR);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../partials/menu.php';
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <title>Kết quả tìm kiếm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <?php include '../partials/logo.php'; ?>
    <link rel="stylesheet" href="../css/search.css">
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
    <?php include '../partials/login.php'; ?>
    <div class="container">

        <!-- LEFT CONTENT -->
        <div class="search-left">

            <h2 class="search-title">SEARCH RESULTS FOR "<span><?= htmlspecialchars($q) ?></span>"</h2>


            <?php if ($results): ?>
                <?php foreach ($results as $r): ?>

                    <div class="search-item">
                        <a href="post.php?slug=<?= urlencode($r['duong_dan']) ?>" class="search-img">
                            <img src="/php/<?= htmlspecialchars($r['anh_bv']) ?>" alt="">
                        </a>

                        <div class="search-info">

                            <a class="search-heading" href="post.php?slug=<?= urlencode($r['duong_dan']) ?>">
                                <?= htmlspecialchars($r['tieu_de']) ?>
                            </a>

                            <!-- Thông tin bài viết -->
                            <div class="post-meta">
                                <span>By <?= htmlspecialchars($r['author_name']) ?></span> •
                                <span><?= date("d/m/Y", strtotime($r['ngay_dang'])) ?></span>
                            </div>

                            <p class="search-desc">
                                <?= mb_substr(strip_tags(html_entity_decode($r['noi_dung'], ENT_QUOTES, 'UTF-8')), 0, 130) ?>...
                            </p>
                        </div>
                    </div>

                    <hr>

                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-result">Không tìm thấy bài viết.</p>
            <?php endif; ?>
            <?php if ($totalPages > 1): ?>
                <div class="search-pagination">

                    <?php if ($page > 1): ?>
                        <a class="prev-link" href="?q=<?= urlencode($q) ?>&page=<?= $page - 1 ?>">‹ NEWER POSTS</a>
                    <?php endif; ?>

                    <span class="divider">/</span>

                    <?php if ($page < $totalPages): ?>
                        <a class="next-link" href="?q=<?= urlencode($q) ?>&page=<?= $page + 1 ?>">OLDER POSTS ›</a>
                    <?php endif; ?>

                </div>
            <?php endif; ?>

        </div>

        <!-- SIDEBAR RIGHT -->
        <div class="search-right">
            <section class="latest">
                <h2>POPULAR POSTS</h2>
                <ul>
                    <?php
                    // Lấy 5 bài thuộc danh mục POPULAR POSTS + kèm tên tác giả
                    $stmtPopular = $pdo->prepare("
                SELECT b.*, kh.ho_ten AS author_name
                FROM baiviet b
                LEFT JOIN khachhang kh ON b.id_kh = kh.id_kh
                WHERE b.trang_thai = 'published'
                  AND b.danh_muc = 'POPULAR POSTS'
                ORDER BY b.ngay_dang DESC
                LIMIT 5
            ");
                    $stmtPopular->execute();
                    $popular = $stmtPopular->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($popular as $p):
                        ?>
                        <li>
                            <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                <img src="/php/<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                                <div>
                                    <p class="post-title"><?= htmlspecialchars($p['tieu_de']) ?></p>

                                    <p class="author-date">
                                        <span>By
                                            <b><?= htmlspecialchars($p['author_name'] ?? 'Unknown Author') ?></b></span> •
                                        <?= formatDateVN($p['ngay_dang']) ?>
                                    </p>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        </div>


    </div>
    <?php include '../partials/footer.php'; ?>
</body>

</html>
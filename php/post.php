<?php
session_start();
require_once './db.php';

// --- Lấy slug --- 
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    die("<h2 style='text-align:center;color:red;'>❌ Không tìm thấy bài viết!</h2>");
}

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
        // Tính điểm và cấp độ
        function tinhDiem($so_diem)
        {
            return floor($so_diem / 10000);
        }
        function xacDinhCapDo($so_diem)
        {
            if ($so_diem >= 1000000)
                return 'Siêu Kim Cương';
            if ($so_diem >= 500000)
                return 'Kim Cương';
            if ($so_diem >= 100000)
                return 'Vàng';
            if ($so_diem >= 50000)
                return 'Bạc';
            return 'Member';
        }

        $so_diem = isset($user['so_diem']) && is_numeric($user['so_diem']) ? $user['so_diem'] : 0;
        $diem = tinhDiem($so_diem);
        $tier = xacDinhCapDo($so_diem);
    }
}

// ====================== LẤY BÀI VIẾT ======================
$stmt = $pdo->prepare("SELECT * FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    die("<h2 style='text-align:center;color:red;'>❌ Bài viết không tồn tại hoặc đã bị ẩn!</h2>");
}

// --- Cập nhật lượt xem ---
$pdo->prepare("UPDATE baiviet SET luot_xem = luot_xem + 1 WHERE ma_bai_viet = ?")->execute([$post['ma_bai_viet']]);

// --- Lấy tên tác giả (người đăng) ---
$stmt_author = $pdo->prepare("SELECT ho_ten FROM khachhang WHERE id_kh = ?");
$stmt_author->execute([$post['ma_tac_gia']]);
$author = $stmt_author->fetch(PDO::FETCH_ASSOC);
$author_name = $author ? htmlspecialchars($author['ho_ten']) : "Người dùng không xác định";

// --- Lấy bài phổ biến ---
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'POPULAR POSTS'
    ORDER BY ngay_dang DESC
    LIMIT 5
");
$popular = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy bài trước
$stmt_prev = $pdo->prepare("SELECT * FROM baiviet WHERE ngay_dang < ? AND trang_thai = 'published' ORDER BY ngay_dang DESC LIMIT 1");
$stmt_prev->execute([$post['ngay_dang']]);
$prev_post = $stmt_prev->fetch(PDO::FETCH_ASSOC);

// Lấy bài tiếp theo
$stmt_next = $pdo->prepare("SELECT * FROM baiviet WHERE ngay_dang > ? AND trang_thai = 'published' ORDER BY ngay_dang ASC LIMIT 1");
$stmt_next->execute([$post['ngay_dang']]);
$next_post = $stmt_next->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['tieu_de']) ?> - Tin tức sức khỏe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/post.css">
    <link rel="stylesheet" href="../css/menu.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
</head>

<body>
    <canvas class="fireworks"></canvas>
    <!-- ✅ HEADER -->
    <header class="site-header">
        <!-- LOGO -->
        <div class="left">
            <a href="index.php" class="logo-link">
                <img src="../img/logo.svg" alt="Logo" class="logo-img" />
            </a>
        </div>

        <!-- NAVIGATION -->
        <nav class="main-nav" aria-label="Main navigation">
            <ul class="nav-menu">
                <li><a href="index.php">Trang chủ</a></li>

                <li class="dropdowns">
                    <a href="#">Xếp hạng ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="#">Nhiều lượt xem hôm nay</a></li>
                        <li><a href="#">Nhiều lượt xem tuần</a></li>
                        <li><a href="#">Nhiều lượt xem tháng</a></li>
                    </ul>
                </li>

                <li class="dropdowns">
                    <a href="#">Tin tức ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="#">Tập luyện</a></li>
                        <li><a href="#">Nghỉ ngơi</a></li>
                        <li><a href="#">Thủ thuật</a></li>
                        <li><a href="#">Dinh dưỡng</a></li>
                        <li><a href="#">Tinh thần</a></li>
                        <li><a href="#">Mẹo mắt - lưng</a></li>
                    </ul>
                </li>

                <li class="dropdowns">
                    <a href="#">Chương trình tập luyện ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="#">Nhóm cơ</a></li>
                        <li><a href="#">Theo mục tiêu</a></li>
                        <li><a href="#">Tự tạo kế hoạch</a></li>
                    </ul>
                </li>

                <li class="dropdowns">
                    <a href="#">Dinh dưỡng ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="#">Giảm cân</a></li>
                        <li><a href="#">Tăng cơ</a></li>
                        <li><a href="#">Ăn uống lành mạnh</a></li>
                    </ul>
                </li>

                <li><a href="#">Giới thiệu </a></li>
                <li><a href="#">Liên hệ</a></li>
            </ul>
        </nav>

        <!-- PHẦN BÊN PHẢI -->
        <div class="right">
            <!-- Nút tìm kiếm -->
            <button class="icon-btn" id="openSearch" aria-label="Tìm kiếm">
                <i class="fas fa-search"></i>
            </button>

            <!-- Thanh tìm kiếm -->
            <div class="search-bar" id="searchBar">
                <input type="text" placeholder="Tìm kiếm bài viết..." id="searchInput">
                <button id="searchSubmit"><i class="fas fa-arrow-right"></i></button>
            </div>

            <!-- USER INFO -->
            <?php if (isset($_SESSION['username'])): ?>
                <div class="header-user">
                    <div class="avatar-container">
                        <?php
                        // Lấy avatar: nếu có thì dùng avatar của user, nếu không thì dùng avt.jpg mặc định
                        $avatar = (!empty($user['avatar_url']) && file_exists($user['avatar_url']))
                            ? htmlspecialchars($user['avatar_url'])
                            : '../img/avt.jpg';

                        // Khung avatar (frame)
                        $frame = !empty($user['avatar_frame']) && file_exists('../frames/' . $user['avatar_frame'] . '.png')
                            ? '../frames/' . htmlspecialchars($user['avatar_frame']) . '.png'
                            : '';

                        // Hiển thị avatar
                        echo '<img src="' . $avatar . '" alt="Avatar" class="avatar">';
                        if ($frame) {
                            echo '<img src="' . $frame . '" alt="Frame" class="frame-overlay">';
                        }
                        ?>
                    </div>

                    <div class="account-info">
                        <div class="name-container">
                            <p class="name"><?= htmlspecialchars($user['ho_ten']) ?></p>
                            <div class="user-email">
                                <?php if ($user['email'] == 'baka@gmail.com'): ?>
                                    <span class="role-badge">ADMIN</span>
                                <?php else: ?>
                                    <?= htmlspecialchars($user['email']) ?>
                                <?php endif; ?>

                                <!-- Ẩn VIP tier nếu là admin -->
                                <?php if ($user['email'] != 'baka@gmail.com'): ?>
                                    <p>
                                        <b class="vip-tier <?= strtolower(str_replace(' ', '-', $tier)) ?>">
                                            <?= htmlspecialchars($tier) ?>
                                        </b>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Dropdown menu -->
                            <div class="dropdown-menu">
                                <ul>
                                    <li>
                                        <a href="./user.php">
                                            <i class="fas fa-user"></i> Tài khoản
                                            <!-- Kiểm tra nếu người dùng là ADMIN, hiển thị ADMIN -->
                                            <b class="vip-tier">
                                                <?php
                                                if ($_SESSION['username'] === 'admin') {
                                                    echo '<span class="role-badge">ADMIN</span>';  // Hiển thị "ADMIN" với hiệu ứng màu sắc cầu vồng
                                                } else {
                                                    echo htmlspecialchars($tier);  // Hiển thị cấp độ thành viên cho người dùng khác
                                                }
                                                ?>
                                            </b>
                                        </a>
                                    </li>

                                    <li><a href="./user.php?view=order"><i class="fas fa-history"></i> Lịch sử</a></li>
                                    <li><a href="./user.php?view=recharge"><i class="fas fa-wallet"></i> Nạp tiền</a>
                                    </li>
                                    <li><a href="./user.php?view=notifications"><i class="fas fa-bell"></i> Thông
                                            báo</a>
                                    </li>
                                    <?php if ($_SESSION['username'] === 'admin'): ?>
                                        <li><a href="./quanlybv.php"><i class="fas fa-cogs"></i> Quản lý bài viết</a></li>
                                    <?php endif; ?>
                                    <li><a href="./logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <label for="showLogin">Đăng nhập</label>
            <?php endif; ?>
        </div>
    </header>
    <main class="post-container">
        <!-- Cột trái: bài viết -->
        <article class="post-content">
            <h1><?= htmlspecialchars($post['tieu_de']) ?></h1>

            <!-- Thông tin bài viết -->
            <div class="post-meta">
                <span>By <?= $author_name ?></span> •
                <span><?= date("F d, Y", strtotime($post['ngay_dang'])) ?></span>
            </div>

            <?php if (!empty($post['anh_bv'])): ?>
                <img src="<?= htmlspecialchars($post['anh_bv']) ?>" alt="Ảnh bài viết" class="main-image">
            <?php endif; ?>

            <div class="post-body">
                <?= nl2br($post['noi_dung']) ?>
            </div>
            <!-- Thông tin tác giả -->
            <div class="author-info">
                <div class="avatar-container">
                    <?php
                    // Lấy avatar: nếu có thì dùng avatar của user, nếu không thì dùng avt.jpg mặc định
                    $avatar = (!empty($user['avatar_url']) && file_exists($user['avatar_url']))
                        ? htmlspecialchars($user['avatar_url'])
                        : '../img/avt.jpg';

                    // Khung avatar (frame)
                    $frame = !empty($user['avatar_frame']) && file_exists('../frames/' . $user['avatar_frame'] . '.png')
                        ? '../frames/' . htmlspecialchars($user['avatar_frame']) . '.png'
                        : '';

                    // Hiển thị avatar
                    echo '<img src="' . $avatar . '" alt="Avatar" class="avatar">';
                    if ($frame) {
                        echo '<img src="' . $frame . '" alt="Frame" class="frame-overlay">';
                    }
                    ?>
                </div>
                <div class="user-info">
                    <div class="author-name">
                        <?php
                        // Hiển thị tên tác giả
                        echo '<strong>' . htmlspecialchars($user['ho_ten']) . '</strong>';
                        ?>
                    </div>

                    <div class="user-email">
                        <?php if ($user['email'] == 'baka@gmail.com'): ?>
                            <span class="role-badge1">ADMIN</span>
                        <?php else: ?>
                            <?= htmlspecialchars($user['email']) ?>
                        <?php endif; ?>

                        <!-- Ẩn VIP tier nếu là admin -->
                        <?php if ($user['email'] != 'baka@gmail.com'): ?>
                            <p>
                                <b class="vip-tier <?= strtolower(str_replace(' ', '-', $tier)) ?>">
                                    <?= htmlspecialchars($tier) ?>
                                </b>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Hiển thị Bài trước và Bài tiếp theo -->
            <div class="post-navigation">
                <?php if ($prev_post): ?>
                    <a href="post.php?slug=<?= urlencode($prev_post['duong_dan']) ?>" class="prev-post">Bài trước:
                        <?= htmlspecialchars($prev_post['tieu_de']) ?></a>
                <?php else: ?>
                    <span class="no-prev">❌ Không có bài trước</span>
                <?php endif; ?>

                <?php if ($next_post): ?>
                    <a href="post.php?slug=<?= urlencode($next_post['duong_dan']) ?>" class="next-post">Bài tiếp theo:
                        <?= htmlspecialchars($next_post['tieu_de']) ?></a>
                <?php else: ?>
                    <span class="no-next">❌ Không có bài tiếp theo</span>
                <?php endif; ?>
            </div>

        </article>

        <!-- Cột phải: bài phổ biến -->
        <aside class="sidebar">
            <h3>POPULAR POSTS</h3>
            <ul class="popular-list">
                <?php foreach ($popular as $p): ?>
                    <li class="popular-item">
                        <img src="<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                        <div class="info">
                            <a href="post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                <?= htmlspecialchars($p['tieu_de']) ?>
                            </a>
                            <p class="date"><?= date("F d, Y", strtotime($p['ngay_dang'])) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="ads">
                <h4>ADVERTISEMENT</h4>
                <div class="ad-box">AD 1</div>
                <div class="ad-box">AD 2</div>
            </div>
        </aside>
    </main>

    <footer>
        <p>© 2025 Nhóm 6 - Website Tin tức Sức khỏe</p>
    </footer>
</body>

</html>
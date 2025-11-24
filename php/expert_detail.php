<?php
session_start();
require_once './db.php';

$id_chuyen_gia = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id_chuyen_gia <= 0) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>⚠️ Chuyên gia không hợp lệ!</h2>";
    exit;
}

/* ====================== LẤY THÔNG TIN NGƯỜI DÙNG ĐĂNG NHẬP ====================== */
$user = null;
$tier = "Member";

$id_kh = $_SESSION['user_id'] ?? null; // người dùng đăng nhập

if ($id_kh) {
    $stmt = $pdo->prepare("
        SELECT kh.*, tk.ngay_tao
        FROM khachhang kh
        LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
        WHERE kh.id_kh = :id
    ");
    $stmt->execute([':id' => $id_kh]);
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

/* ====================== LẤY THÔNG TIN CHUYÊN GIA ====================== */
$stmt = $pdo->prepare("
    SELECT ho_ten, avatar_url, chuyen_mon, mo_ta_chuyen_gia, is_chuyen_gia
    FROM khachhang
    WHERE id_kh = ?
");
$stmt->execute([$id_chuyen_gia]);
$expert = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expert || !$expert['is_chuyen_gia']) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>⚠️ Không tìm thấy chuyên gia hoặc chuyên gia không được hiển thị!</h2>";
    exit;
}

/* ====================== LẤY BÀI VIẾT CHUYÊN GIA ====================== */
$stmtPost = $pdo->prepare("
    SELECT ma_bai_viet, tieu_de, duong_dan, anh_bv, ngay_dang
    FROM baiviet
    WHERE id_kh = ? AND trang_thai = 'published'
    ORDER BY ngay_dang DESC
    LIMIT 20
");
$stmtPost->execute([$id_chuyen_gia]);
$posts = $stmtPost->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hồ sơ chuyên gia - <?= htmlspecialchars($expert['ho_ten']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/expert_detail.css">
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
                <img src="../img/health-logo.png" alt="Logo" class="logo-img" />
            </a>
        </div>
        <!-- NAVIGATION -->
        <nav class="main-nav" aria-label="Main navigation">
            <ul class="nav-menu">
                <li><a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                <li><a href="./experts.php"><i class="fa-solid fa-user-nurse"></i> Chuyên gia</a></li>
                <li class="dropdowns">
                    <a href="#"><i class="fa-solid fa-ranking-star"></i> Xếp hạng ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="#">Nhiều lượt xem hôm nay</a></li>
                        <li><a href="#">Nhiều lượt xem tuần</a></li>
                        <li><a href="#">Nhiều lượt xem tháng</a></li>
                    </ul>
                </li>

                <li class="dropdowns">
                    <a href="#"><i class="fa-solid fa-heart-pulse"></i> Sức khỏe ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="./category.php?id=1"><i class="fa-solid fa-newspaper"></i> Tin tức</a></li>
                        <li><a href="./category.php?id=2"><i class="fa-solid fa-apple-whole"></i> Dinh dưỡng</a></li>
                        <li><a href="./category.php?id=3"><i class="fa-solid fa-dumbbell"></i> Khỏe đẹp</a></li>
                        <li><a href="./category.php?id=4"><i class="fa-solid fa-user-doctor"></i> Tư vấn</a></li>
                        <li><a href="./category.php?id=5"><i class="fa-solid fa-hospital"></i> Dịch vụ y tế</a></li>
                        <li><a href="./category.php?id=6"><i class="fa-solid fa-virus-covid"></i> Các bệnh</a></li>
                    </ul>
                </li>

                <li class="dropdowns">
                    <a href="#"><i class="fa-solid fa-circle-info"></i> Giới thiệu ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="./about.php#about"><i class="fa-solid fa-circle-info"></i> Về chúng tôi</a></li>
                        <li><a href="./about.php#mission"><i class="fa-solid fa-bullseye"></i> Tầm nhìn & Sứ mệnh</a>
                        </li>
                        <li><a href="./about.php#policy"><i class="fa-solid fa-scale-balanced"></i> Chính sách biên
                                tập</a></li>
                        <li><a href="./about.php#team"><i class="fa-solid fa-people-group"></i> Đội ngũ</a></li>
                    </ul>
                </li>

                <li class="dropdowns">
                    <a href="#"><i class="fa-solid fa-envelope-circle-check"></i> Liên hệ ▾</a>
                    <ul class="dropdown-nav">
                        <li><a href="mailto:vuliztva1@gmail.com"><i class="fa-solid fa-envelope"></i> Email hỗ trợ</a>
                        </li>
                        <li><a href="https://www.facebook.com/Shiroko412/" target="_blank"><i
                                    class="fa-brands fa-facebook"></i> Fanpage Facebook</a></li>
                        <li><a href="https://zalo.me/0332138297" target="_blank"><i class="fa-brands fa-zhihu"></i> Zalo
                                liên hệ</a></li>
                        <li><a href="../mail/formmail.php"><i class="fa-solid fa-pen-to-square"></i> Gửi phản hồi</a>
                        </li>
                    </ul>
                </li>
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
                <ul id="searchSuggestions" class="search-suggestions"></ul>
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
                        $frame = '';
                        if (!empty($user['avatar_frame'])) {
                            $possibleExtensions = ['png', 'gif', 'jpg', 'jpeg'];
                            foreach ($possibleExtensions as $ext) {
                                $path = '../frames/' . htmlspecialchars($user['avatar_frame']) . '.' . $ext;
                                if (file_exists($path)) {
                                    $frame = $path;
                                    break;
                                }
                            }
                        }

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
                                            <b
                                                class="vip-tier <?= ($_SESSION['username'] === 'admin') ? 'admin' : strtolower(str_replace(' ', '-', $tier)) ?>">
                                                <?php
                                                if ($_SESSION['username'] === 'admin') {
                                                    echo '<span class="role-badge">ADMIN</span>';  // Hiển thị "ADMIN" cho người dùng admin
                                                } else {
                                                    echo htmlspecialchars($tier);  // Hiển thị cấp độ thành viên cho người dùng khác
                                                }
                                                ?>
                                            </b>

                                        </a>
                                    </li>

                                    <li><a href="./user.php?view=history"><i class="fas fa-history"></i> Lịch sử</a></li>
                                    <li><a href="./user.php?view=saved"><i class="fas fa-bookmark"></i> Đã lưu</a></li>
                                    <li><a href="./user.php?view=notifications"><i class="fas fa-bell"></i> Thông báo</a>
                                    </li>
                                    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'NhanVien' || $_SESSION['user_role'] === 'QuanTri')): ?>
                                        <li><a href="./expert_profile.php"><i class="fa-solid fa-user-doctor"></i> Hồ sơ Chuyên
                                                gia</a></li>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'QuanTri' || $_SESSION['user_role'] === 'NhanVien')): ?>
                                        <li class="dropdown">
                                            <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-cogs"></i> Quản
                                                lý</a>
                                            <ul class="dropdown-content">
                                                <li><a href="./quanlybv.php"><i class="fas fa-pencil-alt"></i> Quản lý bài
                                                        viết</a></li>
                                                <?php if ($_SESSION['user_role'] === 'QuanTri'): ?>
                                                    <li><a href="./quanlyyeucau.php"><i class="fas fa-list"></i> Quản lý yêu cầu</a>
                                                    </li>
                                                    <li><a href="./hethongduyetbai.php"><i class="fas fa-check-circle"></i> Duyệt
                                                            bài viết</a></li>
                                                <?php endif; ?>
                                            </ul>
                                        </li>
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

    <div class="expert-detail-wrapper">
        <!-- Thông tin chuyên gia -->
        <div class="expert-info-card">
            <?php if (isset($_GET['sent']) && $_GET['sent'] == 1): ?>
                <div class="alert-success">
                    🎉 <b>Bạn đã gửi câu hỏi thành công!</b> Vui lòng chờ chuyên gia trả lời.
                </div>
            <?php endif; ?>

            <div class="avatar">
                <img src="<?= htmlspecialchars($expert['avatar_url'] ?: './img/avt.jpg') ?>" alt="Avatar">
            </div>
            <h1><?= htmlspecialchars($expert['ho_ten'] ?: 'Chưa có tên') ?></h1>
            <?php if (!empty($expert['chuyen_mon'])): ?>
                <div class="expert-tag">Chuyên môn: <?= htmlspecialchars($expert['chuyen_mon']) ?></div>
            <?php endif; ?>

            <?php if (!empty($expert['mo_ta_chuyen_gia'])): ?>
                <p>Giới thiệu: <?= nl2br(htmlspecialchars($expert['mo_ta_chuyen_gia'])) ?></p>
            <?php else: ?>
                <p>Chuyên gia chưa cập nhật phần mô tả chi tiết.</p>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="ask-box">
                    <h3>🗨️ Đặt câu hỏi cho chuyên gia</h3>
                    <form action="send_question.php" method="POST">
                        <input type="hidden" name="id_chuyen_gia" value="<?= $id_chuyen_gia ?>">
                        <textarea name="question" placeholder="Nhập câu hỏi của bạn về sức khỏe..." required></textarea>
                        <button type="submit" class="ask-btn">Gửi câu hỏi</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="login-ask">⚠️ Vui lòng đăng nhập để đặt câu hỏi!</p>
            <?php endif; ?>

        </div>

        <!-- Danh sách bài viết -->
        <div class="expert-posts">
            <h2>Bài viết của chuyên gia</h2>
            <?php if (!$posts): ?>
                <p>Chuyên gia chưa có bài viết nào được hiển thị.</p>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <div class="expert-post-item">
                        <?php if (!empty($p['anh_bv'])): ?>
                            <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                <img src="<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                            </a>
                        <?php endif; ?>

                        <div>
                            <div class="expert-post-item-title">
                                <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                    <?= htmlspecialchars($p['tieu_de']) ?>
                                </a>
                            </div>
                            <div class="expert-post-meta">
                                Đăng ngày: <?= date("d/m/Y", strtotime($p['ngay_dang'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-column">
                <h3>🩺 Về chúng tôi</h3>
                <p>
                    “Tin tức Sức khỏe” là nền tảng chia sẻ kiến thức về tập luyện, dinh dưỡng và chăm sóc tinh thần,
                    giúp bạn sống khỏe hơn mỗi ngày.
                </p>
            </div>

            <div class="footer-column">
                <h3>📚 Thông tin</h3>
                <ul>
                    <li><a href="./about.php#mission">Tầm nhìn & Sứ mệnh</a></li>
                    <li><a href="./about.php#policy">Chính sách biên tập</a></li>
                    <li><a href="./about.php#team">Đội ngũ biên tập</a></li>
                    <li><a href="./about.php#about">Về chúng tôi</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>📞 Liên hệ</h3>
                <ul>
                    <li><i class="fa-solid fa-envelope"></i> <a
                            href="mailto:vuliztva1@gmail.com">vuliztva1@gmail.com</a></li>
                    <li><i class="fa-brands fa-facebook"></i> <a href="https://facebook.com/Shiroko412"
                            target="_blank">Fanpage Facebook</a></li>
                    <li><i class="fa-brands fa-zhihu"></i> <a href="https://zalo.me/0332138297" target="_blank">Zalo hỗ
                            trợ</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h3>🌐 Kết nối</h3>
                <div class="social-icons">
                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            © 2025 <strong>Nhóm 6</strong> — Tin tức Sức khỏe 🌱 | Lan tỏa kiến thức · Sống khỏe mỗi ngày
        </div>
    </footer>
</body>

</html>
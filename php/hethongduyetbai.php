<?php
session_start();
require_once './db.php';

// ❌ Chặn người không phải ADMIN
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'QuanTri') {
    echo "<h2 style='color:red; text-align:center; margin-top:50px;'>🚫 Bạn không có quyền truy cập trang này!</h2>";
    exit;
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
                <li><a href="./advice.php"><i class="fa-solid fa-stethoscope"></i> Tư vấn theo triệu chứng</a></li>
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
                        <img src="<?= htmlspecialchars($post['anh_bv']) ?>" alt="Ảnh bài viết">
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
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

// --- Lấy bài viết theo slug ---
$stmt = $pdo->prepare("SELECT * FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die("<h2 style='text-align:center;color:red;'>❌ Bài viết không tồn tại hoặc đã bị ẩn!</h2>");
}

// --- Cập nhật lượt xem ---
$pdo->prepare("UPDATE baiviet SET luot_xem = luot_xem + 1 WHERE ma_bai_viet = ?")
    ->execute([$post['ma_bai_viet']]);

if (isset($_SESSION['user_id'])) {
    $id_kh = $_SESSION['user_id'];
    $ma_bai_viet = $post['ma_bai_viet'];

    // Kiểm tra nếu người dùng đã đọc bài trong vòng 24 giờ chưa
    $check = $pdo->prepare("
    SELECT COUNT(*) 
    FROM diemdoc 
    WHERE id_kh = :id_kh 
      AND ma_bai_viet = :ma_bai_viet 
      AND loai_giao_dich = 'xem_bai' 
      AND ngay_them >= NOW() - INTERVAL 1 DAY
");
    $check->execute(['id_kh' => $id_kh, 'ma_bai_viet' => $post['ma_bai_viet']]);
    $already_added = $check->fetchColumn();
    // Nếu chưa đọc trong 24 giờ, cộng điểm và ghi lại
    if ($already_added == 0) {
        // Cộng điểm cho người dùng
        $points_to_add = max(50, round(strlen(strip_tags($post['noi_dung'])) / 500));  // Điều chỉnh số điểm cộng tùy theo độ dài bài viết

        // Cập nhật điểm trong bảng khachhang
        $stmt_update = $pdo->prepare("
        UPDATE khachhang 
        SET so_diem = so_diem + :diem 
        WHERE id_kh = :id_kh
    ");
        $stmt_update->execute(['diem' => $points_to_add, 'id_kh' => $id_kh]);

        // Ghi lại lịch sử cộng điểm
        $stmt_log = $pdo->prepare("
        INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
        VALUES (:id_kh, :ma_bai_viet, :diem_cong, 'xem_bai', NOW())
    ");
        $stmt_log->execute([
            'id_kh' => $id_kh,
            'ma_bai_viet' => $post['ma_bai_viet'],
            'diem_cong' => $points_to_add
        ]);

        // Thông báo cộng điểm
        echo "
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const popup = document.createElement('div');
            popup.textContent = '+{$points_to_add} điểm!';
            popup.style.position = 'fixed';
            popup.style.bottom = '80px';
            popup.style.right = '30px';
            popup.style.background = 'rgba(0, 200, 0, 0.9)';
            popup.style.color = '#fff';
            popup.style.padding = '10px 20px';
            popup.style.borderRadius = '10px';
            popup.style.fontWeight = 'bold';
            popup.style.fontSize = '18px';
            popup.style.zIndex = '9999';
            popup.style.boxShadow = '0 0 10px rgba(0,0,0,0.3)';
            popup.style.transition = 'all 0.5s ease';
            document.body.appendChild(popup);
            setTimeout(() => { popup.style.opacity = '0'; popup.style.transform = 'translateY(-50px)'; }, 2000);
            setTimeout(() => { popup.remove(); }, 2500);
        });
    </script>
    ";
    }
    if ($check->rowCount() == 0) {
        // Ghi lại lịch sử xem bài viết
        $insert = $pdo->prepare("
            INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
            VALUES (?, ?, 0, 'xem_bai', NOW())
        ");
        $insert->execute([$id_kh, $ma_bai_viet]);
    }
}

// --- Lấy thông tin tác giả ---
$stmt_author = $pdo->prepare("SELECT ho_ten, email, avatar_url, avatar_frame FROM khachhang WHERE id_kh = ?");
$stmt_author->execute([$post['id_kh']]);
$author = $stmt_author->fetch(PDO::FETCH_ASSOC);

// --- Gán mặc định để tránh lỗi ---
$author_name = $author && !empty($author['ho_ten']) ? htmlspecialchars($author['ho_ten']) : "Không rõ tác giả";
$author_email = $author && !empty($author['email']) ? htmlspecialchars($author['email']) : "";
$author_avatar = $author && !empty($author['avatar_url']) ? htmlspecialchars($author['avatar_url']) : "../img/avt.jpg";
$author_frame = $author && !empty($author['avatar_frame']) ? htmlspecialchars($author['avatar_frame']) : "";

// --- Lấy bài phổ biến ---
$stmt = $pdo->query("SELECT * FROM baiviet WHERE trang_thai='published' AND danh_muc='POPULAR POSTS' ORDER BY ngay_dang DESC LIMIT 5");
$popular = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Lấy bài trước & tiếp theo ---
// Lấy bài trước
$stmt_prev = $pdo->prepare("SELECT * FROM baiviet WHERE ngay_dang < ? AND trang_thai='published' ORDER BY ngay_dang DESC LIMIT 1");
$stmt_prev->execute([$post['ngay_dang']]);
$prev_post = $stmt_prev->fetch(PDO::FETCH_ASSOC);

// Lấy bài tiếp theo
$stmt_next = $pdo->prepare("SELECT * FROM baiviet WHERE ngay_dang > ? AND trang_thai='published' ORDER BY ngay_dang ASC LIMIT 1");
$stmt_next->execute([$post['ngay_dang']]);
$next_post = $stmt_next->fetch(PDO::FETCH_ASSOC);

// --- Lấy bình luận ---
$orderBy = "ORDER BY c.ngay_binhluan DESC";
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'oldest':
            $orderBy = "ORDER BY c.ngay_binhluan ASC";
            break;
        case 'name_asc':
            $orderBy = "ORDER BY kh.ho_ten ASC";
            break;
        case 'name_desc':
            $orderBy = "ORDER BY kh.ho_ten DESC";
            break;
    }
}
$stmt_comments = $pdo->prepare("
    SELECT c.*, kh.ho_ten, kh.avatar_url, kh.avatar_frame 
    FROM binhluan c
    JOIN khachhang kh ON c.id_kh = kh.id_kh
    WHERE c.ma_bai_viet = ? $orderBy
");
$stmt_comments->execute([$post['ma_bai_viet']]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
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
    <link rel="stylesheet" href="../css/popup.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/popup.js"></script>
    <script src="../js/post.js" defer></script>
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
    <!-- Các Radio Buttons -->
    <input type="radio" name="popup" id="showLogin" hidden>
    <input type="radio" name="popup" id="showSignup" hidden>
    <input type="radio" name="popup" id="hidePopup" hidden checked>

    <!-- Popup Login -->
    <div class="popup" id="loginPopup">
        <div class="popup-content">
            <!-- Thêm hình ảnh tròn -->
            <div class="avatar-container">
                <img src="../img/yuuka.png" alt="Avatar" class="avatar-circle">
            </div>
            <h2>Đăng nhập</h2>
            <form method="post" action="./login.php" autocomplete="off">
                <input type="text" name="username" placeholder="Tên đăng nhập" required><br><br>

                <div class="password-wrapper">
                    <input type="password" name="password" id="loginPassword" placeholder="Mật khẩu" required>
                    <span class="toggle-password" data-target="loginPassword"><i class="fa fa-eye"></i></span>
                </div>

                <button type="submit">Đăng nhập</button>
            </form>
            <label for="hidePopup" class="close-btn">Đóng</label>
            <label for="showSignup" class="switch-link">Chưa có tài khoản? Đăng ký</label>
        </div>
    </div>

    <!-- Popup Signup -->
    <div class="popup" id="signupPopup">
        <div class="popup-content">
            <!-- Thêm hình ảnh tròn -->
            <div class="avatar-container">
                <img src="../img/yuuka.png" alt="Avatar" class="avatar-circle">
            </div>
            <h2>Đăng ký</h2>
            <form method="POST" action="./signup.php" autocomplete="off">
                <input type="text" name="username" placeholder="Tên đăng nhập" required><br><br>
                <input type="text" name="ho_ten" placeholder="Họ và tên" required><br><br>
                <input type="email" name="email" placeholder="Email" required><br><br>

                <div class="password-wrapper">
                    <input type="password" name="password" id="signupPassword" placeholder="Mật khẩu" required>
                    <span class="toggle-password" data-target="signupPassword"><i class="fa fa-eye"></i></span>
                </div>

                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="signupConfirmPassword"
                        placeholder="Xác nhận mật khẩu" required>
                    <span class="toggle-password" data-target="signupConfirmPassword"><i class="fa fa-eye"></i></span>
                </div>

                <button type="submit">Đăng ký</button>
            </form>
            <label for="hidePopup" class="close-btn">Đóng</label>
            <br>
            <label for="showLogin" class="switch-link">Đã có tài khoản? Đăng nhập</label>
        </div>
    </div>

    <br>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="message-error">
            <?= htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php elseif (isset($_SESSION['signup_error'])): ?>
        <div class="message-error">
            <?= htmlspecialchars($_SESSION['signup_error']); ?>
        </div>
        <?php unset($_SESSION['signup_error']); ?>
    <?php elseif (isset($_SESSION['login_error'])): ?>
        <div class="message-error">
            <?= htmlspecialchars($_SESSION['login_error']); ?>
        </div>
        <?php unset($_SESSION['login_error']); ?>
    <?php elseif (isset($_SESSION['msg'])): ?>
        <div class="message-success">
            <?= htmlspecialchars($_SESSION['msg']); ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <main class="post-container">
        <!-- Cột trái: bài viết -->
        <article class="post-content">
            <h1><?= htmlspecialchars($post['tieu_de']) ?></h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="save_post.php">
                    <input type="hidden" name="ma_bai_viet" value="<?= $post['ma_bai_viet'] ?>">
                    <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">

                    <?php
                    // Kiểm tra đã lưu chưa
                    $checkSaved = $pdo->prepare("SELECT COUNT(*) FROM saved_posts WHERE id_kh = ? AND ma_bai_viet = ?");
                    $checkSaved->execute([$_SESSION['user_id'], $post['ma_bai_viet']]);
                    $isSaved = $checkSaved->fetchColumn() > 0;
                    ?>

                    <button type="submit" class="save-btn">
                        <?php if ($isSaved): ?>
                            <i class="fa-solid fa-bookmark" style="color:#066a49"></i> Đã lưu
                        <?php else: ?>
                            <i class="fa-regular fa-bookmark"></i> Lưu bài viết
                        <?php endif; ?>
                    </button>
                </form>
            <?php endif; ?>

            <p><i class="fas fa-eye"></i> <?= $post['luot_xem'] ?> lượt xem</p>

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

            <div class="user-info">
                <div class="author-name">
                    <?php
                    // Hiển thị tên tác giả (lấy từ thông tin trong cơ sở dữ liệu)
                    echo '<strong>' . htmlspecialchars($author_name) . '</strong>';
                    ?>
                </div>

                <!-- Hiển thị avatar và frame -->
                <div class="avatar-container">
                    <!-- Hiển thị avatar -->
                    <img src="<?= $author_avatar ?>" alt="Avatar" class="avatar">

                    <!-- Hiển thị frame nếu có -->
                    <?php
                    $frame = '';
                    if (!empty($author_frame)) {
                        $possibleExtensions = ['png', 'gif', 'jpg', 'jpeg'];
                        foreach ($possibleExtensions as $ext) {
                            $path = "../frames/" . htmlspecialchars($author_frame) . "." . $ext;
                            if (file_exists($path)) {
                                $frame = $path;
                                break;
                            }
                        }
                    }
                    if (!empty($frame)): ?>
                        <img src="<?= $frame ?>" alt="Frame" class="frame-overlay">
                    <?php endif; ?>
                </div>
                <div class="user-email">
                    <?php
                    // Hiển thị "ADMIN" nếu email là 'baka@gmail.com' từ tác giả
                    if ($author_email == 'baka@gmail.com'): ?>
                        <span class="role-badge1">ADMIN</span>
                    <?php else: ?>
                        <!-- Ẩn VIP tier nếu là admin -->
                        <?php if ($user['email'] != 'baka@gmail.com'): ?>
                            <p>
                                <b class="vip-tier1 <?= strtolower(str_replace(' ', '-', $tier)) ?>">
                                    <?= htmlspecialchars($tier) ?>
                                </b>
                            </p>
                        <?php endif; ?>
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
            <section class="related-posts">
                <h2>BẠN CÓ THỂ THÍCH</h2>
                <div class="related-grid">
                    <?php
                    // Cập nhật LIMIT từ 6 thành 4 để lấy 4 bài ngẫu nhiên
                    $stmt_related = $pdo->prepare("
            SELECT * FROM baiviet 
            WHERE ma_bai_viet != ? AND trang_thai = 'published'
            ORDER BY RAND()  -- Sắp xếp ngẫu nhiên
            LIMIT 4          -- Lấy 4 bài viết
        ");
                    $stmt_related->execute([$post['ma_bai_viet']]);  // Lấy bài viết không phải bài hiện tại
                    $related = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($related as $r): ?>
                        <div class="related-item">
                            <a href="post.php?slug=<?= urlencode($r['duong_dan']) ?>">
                                <img src="<?= htmlspecialchars($r['anh_bv']) ?>" alt="">
                                <h3><?= htmlspecialchars($r['tieu_de']) ?></h3>
                                <p><?= date("F d, Y", strtotime($r['ngay_dang'])) ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <div class="comment-section">
                <h3>THAM GIA BÌNH LUẬN</h3>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form class="comment-form" action="comment.php?slug=<?= htmlspecialchars($slug) ?>" method="POST">
                        <textarea name="comment_text" placeholder="Leave a comment..." required></textarea>
                        <button type="submit" class="submit-btn">SUBMIT</button>
                    </form>
                <?php else: ?>
                    <div class="login-prompt">
                        <p>Please login or register to comment.</p>
                        <label for="showLogin" class="login-link">Sign in</label> |
                        <label for="showSignup" class="signup-link">Sign up</label>
                    </div>
                <?php endif; ?>

                <!-- Dropdown sắp xếp -->
                <div class="sort-comments">
                    <label for="sort">Sắp xếp bình luận: </label>
                    <select name="sort" id="sort"
                        onchange="window.location.href = 'post.php?slug=<?= urlencode($slug) ?>&sort=' + this.value;">
                        <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất
                        </option>
                        <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất</option>
                        <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Tên (A → Z)
                        </option>
                        <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Tên (Z →
                            A)</option>
                    </select>
                </div>

                <!-- Hiển thị bình luận -->
                <div id="comments-container">
                    <?php
                    if ($comments):
                        foreach ($comments as $comment):
                            ?>
                            <div class="comment" id="comment-<?= $comment['id_binhluan'] ?>">
                                <!-- Hiển thị avatar và frame -->
                                <div class="avatar-container">
                                    <!-- Hiển thị avatar -->
                                    <img src="<?= $author_avatar ?>" alt="Avatar" class="avatar">

                                    <!-- Hiển thị frame nếu có -->
                                    <?php
                                    $frame = '';
                                    if (!empty($author_frame)) {
                                        $possibleExtensions = ['png', 'gif', 'jpg', 'jpeg'];
                                        foreach ($possibleExtensions as $ext) {
                                            $path = "../frames/" . htmlspecialchars($author_frame) . "." . $ext;
                                            if (file_exists($path)) {
                                                $frame = $path;
                                                break;
                                            }
                                        }
                                    }

                                    if (!empty($frame)): ?>
                                        <img src="<?= $frame ?>" alt="Frame" class="frame-overlay">
                                    <?php endif; ?>
                                </div>


                                <div class="comment-text" id="comment-text-<?= $comment['id_binhluan'] ?>">
                                    <p><strong><?= htmlspecialchars($comment['ho_ten']) ?></strong>
                                    <div class="user-email">
                                        <?php if ($user['email'] == 'baka@gmail.com'): ?>
                                            <span class="role-badge1">ADMIN</span>
                                        <?php else: ?>
                                        <?php endif; ?>

                                        <!-- Ẩn VIP tier nếu là admin -->
                                        <?php if ($user['email'] != 'baka@gmail.com'): ?>
                                            <p>
                                                <b class="vip-tier1 <?= strtolower(str_replace(' ', '-', $tier)) ?>">
                                                    <?= htmlspecialchars($tier) ?>
                                                </b>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <span
                                        class="comment-time"><?= date("F d, Y H:i", strtotime($comment['ngay_binhluan'])) ?></span>
                                    </p>
                                    <p><?= nl2br(htmlspecialchars($comment['noi_dung'])) ?></p>

                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['id_kh']): ?>
                                        <a href="javascript:void(0);" class="edit-comment"
                                            onclick="editComment(<?= $comment['id_binhluan'] ?>)">Sửa</a>
                                        <a href="javascript:void(0);" class="delete-comment"
                                            onclick="deleteComment(<?= $comment['id_binhluan'] ?>, '<?= urlencode($slug) ?>')">Xóa</a>
                                    <?php endif; ?>
                                </div>
                                <br>
                            </div>
                            <?php
                        endforeach;
                    else:
                        echo "<p>Chưa có bình luận nào.</p>";
                    endif;
                    ?>
                </div>
            </div>

        </article>

        <!-- Cột phải: bài phổ biến -->
        <aside class="sidebar">
            <h3>POPULAR POSTS</h3>
            <ul class="popular-list">
                <?php foreach ($popular as $p): ?>
                    <li class="popular-item">
                        <!-- Bọc ảnh trong thẻ <a> -->
                        <a href="post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                            <img src="<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                        </a>
                        <div class="info">
                            <!-- Tiêu đề vẫn là một liên kết -->
                            <a href="post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                <?= htmlspecialchars($p['tieu_de']) ?>
                            </a>
                            <p class="date"><?= date("F d, Y", strtotime($p['ngay_dang'])) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="ads">

                <div class="ad-box">Advertisement</div>
                <br>
                <div class="ad-box">Advertisement</div>
            </div>
        </aside>
    </main>
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
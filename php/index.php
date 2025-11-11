<?php
session_start();
require_once './db.php'; // file bạn đã có
// Lấy thông tin user
$user_id = $_SESSION['user_id'] ?? null; // Đảm bảo user_id đã được lưu trong session

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $ho_ten = trim($_POST["ho_ten"] ?? "");
    $email = trim($_POST["email"] ?? "");

    // Kiểm tra bắt buộc
    if ($username === "" || $password === "" || $confirm_password === "" || $ho_ten === "" || $email === "") {
        $_SESSION["signup_error"] = "❌ Vui lòng điền đầy đủ thông tin!";
        header("Location: index.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION["signup_error"] = "❌ Mật khẩu xác nhận không khớp!";
        header("Location: index.php");
        exit;
    }

    // Kiểm tra username đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT id_tk FROM taotaikhoan WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $_SESSION["signup_error"] = "❌ Tên đăng nhập đã tồn tại!";
        header("Location: index.php");
        exit;
    }

    // Kiểm tra email đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT id_kh FROM khachhang WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION["signup_error"] = "❌ Email đã được sử dụng!";
        header("Location: index.php");
        exit;
    }

    // Thêm khách hàng mới vào bảng khachhang trước
    $stmt = $pdo->prepare("INSERT INTO khachhang (ho_ten, email) VALUES (?, ?)");
    if (!$stmt->execute([$ho_ten, $email])) {
        $_SESSION["signup_error"] = "❌ Lỗi khi thêm khách hàng!";
        header("Location: index.php");
        exit;
    }

    // Lấy id_kh vừa tạo
    $id_kh = $pdo->lastInsertId();

    $hashedPassword = $password; // lưu mật khẩu chưa mã hóa (không khuyến nghị)

    // Thêm tài khoản vào taotaikhoan kèm id_kh làm khóa ngoại
    $stmt = $pdo->prepare("INSERT INTO taotaikhoan (username, password, id_kh) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $hashedPassword, $id_kh])) {
        $_SESSION["msg"] = "✅ Đăng ký thành công!";
        $_SESSION["username"] = $username;
    } else {
        $_SESSION["signup_error"] = "❌ Có lỗi xảy ra, vui lòng thử lại!";
    }

    header("Location: index.php");
    exit;
}

if ($user_id) {
    try {
        $stmt = $pdo->prepare("SELECT ho_ten, email, so_diem, dia_chi, sdt, avatar_url, avatar_frame FROM khachhang WHERE id_kh = ?");
        $stmt->execute([$user_id]);
        $fetchedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fetchedUser) {
            $user = $fetchedUser; // Gán dữ liệu thực tế
        }
    } catch (PDOException $e) {
        die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
    }
}
function tinhDiem($so_diem)
{
    return floor($so_diem / 10000); // 1 điểm = 10.000đ
}

// Hàm xác định cấp độ
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

// Editor’s Picks (3 bài thuộc danh mục EDITOR'S PICKS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = \"EDITOR'S PICKS\"
    ORDER BY ngay_dang DESC
    LIMIT 3
");

$editors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Main Highlights (6 bài thuộc danh mục HIGHLIGHT)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'MAIN HIGHLIGHTS'
    ORDER BY ngay_dang DESC
    LIMIT 6
");
$highlight = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Latest posts (8 bài thuộc danh mục LATEST POSTS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'LATEST POSTS'
    ORDER BY ngay_dang DESC
    LIMIT 8
");
$latest = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Popular posts (5 bài thuộc danh mục POPULAR POSTS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'POPULAR POSTS'
    ORDER BY ngay_dang DESC
    LIMIT 5
");
$popular = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Rankings (3 bài thuộc danh mục RANKINGS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'RANKINGS'
    ORDER BY ngay_dang DESC
    LIMIT 3
");
$rankings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Interviews (3 bài thuộc danh mục INTERVIEWS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'INTERVIEWS'
    ORDER BY ngay_dang DESC
    LIMIT 3
");
$interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recommendations (3 bài thuộc danh mục RECOMMENDATIONS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'RECOMMENDATIONS'
    ORDER BY ngay_dang DESC
    LIMIT 3
");
$recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tin tức sức khỏe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/index.css">
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
                                    <li><a href="./user.php?view=recharge"><i class="fas fa-wallet"></i> Nạp tiền</a></li>
                                    <li><a href="./user.php?view=notifications"><i class="fas fa-bell"></i> Thông báo</a>
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

    <!-- Overlay tìm kiếm -->
    <div id="searchOverlay" class="search-overlay" aria-hidden="true">
        <div class="search-box">
            <input type="text" placeholder="Tìm kiếm bài viết..." id="searchInput" />
            <button id="searchSubmit" class="btn">Tìm</button>
            <button id="closeSearch" class="btn-close" aria-label="Đóng">✕</button>
        </div>
    </div>
    <!-- Popup -->
    <?php $popupChecked = isset($_GET['error']) ? 'checked' : ''; ?>
    <input type="radio" name="popup" id="showLogin" hidden>
    <input type="radio" name="popup" id="showSignup" hidden>
    <input type="radio" name="popup" id="hidePopup" hidden checked>
    <!-- Popup Login -->
    <div class="popup" id="loginPopup">
        <div class="popup-content">
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

    <main class="container">
        <div class="top-grid">
            <!-- LEFT: Editor's Picks -->
            <section class="editors">
                <h2>EDITOR'S PICKS</h2>
                <?php foreach ($editors as $e): ?>
                    <div class="editor-item">
                        <a href="./post.php?slug=<?= urlencode($e['duong_dan']) ?>">
                            <img src="<?= htmlspecialchars($e['anh_bv']) ?>" alt="">
                            <div class="editor-info">
                                <h3><?= htmlspecialchars($e['tieu_de']) ?></h3>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </section>

            <!-- RIGHT: Main Highlights -->
            <section class="highlights">
                <div class="slider-container">
                    <div class="slider">
                        <?php
                        // Chia $highlight thành nhóm 4 bài / slide
                        $chunks = array_chunk($highlight, 4);
                        foreach ($chunks as $group): ?>
                            <div class="slide">
                                <div class="slide-grid">
                                    <?php foreach ($group as $h): ?>
                                        <div class="slide-item">
                                            <a href="./post.php?slug=<?= urlencode($h['duong_dan']) ?>">
                                                <img src="<?= htmlspecialchars($h['anh_bv']) ?>" alt="">
                                                <div class="overlay">
                                                    <h3><?= htmlspecialchars($h['tieu_de']) ?></h3>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="prev">&#10094;</button>
                    <button class="next">&#10095;</button>
                </div>
            </section>

        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">
            <section class="latest">
                <h2>LATEST POSTS</h2>
                <div class="latest-grid">
                    <?php foreach ($latest as $l): ?>
                        <div class="latest-item">
                            <a href="./post.php?slug=<?= urlencode($l['duong_dan']) ?>">
                                <img src="<?= htmlspecialchars($l['anh_bv']) ?>" alt="">
                                <p><?= htmlspecialchars($l['tieu_de']) ?></p>
                            </a>
                        <?php endforeach; ?>
                    </div>
            </section>

            <aside class="popular">
                <section class="latest">
                    <h2>POPULAR POSTS</h2>
                    <ul>
                        <?php foreach ($popular as $p): ?>
                            <li>
                                <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                    <img src="<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                                    <div>
                                        <p><?= htmlspecialchars($p['tieu_de']) ?></p>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
            </aside>
        </div>

        <div class="triple-section">
            <!-- Rankings -->
            <section class="rankings">
                <h2>RANKINGS</h2>
                <?php foreach ($rankings as $r): ?>
                    <div class="post-item">
                        <a href="./post.php?slug=<?= urlencode($r['duong_dan']) ?>" class="post-link">
                            <img src="<?= htmlspecialchars($r['anh_bv']) ?>" alt="">
                            <div class="post-info">
                                <h3><?= htmlspecialchars($r['tieu_de']) ?></h3>
                            </div>
                        </a>
                        <p class="meta">by <?= htmlspecialchars($r['tac_gia']) ?> |
                            <?= date("F d, Y", strtotime($r['ngay_dang'])) ?>
                        </p>
                    </div>
            </div>
        <?php endforeach; ?>
        </section>

        <section class="interviews">
            <h2>INTERVIEWS</h2>
            <?php foreach ($interviews as $i): ?>
                <div class="post-item">
                    <a href="./post.php?slug=<?= urlencode($r['duong_dan']) ?>" class="post-link">
                        <img src="<?= htmlspecialchars($r['anh_bv']) ?>" alt="">
                        <div class="post-info">
                            <h3><?= htmlspecialchars($r['tieu_de']) ?></h3>
                        </div>
                    </a>
                    </h3>
                    <p class="meta">by <?= htmlspecialchars($i['tac_gia']) ?> |
                        <?= date("F d, Y", strtotime($i['ngay_dang'])) ?>
                    </p>
                </div>
                </div>
            <?php endforeach; ?>
        </section>
        <!-- Recommendations -->
        <section class="recommendations">
            <h2>RECOMMENDATIONS</h2>
            <?php foreach ($recommendations as $rec): ?>
                <div class="post-item">
                    <a href="./post.php?slug=<?= urlencode($r['duong_dan']) ?>" class="post-link">
                        <img src="<?= htmlspecialchars($r['anh_bv']) ?>" alt="">
                        <div class="post-info">
                            <h3><?= htmlspecialchars($r['tieu_de']) ?></h3>
                        </div>
                    </a>
                    </h3>
                    <p class="meta">by <?= htmlspecialchars($rec['tac_gia']) ?> |
                        <?= date("F d, Y", strtotime($rec['ngay_dang'])) ?>
                    </p>
                </div>
                </div>
            <?php endforeach; ?>
        </section>
        </div>
    </main>

    <footer>
        <p>© 2025 Nhóm 6 - Website Tin tức Sức khỏe</p>
    </footer>
</body>

</html>
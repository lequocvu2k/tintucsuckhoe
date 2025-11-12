<?php

session_start();
require_once './db.php';

// ✅ CHỈ ADMIN HOẶC NHÂN VIÊN MỚI ĐƯỢC TRUY CẬP
if (!isset($_SESSION['username']) || ($_SESSION['username'] !== 'admin' && $_SESSION['user_role'] !== 'NhanVien')) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>🚫 Bạn không có quyền truy cập trang này!</h2>";
    exit;
}

// Kiểm tra người dùng và tính toán cấp độ
$user = null;
$tier = "Member";
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["username"]) && isset($_POST["email"])) {
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
$user_id = $_SESSION['user_id'] ?? null;
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

        $so_diem = isset($user['so_diem']) && is_numeric($user['so_diem']) ? $user['so_diem'] : 0;
        $diem = tinhDiem($so_diem);
        $tier = xacDinhCapDo($so_diem);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra thao tác thêm bài viết
    if (isset($_POST['add'])) {
        $tieu_de = $_POST['tieu_de'] ?? '';
        $duong_dan = $_POST['duong_dan'] ?? '';
        $noi_dung = $_POST['noi_dung'] ?? '';
        $ma_tac_gia = $_POST['ma_tac_gia'] ?: null;
        $ma_chuyen_muc = $_POST['ma_chuyen_muc'] ?: null;
        $trang_thai = $_POST['trang_thai'] ?? 'draft';
        $danh_muc = $_POST['danh_muc'] ?? null;

        // Kiểm tra dữ liệu bài viết
        if (empty($tieu_de) || empty($duong_dan) || empty($noi_dung)) {
            $_SESSION['error'] = "❌ Các trường Tiêu đề, Đường dẫn, và Nội dung là bắt buộc.";
            header("Location: quanlybv.php");
            exit;
        }

        // Xử lý ảnh
        $anh_bv = null;
        if (isset($_FILES['anh_bv']) && $_FILES['anh_bv']['error'] === 0) {
            $dir = "uploads/baiviet/";
            if (!is_dir($dir))
                mkdir($dir, 0777, true);
            $fileName = time() . "_" . basename($_FILES["anh_bv"]["name"]);
            $target = $dir . $fileName;
            move_uploaded_file($_FILES["anh_bv"]["tmp_name"], $target);
            $anh_bv = $target;
        }

        try {
            // Kiểm tra xem đường dẫn đã tồn tại chưa
            $check = $pdo->prepare("SELECT COUNT(*) FROM baiviet WHERE duong_dan = ?");
            $check->execute([$duong_dan]);
            if ($check->fetchColumn() > 0) {
                $_SESSION['error'] = "⚠️ Đường dẫn (slug) \"$duong_dan\" đã tồn tại! Vui lòng chọn slug khác.";
                header("Location: quanlybv.php");
                exit;
            }

            // Thêm bài viết vào cơ sở dữ liệu
            // Khi bạn thêm một bài viết mới
            $stmt = $pdo->prepare("
    INSERT INTO baiviet (tieu_de, duong_dan, noi_dung, anh_bv, ma_tac_gia, ma_chuyen_muc, ngay_dang, ngay_cap_nhat, trang_thai, luot_xem, danh_muc, id_kh)
    VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW(), ?, 0, ?, ?)
");

            $stmt->execute([$tieu_de, $duong_dan, $noi_dung, $anh_bv, $ma_tac_gia, $ma_chuyen_muc, $trang_thai, $danh_muc, $id_kh]);



            $_SESSION['success'] = "✅ Thêm bài viết thành công!";
            header("Location: quanlybv.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "❌ Lỗi khi thêm bài viết: " . $e->getMessage();
            header("Location: quanlybv.php");
            exit;
        }
    }

    // Cập nhật bài viết
    if (isset($_POST['update'])) {
        $id = $_POST['ma_bai_viet'];
        $tieu_de = $_POST['tieu_de'];
        $duong_dan = $_POST['duong_dan'];
        $noi_dung = $_POST['noi_dung'];
        $ma_tac_gia = $_POST['ma_tac_gia'];
        $ma_chuyen_muc = $_POST['ma_chuyen_muc'];
        $trang_thai = $_POST['trang_thai'];
        $danh_muc = $_POST['danh_muc'];

        // Xử lý ảnh mới (nếu có)
        $anh_bv = $_POST['anh_cu'] ?? null;
        if (isset($_FILES['anh_bv']) && $_FILES['anh_bv']['error'] === 0) {
            $dir = "uploads/baiviet/";
            if (!is_dir($dir))
                mkdir($dir, 0777, true);
            $fileName = time() . "_" . basename($_FILES["anh_bv"]["name"]);
            $target = $dir . $fileName;
            move_uploaded_file($_FILES["anh_bv"]["tmp_name"], $target);
            $anh_bv = $target;
        }

        try {
            // Kiểm tra đường dẫn có trùng không
            $check = $pdo->prepare("SELECT COUNT(*) FROM baiviet WHERE duong_dan = ? AND ma_bai_viet != ?");
            $check->execute([$duong_dan, $id]);
            if ($check->fetchColumn() > 0) {
                $_SESSION['error'] = "⚠️ Đường dẫn đã tồn tại, vui lòng nhập khác!";
                header("Location: quanlybv.php");
                exit;
            }

            // Cập nhật bài viết
            $stmt = $pdo->prepare("UPDATE baiviet SET 
            tieu_de=?, duong_dan=?, noi_dung=?, anh_bv=?, ma_tac_gia=?, ma_chuyen_muc=?, trang_thai=?, danh_muc=?, ngay_cap_nhat=NOW() 
            WHERE ma_bai_viet=?");
            $stmt->execute([$tieu_de, $duong_dan, $noi_dung, $anh_bv, $ma_tac_gia, $ma_chuyen_muc, $trang_thai, $danh_muc, $id]);

            $_SESSION['success'] = "✏️ Cập nhật thành công!";
            header("Location: quanlybv.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "❌ Lỗi khi cập nhật bài viết: " . $e->getMessage();
            header("Location: quanlybv.php");
            exit;
        }
    }

    // Xóa bài viết
    if (isset($_POST['delete'])) {
        $id = $_POST['ma_bai_viet'];
        $stmt = $pdo->prepare("DELETE FROM baiviet WHERE ma_bai_viet=?");
        $stmt->execute([$id]);
        $_SESSION['success'] = "🗑️ Đã xóa bài viết!";
        header("Location: quanlybv.php");
        exit;
    }

    // Xóa tất cả bài viết
    if (isset($_POST['delete_all'])) {
        $stmt = $pdo->exec("DELETE FROM baiviet");
        $_SESSION['success'] = "⚠️ Đã xóa toàn bộ bài viết!";
        header("Location: quanlybv.php");
        exit;
    }
}

// Lấy bài viết cho phần sửa
$editPost = null;
if (isset($_POST['edit'])) {
    $id = $_POST['ma_bai_viet'];
    $stmt = $pdo->prepare("SELECT * FROM baiviet WHERE ma_bai_viet = ?");
    $stmt->execute([$id]);
    $editPost = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lấy danh sách bài viết
// Lấy danh sách chuyên mục để hiển thị dropdown lọc
$chuyenmucs = $pdo->query("SELECT ma_chuyen_muc, ten_chuyen_muc FROM chuyenmuc ORDER BY ten_chuyen_muc ASC")->fetchAll(PDO::FETCH_ASSOC);

// Xử lý lọc bài viết theo chuyên mục (nếu có)
$filter = $_GET['chuyenmuc'] ?? '';
$sql = "
    SELECT b.*, c.ten_chuyen_muc 
    FROM baiviet b
    LEFT JOIN chuyenmuc c ON b.ma_chuyen_muc = c.ma_chuyen_muc
";

if (!empty($filter)) {
    $stmt = $pdo->prepare($sql . " WHERE b.ma_chuyen_muc = ? ORDER BY b.ngay_dang DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->prepare($sql . " ORDER BY b.ngay_dang DESC");
    $stmt->execute();
}

$baiviet = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Bài Viết</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/quanlybv.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/popup.css">
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

    <?php if (isset($_SESSION['success'])): ?>
        <div class="message-success"><?= htmlspecialchars($_SESSION['success']); ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="message-error"><?= htmlspecialchars($_SESSION['error']); ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <h2 class="page-title">📰 Quản lý Bài Viết</h2>
    <!-- FORM THÊM / SỬA -->
    <div class="card">
        <form method="POST" enctype="multipart/form-data">
            <?php if ($editPost): ?>
                <input type="hidden" name="ma_bai_viet" value="<?= htmlspecialchars($editPost['ma_bai_viet']) ?>">
                <h3>✏️ Sửa bài viết #<?= htmlspecialchars($editPost['ma_bai_viet']) ?></h3>
            <?php else: ?>
                <h3>🆕 Thêm bài viết mới</h3>
            <?php endif; ?>

            <div class="form-grid">
                <!-- Tiêu đề -->
                <div class="form-group">
                    <label>Tiêu đề</label>
                    <input type="text" name="tieu_de" value="<?= htmlspecialchars($editPost['tieu_de'] ?? '') ?>"
                        required>
                </div>

                <!-- Đường dẫn (slug) -->
                <div class="form-group">
                    <label>Đường dẫn (slug)</label>
                    <input type="text" name="duong_dan" value="<?= htmlspecialchars($editPost['duong_dan'] ?? '') ?>"
                        required>
                </div>

                <!-- Ảnh bài viết -->
                <div class="form-group">
                    <label>Ảnh bài viết</label>
                    <?php if (!empty($editPost['anh_bv'])): ?>
                        <img src="<?= htmlspecialchars($editPost['anh_bv']) ?>" class="thumb"><br>
                        <input type="hidden" name="anh_cu" value="<?= htmlspecialchars($editPost['anh_bv']) ?>">
                    <?php endif; ?>
                    <input type="file" name="anh_bv" accept="image/*">
                </div>

                <!-- Mã tác giả -->
                <div class="form-group">
                    <label>Mã tác giả</label>
                    <input type="number" name="ma_tac_gia"
                        value="<?= htmlspecialchars($editPost['ma_tac_gia'] ?? '') ?>">
                </div>

                <!-- Mã chuyên mục -->
                <div class="form-group">
                    <label>Chuyên mục</label>
                    <select name="ma_chuyen_muc" required>
                        <option value="">-- Chọn chuyên mục --</option>
                        <?php
                        try {
                            $chuyenmucStmt = $pdo->query("SELECT ma_chuyen_muc, ten_chuyen_muc FROM chuyenmuc ORDER BY ma_chuyen_muc ASC");
                            $chuyenmucs = $chuyenmucStmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($chuyenmucs as $cm) {
                                $selected = (isset($editPost['ma_chuyen_muc']) && $editPost['ma_chuyen_muc'] == $cm['ma_chuyen_muc']) ? 'selected' : '';
                                echo "<option value='{$cm['ma_chuyen_muc']}' {$selected}>{$cm['ten_chuyen_muc']}</option>";
                            }
                        } catch (PDOException $e) {
                            echo "<option disabled>Lỗi tải chuyên mục</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Danh mục -->
                <div class="form-group">
                    <label>Danh mục</label>
                    <select name="danh_muc" required>
                        <option value="">-- Chọn danh mục --</option>
                        <option value="LATEST POSTS" <?= (isset($editPost['danh_muc']) && $editPost['danh_muc'] == 'LATEST POSTS') ? 'selected' : '' ?>>LATEST POSTS</option>
                        <option value="POPULAR POSTS" <?= (isset($editPost['danh_muc']) && $editPost['danh_muc'] == 'POPULAR POSTS') ? 'selected' : '' ?>>POPULAR POSTS</option>
                        <option value="RANKINGS" <?= (isset($editPost['danh_muc']) && $editPost['danh_muc'] == 'RANKINGS') ? 'selected' : '' ?>>RANKINGS</option>
                        <option value="EDITOR'S PICKS" <?= (isset($editPost['danh_muc']) && $editPost['danh_muc'] == "EDITOR'S PICKS") ? 'selected' : '' ?>>EDITOR'S PICKS</option>
                        <option value="INTERVIEWS" <?= (isset($editPost['danh_muc']) && $editPost['danh_muc'] == 'INTERVIEWS') ? 'selected' : '' ?>>INTERVIEWS</option>
                        <option value="RECOMMENDATIONS" <?= (isset($editPost['danh_muc']) && $editPost['danh_muc'] == 'RECOMMENDATIONS') ? 'selected' : '' ?>>RECOMMENDATIONS</option>
                        <option value="MAIN HIGHLIGHTS" <?= (isset($editPost['danh_muc']) && $editPost['danh_muc'] == 'MAIN HIGHLIGHTS') ? 'selected' : '' ?>>MAIN HIGHLIGHTS</option>
                    </select>
                </div>

                <!-- Trạng thái -->
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="trang_thai">
                        <option value="draft" <?= (isset($editPost['trang_thai']) && $editPost['trang_thai'] == 'draft') ? 'selected' : '' ?>>📝 Nháp</option>
                        <option value="published" <?= (isset($editPost['trang_thai']) && $editPost['trang_thai'] == 'published') ? 'selected' : '' ?>>✅ Công khai</option>
                        <option value="hidden" <?= (isset($editPost['trang_thai']) && $editPost['trang_thai'] == 'hidden') ? 'selected' : '' ?>>🚫 Ẩn</option>
                    </select>
                </div>
            </div>

            <!-- TinyMCE cho phần nội dung -->
            <div class="form-group">
                <label>Nội dung</label>
                <textarea name="noi_dung" class="tinymce" rows="5" required>
        <?= htmlspecialchars($editPost['noi_dung'] ?? '') ?>
    </textarea>
            </div>


            <div class="form-actions">
                <?php if ($editPost): ?>
                    <button type="submit" name="update" class="btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
                    <a href="quanlybv.php" class="btn-cancel">❌ Hủy</a>
                <?php else: ?>
                    <button type="submit" name="add" class="btn-primary">Đăng bài</button>

                <?php endif; ?>
            </div>
        </form>
    </div>
    <!-- Bộ lọc chuyên mục -->
    <form method="GET" style="margin-bottom: 20px; text-align:right;">
        <label for="chuyenmuc" style="font-weight:bold; margin-right:10px;">📂 Lọc theo chuyên mục:</label>
        <select name="chuyenmuc" id="chuyenmuc" onchange="this.form.submit()" style="padding:5px 10px;">
            <option value="">-- Tất cả --</option>
            <?php foreach ($chuyenmucs as $cm): ?>
                <option value="<?= $cm['ma_chuyen_muc'] ?>" <?= (isset($_GET['chuyenmuc']) && $_GET['chuyenmuc'] == $cm['ma_chuyen_muc']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    <table>
        <thead>
            <tr>
                <th>Mã bài viết</th>
                <th>Ảnh</th>
                <th>Tiêu đề</th>
                <th>Đường dẫn</th>
                <th>Danh mục</th> <!-- 🆕 thêm cột Danh mục -->
                <th>Chuyên mục</th>
                <th>Tác giả</th>
                <th>Trạng thái</th>
                <th>Ngày đăng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($baiviet as $bv): ?>
                <tr>
                    <td><?= $bv['ma_bai_viet'] ?></td>
                    <td><img src="<?= htmlspecialchars($bv['anh_bv']) ?>" class="thumb" alt="Ảnh bài viết"></td>
                    <td><?= htmlspecialchars($bv['tieu_de']) ?></td>
                    <td><?= htmlspecialchars($bv['duong_dan']) ?></td>
                    <td><span class="category"><?= htmlspecialchars($bv['danh_muc']) ?></span></td> <!-- 🆕 -->
                    <td><?= htmlspecialchars($bv['ten_chuyen_muc'] ?? 'Không rõ') ?></td>

                    <td><?= htmlspecialchars($bv['ma_tac_gia']) ?></td>
                    <td><span class="status <?= $bv['trang_thai'] ?>"><?= ucfirst($bv['trang_thai']) ?></span></td>
                    <td><?= $bv['ngay_dang'] ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="ma_bai_viet" value="<?= $bv['ma_bai_viet'] ?>">
                            <button type="submit" name="edit" class="btn-edit"><i class="fas fa-edit"></i></button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="ma_bai_viet" value="<?= $bv['ma_bai_viet'] ?>">
                            <button type="submit" name="delete" class="btn-danger"
                                onclick="return confirm('Xóa bài này?')"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.1/tinymce.min.js"></script>
    <script>
        tinymce.init({
            selector: 'textarea[name="noi_dung"]',  // Chọn textarea cần thay thế
            height: 300,
            plugins: 'advlist autolink lists link image charmap print preview anchor',
            toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | code | image link',
            content_style: "body { font-family:Arial, sans-serif; font-size:14px }",
            images_upload_url: 'upload_image.php', // URL của script xử lý ảnh
            automatic_uploads: true,  // Tự động tải ảnh lên khi người dùng chèn ảnh

            setup: function (editor) {
                // Đảm bảo rằng TinyMCE sẽ cập nhật nội dung vào textarea khi thay đổi
                editor.on('change', function () {
                    tinymce.triggerSave();  // Đồng bộ hóa nội dung vào textarea
                });
            }
        });
    </script>
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
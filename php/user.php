<?php
session_start();
require_once './db.php';

// ====================== KIỂM TRA ĐĂNG NHẬP ======================
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
$id_kh = $_SESSION['user_id'];
$view = $_GET['view'] ?? 'info'; // Nếu không có tham số view, mặc định sẽ hiển thị 'info'

// Xử lý hủy yêu cầu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_request'])) {
    // Lấy ID yêu cầu từ form
    $cancel_request_id = $_POST['cancel_request_id'] ?? '';

    if ($cancel_request_id) {
        // Xóa yêu cầu khỏi bảng nhanvien_yc
        $stmt = $pdo->prepare("DELETE FROM nhanvien_yc WHERE id = :id");
        $stmt->bindParam(':id', $cancel_request_id);
        $stmt->execute();

        // Thông báo thành công và reload lại trang
        echo "<script>alert('Yêu cầu đã bị hủy thành công!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('Có lỗi khi xóa yêu cầu!'); window.location.reload();</script>";
    }
}

// Xử lý xóa tài khoản khi người dùng nhấn nút
// Xử lý xóa tài khoản khi người dùng nhấn nút
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {

    // 1️⃣ Xóa trong bảng taotaikhoan
    $stmt = $pdo->prepare("DELETE FROM taotaikhoan WHERE id_kh = :id");
    $stmt->bindParam(':id', $id_kh);
    $stmt->execute();

    // 2️⃣ Xóa trong bảng dangnhap (dùng username)
    $stmt = $pdo->prepare("
        DELETE FROM dangnhap 
        WHERE username = (
            SELECT username FROM taotaikhoan WHERE id_kh = :id LIMIT 1
        )
    ");
    $stmt->bindParam(':id', $id_kh);
    $stmt->execute();

    // 3️⃣ Xóa trong bảng khachhang
    $stmt = $pdo->prepare("DELETE FROM khachhang WHERE id_kh = :id");
    $stmt->bindParam(':id', $id_kh);
    $stmt->execute();

    // Đăng xuất người dùng và chuyển về trang chủ
    session_destroy();
    header('Location: index.php');
    exit;
}

// ====================== LẤY THÔNG TIN NGƯỜI DÙNG ======================
$stmt = $pdo->prepare("
    SELECT kh.*, tk.ngay_tao
    FROM khachhang kh
    LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
    WHERE kh.id_kh = :id
");
$stmt->bindParam(':id', $id_kh);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Không tìm thấy người dùng!");
}
$isMale = ($user['gioi_tinh'] ?? '') === 'Nam' ? 'checked' : '';
$isFemale = ($user['gioi_tinh'] ?? '') === 'Nữ' ? 'checked' : '';
// ====================== HÀM TÍNH ĐIỂM VÀ CẤP ĐỘ ======================
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doixp'])) {
    $addXP = (int) $_POST['add_xp'];
    $id_kh = $user['id_kh']; // lấy id người dùng hiện tại

    // 🔹 Lấy tổng điểm đọc bài hiện có
    $stmt_diem = $pdo->prepare("
        SELECT COALESCE(SUM(diem_cong), 0) AS tong_diem_doc
        FROM diemdoc
        WHERE id_kh = ?
          AND loai_giao_dich IN ('xem_bai', 'doi_xp')
    ");
    $stmt_diem->execute([$id_kh]);
    $tong_diem_doc = (int) $stmt_diem->fetchColumn();

    // 🔸 Kiểm tra hợp lệ
    if ($addXP > 0 && $addXP <= $tong_diem_doc) {

        // 1️⃣ Ghi lại giao dịch đổi XP (trừ điểm đọc bài)
        $stmt_insert = $pdo->prepare("
            INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
            VALUES (:id_kh, NULL, :diem_cong, 'doi_xp', NOW())
        ");
        $stmt_insert->execute([
            ':id_kh' => $id_kh,
            ':diem_cong' => -$addXP // Trừ điểm đọc bài
        ]);

        // 2️⃣ Cập nhật bảng khachhang: trừ so_diem và cộng xp
        $stmt_update = $pdo->prepare("
            UPDATE khachhang 
            SET xp = xp + :xp, 
                so_diem = GREATEST(so_diem - :xp, 0)  -- tránh âm điểm
            WHERE id_kh = :id_kh
        ");
        $stmt_update->execute([
            ':xp' => $addXP,
            ':id_kh' => $id_kh
        ]);

        // 3️⃣ Thông báo và reload
        $_SESSION['success'] = "🎉 Đã đổi {$addXP} điểm sang XP thành công!";
        header("Location: user.php");
        exit;
    } elseif ($addXP > $tong_diem_doc) {
        $_SESSION['error'] = "⚠️ Bạn không đủ điểm để đổi!";
        header("Location: user.php");
        exit;
    } else {
        $_SESSION['error'] = "❌ Vui lòng nhập số XP hợp lệ!";
        header("Location: user.php");
        exit;
    }
}


// ====================== XỬ LÝ POST ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Upload avatar ---
    if (isset($_POST['upload_avatar']) && isset($_FILES['avatar'])) {
        $file = $_FILES['avatar'];
        if ($file['error'] === 0) {
            $targetDir = "../uploads/avatars/";
            if (!is_dir($targetDir))
                mkdir($targetDir, 0777, true);

            $fileName = time() . "_" . basename($file["name"]);
            $targetFile = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = "❌ Chỉ cho phép ảnh JPG, PNG hoặc GIF.";
            } elseif (move_uploaded_file($file["tmp_name"], $targetFile)) {
                $stmt = $pdo->prepare("UPDATE khachhang SET avatar_url=? WHERE id_kh=?");
                $stmt->execute([$targetFile, $user['id_kh']]);
                $user['avatar_url'] = $targetFile;
                $_SESSION['success'] = "✅ Cập nhật ảnh đại diện thành công!";
            } else {
                $_SESSION['error'] = "⚠️ Lỗi khi tải ảnh lên.";
            }
        } else {
            $_SESSION['error'] = "⚠️ Chưa chọn ảnh hợp lệ.";
        }
        header("Location: user.php");
        exit;
    }

    // --- Chọn khung avatar ---
    if (isset($_POST['save_frame']) && isset($_POST['avatar_frame'])) {
        $avatar_frame = $_POST['avatar_frame'];
        $stmt = $pdo->prepare("UPDATE khachhang SET avatar_frame=? WHERE id_kh=?");
        $stmt->execute([$avatar_frame, $user['id_kh']]);
        $user['avatar_frame'] = $avatar_frame;
        $_SESSION['success'] = "✅ Cập nhật khung thành công!";
        header("Location: user.php");
        exit;
    }

    // --- Cập nhật thông tin cá nhân ---
    if (isset($_POST['update_info'])) {
        $ho_ten = $_POST['ho_ten'] ?? '';
        $sdt = $_POST['sdt'] ?? '';
        $email = $_POST['email'] ?? '';
        $ngay_sinh = $_POST['ngay_sinh'] ?? '';
        $gioi_tinh = $_POST['gioi_tinh'] ?? '';
        $dia_chi = $_POST['dia_chi'] ?? '';
        $tinh_thanh = $_POST['tinh_thanh'] ?? '';
        $quoc_gia = $_POST['quoc_gia'] ?? '';

        $update = $pdo->prepare("
            UPDATE khachhang 
            SET ho_ten = :ho_ten,
                sdt = :sdt,
                email = :email,
                ngay_sinh = :ngay_sinh,
                gioi_tinh = :gioi_tinh,
                dia_chi = :dia_chi,
                tinh_thanh = :tinh_thanh,
                quoc_gia = :quoc_gia
            WHERE id_kh = :id
        ");
        $update->execute([
            ':ho_ten' => $ho_ten,
            ':sdt' => $sdt,
            ':email' => $email,
            ':ngay_sinh' => $ngay_sinh,
            ':gioi_tinh' => $gioi_tinh,
            ':dia_chi' => $dia_chi,
            ':tinh_thanh' => $tinh_thanh,
            ':quoc_gia' => $quoc_gia,
            ':id' => $id_kh
        ]);

        $_SESSION['success'] = "✅ Cập nhật thông tin cá nhân thành công!";
        header("Location: user.php");
        exit;
    }

    // --- Đổi mật khẩu ---
    if (isset($_POST['update_pass'])) {
        $matkhau_cu = $_POST['matkhau_cu'] ?? '';
        $matkhau_moi = $_POST['matkhau_moi'] ?? '';

        // 1️⃣ Lấy mật khẩu hiện tại từ bảng taotaikhoan
        $stmt = $pdo->prepare("SELECT username, password FROM taotaikhoan WHERE id_kh = :id LIMIT 1");
        $stmt->execute([':id' => $id_kh]);
        $account = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$account) {
            $_SESSION['error'] = "❌ Không tìm thấy tài khoản!";
            header("Location: user.php?view=settings");
            exit;
        }

        $username = $account['username'];
        $password_hash = $account['password'];

        // 2️⃣ Mật khẩu cũ KHÔNG phải hash → so sánh trực tiếp
        if ($matkhau_cu !== $password_hash) {
            $_SESSION['error'] = "❌ Mật khẩu hiện tại không đúng!";
            header("Location: user.php?view=settings");
            exit;
        }

        // 3️⃣ Hash mật khẩu mới
        $newHash = $matkhau_moi; // nếu bạn chưa dùng hash
        // Nếu bạn muốn hash thực sự thì dùng:
        // $newHash = password_hash($matkhau_moi, PASSWORD_DEFAULT);

        // 4️⃣ Cập nhật taotaikhoan
        $stmt = $pdo->prepare("
        UPDATE taotaikhoan
        SET password = :pass, confirm_password = :pass
        WHERE id_kh = :id
    ");
        $stmt->execute([
            ':pass' => $newHash,
            ':id' => $id_kh
        ]);

        // 5️⃣ Cập nhật dangnhap theo username
        $stmt = $pdo->prepare("UPDATE dangnhap SET password = :pass WHERE username = :username");
        $stmt->execute([
            ':pass' => $newHash,
            ':username' => $username
        ]);

        $_SESSION['success'] = "✅ Đổi mật khẩu thành công!";
        header("Location: user.php?view=settings");
        exit;
    }

}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông tin cá nhân</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/user.css">
    <link rel="stylesheet" href="../css/menu.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/user.js" defer></script>
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

    <div class="notification">
        <?php
        if (isset($_SESSION['success'])) {
            echo '<p class="success-msg">' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<p class="error-msg">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
    </div>

    <div class="profile-container">
        <!-- KHUNG TRÁI -->
        <div class="profile-left">
            <div class="user-info">
                <div class="avatar-wrapper">
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
                    echo '<div class="avatar-container">';
                    echo '<img src="' . $avatar . '" alt="Avatar" class="avatar">';
                    if ($frame) {
                        echo '<img src="' . $frame . '" alt="Frame" class="frame-overlay">';
                    }
                    echo '</div>';
                    ?>

                    <!-- Nút nhỏ đổi avatar -->
                    <form method="post" enctype="multipart/form-data" class="avatar-form">
                        <input type="hidden" name="upload_avatar" value="1">
                        <label for="avatarInput" class="camera-btn">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="avatar" id="avatarInput" accept="image/*"
                            onchange="this.form.submit()">
                    </form>
                </div>

                <div class="user-name <?php
                // Tính cấp độ và gán lớp màu sắc
                $level = floor($xp / 100); // Mỗi 100 XP = 1 cấp
                
                // Xác định màu sắc dựa trên cấp độ
                if ($level >= 40) {
                    echo 'level-40';
                } elseif ($level >= 30) {
                    echo 'level-30';
                } elseif ($level >= 20) {
                    echo 'level-20';
                } elseif ($level >= 10) {
                    echo 'level-1';
                } else {
                    echo 'level-1'; // Màu cho các cấp thấp hơn
                }
                ?>">
                    <?= htmlspecialchars($user['ho_ten']) ?>
                </div>

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
                <div class="level-bar">
                    <?php
                    // --- Lấy XP hiện tại từ bảng khachhang ---
                    $xp = isset($user['xp']) && is_numeric($user['xp']) ? (int) $user['xp'] : 0;

                    // --- Tính cấp độ và tiến trình ---
                    $level = floor($xp / 100); // Mỗi 100 XP = 1 cấp
                    $nextLevelXP = ($level + 1) * 100;
                    $percent = min(100, ($xp / $nextLevelXP) * 100);
                    ?>
                    <p>Level <?= $level ?> - XP: <?= number_format($xp) ?> / <?= number_format($nextLevelXP) ?></p>
                    <div class="progress">
                        <div class="progress-fill" style="width: <?= $percent ?>%;"></div>
                    </div>
                </div>

                <?php
                // 🔹 Tính tổng tất cả điểm cộng / trừ thực tế từ bảng diemdoc
                $stmt_diem = $pdo->prepare("
    SELECT COALESCE(SUM(diem_cong), 0) AS tong_diem
    FROM diemdoc
    WHERE id_kh = ?
");
                $stmt_diem->execute([$user['id_kh']]);
                $tong_diem_con_lai = (int) $stmt_diem->fetchColumn();
                ?>

                <i class="fas fa-gem"></i>
                <span>Tổng điểm còn lại:</span>
                <b><?= number_format($tong_diem_con_lai) ?></b>

                <p><b>Ngày tạo:</b> <?= htmlspecialchars($user['ngay_tao']) ?></p>

                <!-- Nút mở popup đổi XP -->
                <button type="button" class="xp-btn" onclick="openXPModal()">Đổi XP</button>

                <!-- Popup -->
                <div id="xpModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeXPModal()">&times;</span>
                        <h3>Đổi điểm sang XP</h3>

                        <?php
                        // Lấy tổng điểm đọc bài thực tế
                        // Kiểm tra và lấy tổng điểm đọc bài từ bảng diemdoc
                        $stmt_diem = $pdo->prepare("
    SELECT COALESCE(SUM(diem_cong), 0) AS tong_diem_doc
    FROM diemdoc
    WHERE id_kh = ? AND loai_giao_dich = 'xem_bai'
");
                        $stmt_diem->execute([$user['id_kh']]);
                        $diem_result = $stmt_diem->fetch(PDO::FETCH_ASSOC);
                        $tong_diem_doc = (int) $diem_result['tong_diem_doc'];

                        ?>

                        <p class="current-points">
                            Bạn hiện có: <b><?= number_format($tong_diem_doc) ?></b> điểm đọc bài
                        </p>

                        <form method="POST">
                            <label for="add_xp">Nhập số XP muốn đổi:</label>
                            <input type="number" id="add_xp" name="add_xp" min="1" max="<?= $tong_diem_doc ?>" required>

                            <p class="note">💡 1 điểm đọc bài = 1 XP</p>
                            <button type="submit" name="doixp" class="confirm-btn">Xác nhận đổi</button>
                        </form>
                    </div>
                </div>

                <button class="logout-btn" onclick="window.location.href='logout.php'">Đăng xuất</button>

            </div>
            <div class="frame-selection">
                <br><br><br>
                <h2>Chọn khung avatar của bạn</h2>
                <form method="post" action="">
                    <div class="frame-list">
                        <label>
                            <input type="radio" name="avatar_frame" value="game" <?= ($user['avatar_frame'] == 'game') ? 'checked' : '' ?>>
                            <img src="../frames/game.png" alt="Fire Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="fire" <?= ($user['avatar_frame'] == 'fire') ? 'checked' : '' ?>>
                            <img src="../frames/fire.png" alt="Fire Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="gc" <?= ($user['avatar_frame'] == 'gc') ? 'checked' : '' ?>>
                            <img src="../frames/gc.gif" alt="Fire Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="ice" <?= ($user['avatar_frame'] == 'ice') ? 'checked' : '' ?>>
                            <img src="../frames/ice.png" alt="Ice Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="nahida" <?= ($user['avatar_frame'] == 'nahida') ? 'checked' : '' ?>>
                            <img src="../frames/nahida.png" alt="Gold Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="raiden" <?= ($user['avatar_frame'] == 'raiden') ? 'checked' : '' ?>>
                            <img src="../frames/raiden.png" alt="Gold Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="chiu" <?= ($user['avatar_frame'] == 'chiu') ? 'checked' : '' ?>>
                            <img src="../frames/chiu.gif" alt="Ice Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="firefly"
                                <?= ($user['avatar_frame'] == 'firefly') ? 'checked' : '' ?>>
                            <img src="../frames/firefly.png" alt="Gold Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="genhsin"
                                <?= ($user['avatar_frame'] == 'genhsin') ? 'checked' : '' ?>>
                            <img src="../frames/genhsin.gif" alt="Gold Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="peak" <?= ($user['avatar_frame'] == 'peak') ? 'checked' : '' ?>>
                            <img src="../frames/peak.gif" alt="Gold Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="gi" <?= ($user['avatar_frame'] == 'gi') ? 'checked' : '' ?>>
                            <img src="../frames/gi.gif" alt="Gold Frame">
                        </label>
                        <label>
                            <input type="radio" name="avatar_frame" value="evernight"
                                <?= ($user['avatar_frame'] == 'evernight') ? 'checked' : '' ?>>
                            <img src="../frames/evernight.png" alt="Gold Frame">
                        </label>
                    </div>
                    <button type="submit" name="save_frame">Lưu khung</button>
                </form>
            </div>
            <br>
            <div class="health-box">
                <h3 class="health-title">⚡ Yêu cầu</h3>

                <button class="btn-health upgrade">
                    🌿 Nâng cấp hạng
                </button>

                <button class="btn-health club">
                    🧘 Tham gia Câu lạc bộ Sức khỏe
                </button>
                <!-- Nút để mở popup "Trở thành nhân viên" -->
                <button class="btn-health share" onclick="openEmployeeModal()">Trở thành nhân viên</button>
                <!-- Modal "Trở thành nhân viên" -->
                <div id="employeeModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeEmployeeModal()">&times;</span>
                        <h3>Đăng ký trở thành nhân viên - Gia nhập đội ngũ chúng tôi!</h3>

                        <p>Chúng tôi đang tìm kiếm những cá nhân năng động, đam mê và sẵn sàng tham gia vào đội ngũ của
                            mình. Hãy điền thông tin dưới đây để chúng tôi có thể liên hệ với bạn ngay!</p>

                        <!-- Form yêu cầu trở thành nhân viên -->
                        <form method="POST" action="xac_nhan.php">
                            <label for="ho_ten">Họ và tên:</label>
                            <input type="text" id="ho_ten" name="ho_ten" placeholder="Nhập họ tên của bạn" required>

                            <label for="sdt">Số điện thoại:</label>
                            <input type="text" id="sdt" name="sdt" placeholder="Số điện thoại liên hệ" required>

                            <label for="the_loai">Bạn muốn đăng ký thể loại công việc nào?</label>
                            <input type="text" id="the_loai" name="the_loai"
                                placeholder="Chọn thể loại công việc bạn muốn tham gia" required>

                            <p class="note">💡 Hãy cho chúng tôi biết công việc mà bạn đang tìm kiếm, và chúng tôi sẽ
                                xem xét yêu cầu của bạn nhanh nhất có thể.</p>

                            <button type="submit" name="submit_employee_request" class="confirm-btn">Gửi yêu
                                cầu</button>
                            <button type="button" class="cancel-btn" onclick="closeEmployeeModal()">Hủy bỏ</button>
                        </form>
                    </div>
                </div>
            </div>
            <br>
            <div class="history-box">
                <h3 class="history-title">🔁 Lịch sử yêu cầu</h3>
                <button class="hide-btn" onclick="toggleHistory()">Ẩn bớt</button>

                <div class="history-section">
                    <h4 class="history-subtitle">🌿 Yêu cầu nâng cấp hạng</h4>
                    <p>Hiện tại chưa có yêu cầu nào</p>
                </div>

                <div class="history-section">
                    <h4 class="history-subtitle">🧘 Yêu cầu tham gia Câu lạc bộ Sức khỏe</h4>
                    <p>Hiện tại chưa có yêu cầu nào</p>
                </div>
                <div class="history-section">
                    <h4 class="history-subtitle">💬 Yêu cầu trở thành nhân viên</h4>
                    <?php
                    // Lấy yêu cầu "Trở thành nhân viên"
                    if ($id_kh) {
                        $stmt = $pdo->prepare("SELECT ho_ten, sdt, the_loai, ngay_tao, trang_thai, id FROM nhanvien_yc WHERE id_kh = :id_kh ORDER BY ngay_tao DESC");
                        $stmt->bindParam(':id_kh', $id_kh);
                        $stmt->execute();
                        $yeu_cau = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // Nếu có yêu cầu, hiển thị thông tin
                        if ($yeu_cau) {
                            foreach ($yeu_cau as $cau) {
                                // Chọn lớp CSS dựa trên trạng thái
                                $statusClass = '';
                                if ($cau['trang_thai'] == 'chờ duyệt') {
                                    $statusClass = 'status-pending';
                                } elseif ($cau['trang_thai'] == 'đã duyệt') {
                                    $statusClass = 'status-approved';
                                } elseif ($cau['trang_thai'] == 'bị từ chối') {
                                    $statusClass = 'status-rejected';
                                }

                                echo '<div class="history-item">';
                                echo '<h4 class="history-subtitle">📝 Yêu cầu: ' . htmlspecialchars($cau['the_loai']) . '</h4>';
                                echo '<p><b>Họ tên:</b> ' . htmlspecialchars($cau['ho_ten']) . '</p>';
                                echo '<p><b>Số điện thoại:</b> ' . htmlspecialchars($cau['sdt']) . '</p>';
                                echo '<p><b>Ngày gửi yêu cầu:</b> ' . htmlspecialchars($cau['ngay_tao']) . '</p>';
                                echo '<p><b>Trạng thái:</b> <span class="' . $statusClass . '">' . htmlspecialchars($cau['trang_thai']) . '</span></p>';

                                // Kiểm tra trạng thái và hiển thị nút phù hợp
                                if ($cau['trang_thai'] == 'đã duyệt') {
                                    // Hiển thị nút "Xóa yêu cầu" khi trạng thái là "đã duyệt"
                                    echo '<form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_request_id" value="' . $cau['id'] . '">
                            <button type="submit" name="delete_request" class="delete-btn">Xóa yêu cầu</button>
                          </form>';
                                } else {
                                    // Hiển thị nút "Hủy yêu cầu" khi trạng thái không phải là "đã duyệt"
                                    echo '<form method="POST" style="display:inline;">
                            <input type="hidden" name="cancel_request_id" value="' . $cau['id'] . '">
                            <button type="submit" name="cancel_request" class="cancel-btn">Hủy yêu cầu</button>
                          </form>';
                                }
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Hiện tại chưa có yêu cầu nào</p>';
                        }
                    } else {
                        echo '<p>Vui lòng đăng nhập để xem lịch sử yêu cầu.</p>';
                    }
                    ?>
                </div>

            </div>
        </div>
        <div class="profile-content">
            <!-- HÀNG TIÊU ĐỀ + TAB -->
            <div class="profile-header">
                <div class="profile-tabs">
                    <button class="tab-btn <?= ($view === 'info') ? 'active' : '' ?>" data-tab="info">
                        <i class="fas fa-user"></i> Thông tin
                    </button>
                    <button class="tab-btn <?= ($view === 'history') ? 'active' : '' ?>" data-tab="history">
                        <i class="fas fa-history"></i> Lịch sử
                    </button>
                    <button class="tab-btn <?= ($view === 'saved') ? 'active' : '' ?>" data-tab="saved">
                        <i class="fas fa-bookmark"></i> Đã lưu
                    </button>
                    <button class="tab-btn <?= ($view === 'notifications') ? 'active' : '' ?>" data-tab="notifications">
                        <i class="fas fa-bell"></i> Thông báo
                    </button>
                    <button class="tab-btn <?= ($view === 'settings') ? 'active' : '' ?>" data-tab="settings">
                        <i class="fas fa-cog"></i> Cài đặt
                    </button>
                </div>

            </div>
            <!-- TAB KHÁC -->
            <?php if ($view === 'info'): ?>
                <div class="tab-content <?= ($view === 'info') ? 'active' : '' ?>" id="info">
                    <form method="POST" class="info-form">
                        <h2 class="profile-title">Thông tin cá nhân</h2>
                        <div class="form-columns">
                            <div class="form-left">
                                <label>Họ tên:</label>
                                <input type="text" name="ho_ten" value="<?= htmlspecialchars($user['ho_ten']) ?>" required>

                                <label>Số điện thoại:</label>
                                <input type="text" name="sdt" value="<?= htmlspecialchars($user['sdt']) ?>">

                                <label>Email:</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

                                <label>Ngày sinh:</label>
                                <input type="date" name="ngay_sinh" value="<?= htmlspecialchars($user['ngay_sinh']) ?>">
                            </div>

                            <div class="form-right">
                                <label>Địa chỉ:</label>
                                <input type="text" name="dia_chi" value="<?= htmlspecialchars($user['dia_chi']) ?>">

                                <label>Thành phố / Tỉnh:</label>
                                <input type="text" name="tinh_thanh" value="<?= htmlspecialchars($user['tinh_thanh']) ?>">

                                <label>Quốc gia:</label>
                                <input type="text" name="quoc_gia" value="<?= htmlspecialchars($user['quoc_gia']) ?>">

                                <label>Giới tính:</label>
                                <div class="radio-group">
                                    <label>
                                        <input type="radio" name="gioi_tinh" value="Nam" <?= $isMale ?>> Nam
                                    </label>
                                    <label>
                                        <input type="radio" name="gioi_tinh" value="Nữ" <?= $isFemale ?>> Nữ
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" name="update_info" class="save-btn">Lưu thay đổi</button>
                    </form>
                </div>
            <?php elseif ($view === 'history'): ?>
                <div class="tab-content <?= ($view === 'history') ? 'active' : '' ?>" id="history">
                    <h2>Lịch sử đọc</h2>

                    <?php
                    // 🔹 Lấy dữ liệu bài viết kèm lượt xem
                    $stmt = $pdo->prepare("
        SELECT 
            b.tieu_de,
            b.duong_dan,
            b.anh_bv,
            b.luot_xem,     -- 👈 Lấy thêm cột lượt xem
            d.ngay_them
        FROM diemdoc d
        JOIN baiviet b ON d.ma_bai_viet = b.ma_bai_viet
        WHERE d.id_kh = ? AND d.loai_giao_dich = 'xem_bai'
        ORDER BY d.ngay_them DESC
    ");
                    $stmt->execute([$user['id_kh']]);
                    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if ($history): ?>
                        <div class="history-grid">
                            <?php foreach ($history as $item): ?>
                                <div class="history-card">
                                    <a href="post.php?slug=<?= htmlspecialchars($item['duong_dan']) ?>">
                                        <div class="thumb">
                                            <img src="<?= !empty($item['anh_bv']) ? htmlspecialchars($item['anh_bv']) : '../img/noimage.jpg' ?>"
                                                alt="<?= htmlspecialchars($item['tieu_de']) ?>">

                                            <!-- ✅ Badge lượt xem -->
                                            <div class="badge-wrap">
                                                <span class="badge badge-views">
                                                    <i class="fa-regular fa-eye"></i> <?= number_format($item['luot_xem']) ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="card-body">
                                            <h3><?= htmlspecialchars($item['tieu_de']) ?></h3>
                                            <p class="time">
                                                <i class="fa-regular fa-clock"></i>
                                                <?= date("d/m/Y H:i", strtotime($item['ngay_them'])) ?>
                                            </p>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Bạn chưa đọc bài viết nào gần đây.</p>
                    <?php endif; ?>
                </div>

            <?php elseif ($view === 'saved'): ?>
                <div class="tab-content active" id="saved">
                    <h2>Bài viết đã lưu</h2>

                    <?php
                    $stmt = $pdo->prepare("
        SELECT b.tieu_de, b.duong_dan, b.anh_bv, b.ngay_dang
        FROM saved_posts s
        JOIN baiviet b ON s.ma_bai_viet = b.ma_bai_viet
        WHERE s.id_kh = ?
        ORDER BY s.saved_at DESC
    ");
                    $stmt->execute([$user['id_kh']]);
                    $saved = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if ($saved): ?>
                        <div class="saved-grid">
                            <?php foreach ($saved as $item): ?>
                                <div class="saved-item">
                                    <a href="post.php?slug=<?= urlencode($item['duong_dan']) ?>">

                                        <img src="<?= htmlspecialchars($item['anh_bv']) ?>" alt="">
                                        <h3><?= htmlspecialchars($item['tieu_de']) ?></h3>
                                        <p><?= date("F d, Y", strtotime($item['ngay_dang'])) ?></p>
                                    </a>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Bạn chưa lưu bài viết nào.</p>
                    <?php endif; ?>
                </div>

            <?php elseif ($view === 'notifications'): ?>
                <div class="tab-content active" id="notifications">
                    <h2>🔔 Thông báo của bạn</h2>

                    <?php
                    // Đánh dấu tất cả thông báo đã đọc
                    $pdo->prepare("UPDATE thongbao SET da_doc = 1 WHERE id_kh = ?")
                        ->execute([$user['id_kh']]);

                    // Lấy thông báo
                    $stmt = $pdo->prepare("
            SELECT noi_dung, created_at, da_doc 
            FROM thongbao 
            WHERE id_kh = ? 
            ORDER BY created_at DESC
        ");
                    $stmt->execute([$user['id_kh']]);
                    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php if ($notifications): ?>
                        <ul class="notification-list">
                            <?php foreach ($notifications as $n): ?>
                                <li class="notification-item <?= $n['da_doc'] ? 'read' : 'unread' ?>">
                                    <p><?= $n['noi_dung'] ?></p> <!-- không htmlspecialchars -->
                                    <span class="time">
                                        🕒 <?= date("d/m/Y H:i", strtotime($n['created_at'])) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Không có thông báo mới.</p>
                    <?php endif; ?>
                </div>

            <?php elseif ($view === 'settings'): ?>
                <div class="tab-content <?= ($view === 'settings') ? 'active' : '' ?>" id="settings">
                    <h2>Cài đặt tài khoản</h2>
                    <p>Bạn có thể tùy chỉnh bảo mật và các thiết lập khác ở đây.</p>

                    <h2>Đổi mật khẩu</h2>
                    <form method="POST" class="password-form">
                        <div class="password-group">
                            <label>Mật khẩu hiện tại:</label>
                            <div class="password-field">
                                <input type="password" id="matkhau_cu" name="matkhau_cu" required>
                                <i class="fa-solid fa-eye" onclick="togglePass('matkhau_cu', this)"></i>
                            </div>

                            <label>Mật khẩu mới:</label>
                            <div class="password-field">
                                <input type="password" id="matkhau_moi" name="matkhau_moi" required>
                                <i class="fa-solid fa-eye" onclick="togglePass('matkhau_moi', this)"></i>
                            </div>

                            <button type="submit" name="update_pass" class="save-btn">🔑 Đổi mật khẩu</button>
                        </div>
                    </form>

                    <?php if (!empty($msg)): ?>
                        <p class="msg">
                            <?= $msg ?>
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Xóa tài khoản -->
                <div class="delete-account">
                    <h3>Xóa tài khoản</h3>
                    <p>Chú ý: Việc xóa tài khoản sẽ không thể hoàn tác. Bạn muốn xóa tài khoản?</p>
                    <form method="POST" action="">
                        <button type="submit" name="delete_account" class="delete-btn">Xóa tài khoản</button>
                    </form>
                </div>

            </div>
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
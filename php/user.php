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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    // Xóa thông tin người dùng khỏi bảng "khachhang"
    $stmt = $pdo->prepare("DELETE FROM khachhang WHERE id_kh = :id");
    $stmt->bindParam(':id', $id_kh);
    $stmt->execute();

    // Xóa thông tin người dùng khỏi bảng "taotaikhoan" nếu có
    $stmt = $pdo->prepare("DELETE FROM taotaikhoan WHERE id_kh = :id");
    $stmt->bindParam(':id', $id_kh);
    $stmt->execute();

    // Đăng xuất người dùng và chuyển hướng về trang chủ
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doixp'])) {
    $addXP = (int) $_POST['add_xp'];
    $currentPoints = (int) $user['so_diem'];

    if ($addXP > 0 && $addXP <= $currentPoints) {
        // Trừ điểm và cộng XP
        $stmt = $pdo->prepare("UPDATE khachhang SET so_diem = so_diem - ?, xp = xp + ? WHERE id_kh = ?");
        $stmt->execute([$addXP, $addXP, $id_kh]);
        echo "<script>alert('Đã đổi $addXP điểm thành XP thành công!'); window.location.reload();</script>";
        exit;
    } elseif ($addXP > $currentPoints) {
        echo "<script>alert('Bạn không đủ điểm để đổi!');</script>";
    } else {
        echo "<script>alert('Vui lòng nhập số XP hợp lệ!');</script>";
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

        $stmt = $pdo->prepare("SELECT mat_khau FROM doimatkhau WHERE id_kh = :id ORDER BY id_dmk DESC LIMIT 1");
        $stmt->execute([':id' => $id_kh]);
        $matkhau = $stmt->fetchColumn();

        if ($matkhau && password_verify($matkhau_cu, $matkhau)) {
            $hash = password_hash($matkhau_moi, PASSWORD_DEFAULT);
            $up = $pdo->prepare("INSERT INTO doimatkhau (id_kh, mat_khau, ngay_tao) VALUES (:id, :matkhau, NOW())");
            $up->execute([':matkhau' => $hash, ':id' => $id_kh]);
            $_SESSION['success'] = "✅ Đổi mật khẩu thành công!";
        } else {
            $_SESSION['error'] = "❌ Mật khẩu hiện tại không đúng!";
        }
        header("Location: user.php");
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

                                    <li><a href="./user.php?view=order"><i class="fas fa-history"></i> Lịch sử</a></li>
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
                    $frame = !empty($user['avatar_frame']) && file_exists('../frames/' . $user['avatar_frame'] . '.png')
                        ? '../frames/' . htmlspecialchars($user['avatar_frame']) . '.png'
                        : '';

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

                <div class="user-name"><?= htmlspecialchars($user['ho_ten']) ?></div>
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
                    $xp = $user['so_diem'] ?? 0;
                    $level = floor($xp / 100); // Mỗi 100 điểm = 1 cấp
                    $nextLevelXP = ($level + 1) * 100;
                    $percent = min(100, ($xp / $nextLevelXP) * 100);
                    ?>
                    <p>Level <?= $level ?> - XP: <?= $xp ?> / <?= $nextLevelXP ?></p>
                    <div class="progress">
                        <div class="progress-fill" style="width: <?= $percent ?>%;"></div>
                    </div>
                </div>

                <p><b>Điểm:</b> <?= number_format($xp) ?></p>
                <p><b>Ngày tạo:</b> <?= htmlspecialchars($user['ngay_tao']) ?></p>

                <!-- Nút mở popup đổi XP -->
                <button type="button" class="xp-btn" onclick="openXPModal()">Đổi XP</button>

                <!-- Popup -->
                <div id="xpModal" class="modal">
                    <div class="modal-content">
                        <span class="close" onclick="closeXPModal()">&times;</span>
                        <h3>Đổi điểm sang XP</h3>

                        <!-- Hiển thị điểm hiện có -->
                        <p class="current-points">Bạn hiện có: <b><?= number_format($user['so_diem'] ?? 0) ?></b>
                            điểm</p>

                        <form method="POST">
                            <label for="add_xp">Nhập số XP muốn đổi:</label>
                            <input type="number" id="add_xp" name="add_xp" min="1" max="<?= $user['so_diem'] ?? 0 ?>"
                                required>
                            <p class="note">💡 1 điểm = 1 XP</p>
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
                            <input type="radio" name="avatar_frame" value="fire1" <?= ($user['avatar_frame'] == 'fire1') ? 'checked' : '' ?>>
                            <img src="../frames/fire1.png" alt="Fire Frame">
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
                    </div>
                    <button type="submit" name="save_frame">Lưu khung</button>
                </form>
            </div>
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
                                echo '<form method="POST" style="display:inline;">
                        <input type="hidden" name="cancel_request_id" value="' . $cau['id'] . '">
                        <button type="submit" name="cancel_request" class="cancel-btn">Hủy yêu cầu</button>
                      </form>';
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
                    <h2>Lịch sử hoạt động</h2>
                    <p>Bạn chưa có hoạt động nào gần đây.</p>
                </div>
            <?php elseif ($view === 'saved'): ?>
                <div class="tab-content <?= ($view === 'saved') ? 'active' : '' ?>" id="saved">
                    <h2>Bài viết đã lưu</h2>
                    <p>Danh sách các bài viết bạn lưu sẽ hiển thị ở đây.</p>
                </div>
            <?php elseif ($view === 'notifications'): ?>
                <div class="tab-content <?= ($view === 'notifications') ? 'active' : '' ?>" id="notifications">
                    <h2>Thông báo</h2>
                    <p>Không có thông báo mới.</p>
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

</body>

</html>
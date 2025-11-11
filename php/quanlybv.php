<?php

session_start();
require_once './db.php';

// ✅ CHỈ ADMIN ĐƯỢC TRUY CẬP
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>🚫 Bạn không có quyền truy cập trang này!</h2>";
    exit;
}

// Kiểm tra người dùng và tính toán cấp độ
$user = null;
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
            $stmt = $pdo->prepare("INSERT INTO baiviet (tieu_de, duong_dan, noi_dung, anh_bv, ma_tac_gia, ma_chuyen_muc, trang_thai, danh_muc, ngay_dang)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$tieu_de, $duong_dan, $noi_dung, $anh_bv, $ma_tac_gia, $ma_chuyen_muc, $trang_thai, $danh_muc]);

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
$baiviet = $pdo->query("SELECT * FROM baiviet ORDER BY ngay_dang ASC")->fetchAll(PDO::FETCH_ASSOC);
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
                    <label>Mã chuyên mục</label>
                    <input type="number" name="ma_chuyen_muc"
                        value="<?= htmlspecialchars($editPost['ma_chuyen_muc'] ?? '') ?>">
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
    <table>
        <thead>
            <tr>
                <th>Mã bài viết</th>
                <th>Ảnh</th>
                <th>Tiêu đề</th>
                <th>Đường dẫn</th>
                <th>Danh mục</th> <!-- 🆕 thêm cột Danh mục -->
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

</body>

</html>
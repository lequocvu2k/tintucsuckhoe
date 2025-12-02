<?php
session_start();
require_once '../php/db.php';

// Kiểm tra quyền admin
if ($_SESSION['username'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$id_kh = $_SESSION['user_id'];

// Lấy thông tin người dùng
$user = null; // Mặc định là khách
$tier = "Member";

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['user_id'])) {
    $id_kh = $_SESSION['user_id']; // Lấy id người dùng từ session
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

        $so_diem = isset($user['so_diem']) && is_numeric($user['so_diem']) ? $user['so_diem'] : 0;
        $diem = tinhDiem($so_diem);
        $tier = xacDinhCapDo($so_diem);
    }
}
if (isset($_POST['duyet'])) {
    $id = $_POST['id']; // id của yêu cầu
    $stmt = $pdo->prepare("UPDATE nhanvien_yc SET trang_thai = 'đã duyệt' WHERE id = ?");
    $stmt->execute([$id]);

    // 🔹 Lấy id_kh để gửi thông báo
    $get = $pdo->prepare("SELECT id_kh, ho_ten FROM nhanvien_yc WHERE id = ?");
    $get->execute([$id]);
    $yc = $get->fetch(PDO::FETCH_ASSOC);

    if ($yc) {
        $id_kh = $yc['id_kh'];
        $name = $yc['ho_ten'];

        // 🔸 Thêm thông báo cho người dùng
        $msg = "🎉 Xin chúc mừng $name! Yêu cầu trở thành nhân viên của bạn đã được duyệt.";
        $insert = $pdo->prepare("INSERT INTO thongbao (id_kh, noi_dung) VALUES (?, ?)");
        $insert->execute([$id_kh, $msg]);
    }

    $_SESSION['success'] = "✅ Đã duyệt yêu cầu và gửi thông báo.";
    header("Location: quanlyyeucau.php");
    exit;
}

// Lấy danh sách yêu cầu từ cơ sở dữ liệu
$stmt = $pdo->prepare("SELECT * FROM nhanvien_yc ORDER BY ngay_tao DESC");
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Xử lý duyệt yêu cầu và chuyển sang chọn vai trò
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_request'])) {
    $request_id = $_POST['request_id'];

    // Cập nhật trạng thái yêu cầu thành 'đã duyệt'
    $stmt = $pdo->prepare("UPDATE nhanvien_yc SET trang_thai = 'đã duyệt' WHERE id = ?");
    $stmt->execute([$request_id]);

    // Lấy id_kh của yêu cầu để cấp quyền
    $stmt = $pdo->prepare("SELECT id_kh FROM nhanvien_yc WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_kh = $request['id_kh'];

    // Lưu id_kh trong session để sử dụng khi chọn vai trò
    $_SESSION['current_request_id'] = $id_kh;

    // Thông báo yêu cầu đã được duyệt
    $_SESSION['msg'] = "✅ Yêu cầu đã được duyệt! Hãy chọn vai trò cho người dùng.";

    // Chuyển hướng về trang quản lý yêu cầu và truyền thêm request_id làm tham số trong URL
    header("Location: quanlyyeucau.php?request_id=" . $request_id);
    exit;
}


// Xử lý khi chọn vai trò
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_role'])) {
    $id_kh = $_SESSION['current_request_id'];
    $role = $_POST['vai_tro'];  // Lấy vai trò từ form chọn

    // Cập nhật vai trò người dùng trong bảng khachhang
    $stmt = $pdo->prepare("UPDATE khachhang SET vai_tro = ? WHERE id_kh = ?");
    $stmt->execute([$role, $id_kh]);

    // Cập nhật vai trò người dùng trong bảng nhanvien_yc
    $stmt = $pdo->prepare("UPDATE nhanvien_yc SET vai_tro = ? WHERE id_kh = ?");
    $stmt->execute([$role, $id_kh]);

    // Cập nhật trạng thái yêu cầu thành 'đã duyệt'
    $stmt = $pdo->prepare("UPDATE nhanvien_yc SET trang_thai = 'đã duyệt' WHERE id_kh = ?");
    $stmt->execute([$id_kh]);

    // Thông báo thành công
    $_SESSION['msg'] = "✅ Vai trò đã được cấp cho người dùng! Yêu cầu đã duyệt.";

    // Chuyển hướng về trang quản lý yêu cầu
    header("Location: quanlyyeucau.php");
    exit;
}


// Xử lý từ chối yêu cầu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_request'])) {
    $request_id = $_POST['request_id'];

    // Cập nhật trạng thái yêu cầu thành 'bị từ chối'
    $stmt = $pdo->prepare("UPDATE nhanvien_yc SET trang_thai = 'bị từ chối' WHERE id = ?");
    $stmt->execute([$request_id]);

    $_SESSION['msg'] = "❌ Yêu cầu đã bị từ chối!";
    header("Location: quanlyyeucau.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý yêu cầu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/menu.css">
    <?php include '../partials/logo.php'; ?>
    <link rel="stylesheet" href="../css/quanlyyeucau.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
</head>

<body>
    <?php include '../partials/header.php'; ?>

    <!-- Nội dung trang Quản lý yêu cầu -->
    <main class="container">
        <h2>Quản lý yêu cầu</h2>

        <?php if (isset($_SESSION['msg'])): ?>
            <div class="message-success">
                <?= htmlspecialchars($_SESSION['msg']); ?>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <table border="1" cellpadding="10">
            <thead>
                <tr>
                    <th>Avatar</th>
                    <th>Họ tên</th>
                    <th>Số điện thoại</th>
                    <th>Thể loại công việc</th>
                    <th>Ngày gửi yêu cầu</th>
                    <th>Trạng thái</th>
                    <th>Vai trò</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td>
                            <div class="avatar-container">
                                <?php
                                // Truy vấn thông tin người dùng dựa trên id_kh
                                $id_kh = $request['id_kh']; // Lấy id_kh từ mỗi yêu cầu
                            
                                // Truy vấn thông tin avatar và khung avatar từ bảng khachhang
                                $stmt = $pdo->prepare("SELECT avatar_url, avatar_frame, vai_tro FROM khachhang WHERE id_kh = ?");
                                $stmt->execute([$id_kh]);
                                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Lấy avatar: nếu có thì dùng avatar của user, nếu không thì dùng avt.jpg mặc định
                                $avatar = (!empty($user['avatar_url']) && file_exists($user['avatar_url']))
                                    ? htmlspecialchars($user['avatar_url'])
                                    : '../img/avt.jpg';  // Avatar mặc định
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

                                // Hiển thị khung avatar nếu có
                                if ($frame) {
                                    echo '<img src="' . $frame . '" alt="Frame" class="frame-overlay1">';
                                }
                                ?>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($request['ho_ten']) ?></td>
                        <td><?= htmlspecialchars($request['sdt']) ?></td>
                        <td><?= htmlspecialchars($request['the_loai']) ?></td>
                        <td><?= date("F d, Y", strtotime($request['ngay_tao'])) ?></td>
                        <td><?= htmlspecialchars($request['trang_thai']) ?></td>
                        <td><?= htmlspecialchars($request['vai_tro']) ?></td>
                        <td>
                            <!-- Hiển thị nút "Duyệt" và "Từ chối" cho tất cả yêu cầu -->
                            <?php if ($request['trang_thai'] === 'chờ duyệt'): ?>
                                <!-- Chỉ hiển thị khi trạng thái là 'chờ duyệt' -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                    <button type="submit" name="approve_request" class="approve-btn">Duyệt</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                    <button type="submit" name="reject_request" class="reject-btn">Từ chối</button>
                                </form>
                            <?php elseif ($request['trang_thai'] === 'đã duyệt' || $request['trang_thai'] === 'bị từ chối'): ?>
                                <!-- Hiển thị form chọn vai trò khi yêu cầu đã được duyệt hoặc từ chối -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                    <button type="submit" name="approve_request" class="approve-btn"
                                        onclick="showRoleSelection(<?= $request['id'] ?>)">Duyệt</button>
                                </form>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                    <button type="submit" name="reject_request" class="reject-btn">Từ chối</button>
                                </form>
                            <?php endif; ?>
                        </td>

                        <?php
                        // Kiểm tra nếu có tham số request_id trong URL
                        if (isset($_GET['request_id'])) {
                            $request_id = $_GET['request_id'];

                            // Lấy yêu cầu từ cơ sở dữ liệu
                            $stmt = $pdo->prepare("SELECT * FROM nhanvien_yc WHERE id = ?");
                            $stmt->execute([$request_id]);
                            $request = $stmt->fetch(PDO::FETCH_ASSOC);

                            // Kiểm tra trạng thái yêu cầu
                            if ($request['trang_thai'] === 'đã duyệt') {
                                // Hiển thị form chọn vai trò
                                ?>
                                <div id="role-selection-<?= $request['id'] ?>" class="role-selection" style="display:block;">
                                    <h3>Chọn vai trò cho người dùng</h3>
                                    <form method="POST">
                                        <label for="vai_tro">Chọn vai trò:</label>
                                        <select name="vai_tro" id="vai_tro" required>
                                            <option value="Khach">Khách</option>
                                            <option value="NhanVien">Nhân viên</option>
                                            <option value="QuanTri">Quản trị viên</option>
                                        </select>
                                        <button type="submit" name="assign_role" class="assign-role-btn">Cấp vai trò</button>
                                        <!-- Nút hủy -->
                                        <a href="quanlyyeucau.php" class="cancel-btn">Hủy</a>
                                    </form>
                                </div>
                                <?php
                            }
                        }
                        ?>


                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </main>
    <?php include '../partials/footer.php'; ?>
    <script>
        function showRoleSelection(requestId) {
            const roleSelection = document.getElementById(`role-selection-${requestId}`);
            if (roleSelection) {
                roleSelection.style.display = 'block'; // Hiển thị form
            }
        }
    </script>
</body>

</html>
<?php
session_start();
require_once "../php/db.php";

// CHỈ ADMIN ĐƯỢC VÀO
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== "QuanTri") {
    die("<h2 style='color:red;text-align:center;'>⛔ Bạn không có quyền truy cập.</h2>");
}

// Xử lý các hành động
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    switch ($action) {
        case "ban":
            $pdo->prepare("UPDATE khachhang SET is_banned = 1 WHERE id_kh = ?")->execute([$id]);
            $_SESSION['message'] = "Đã BAN tài khoản ID: $id";
            break;

        case "unban":
            $pdo->prepare("UPDATE khachhang SET is_banned = 0 WHERE id_kh = ?")->execute([$id]);
            $_SESSION['message'] = "Đã GỠ BAN tài khoản ID: $id";
            break;

        case "mute":
            $time = $_GET['time'] ?? "forever";

            switch ($time) {
                case "5m":
                    $pdo->prepare("UPDATE khachhang SET is_muted = 1, muted_until = NOW() + INTERVAL 5 MINUTE WHERE id_kh = ?")->execute([$id]);
                    $_SESSION['message'] = "Đã CẤM CHAT 5 PHÚT cho ID: $id";
                    break;

                case "1h":
                    $pdo->prepare("UPDATE khachhang SET is_muted = 1, muted_until = NOW() + INTERVAL 1 HOUR WHERE id_kh = ?")->execute([$id]);
                    $_SESSION['message'] = "Đã CẤM CHAT 1 GIỜ cho ID: $id";
                    break;

                case "1d":
                    $pdo->prepare("UPDATE khachhang SET is_muted = 1, muted_until = NOW() + INTERVAL 1 DAY WHERE id_kh = ?")->execute([$id]);
                    $_SESSION['message'] = "Đã CẤM CHAT 1 NGÀY cho ID: $id";
                    break;

                case "7d":
                    $pdo->prepare("UPDATE khachhang SET is_muted = 1, muted_until = NOW() + INTERVAL 7 DAY WHERE id_kh = ?")->execute([$id]);
                    $_SESSION['message'] = "Đã CẤM CHAT 1 TUẦN cho ID: $id";
                    break;

                case "forever":
                    $pdo->prepare("UPDATE khachhang SET is_muted = 1, muted_until = NULL WHERE id_kh = ?")->execute([$id]);
                    $_SESSION['message'] = "Đã CẤM CHAT VĨNH VIỄN cho ID: $id";
                    break;
            }
            break;

        case "unmute":
            $pdo->prepare("UPDATE khachhang SET is_muted = 0, muted_until = NULL WHERE id_kh = ?")->execute([$id]);
            $_SESSION['message'] = "Đã GỠ CẤM CHAT cho ID: $id";
            break;

        case "role":
            if (isset($_GET['value'])) {
                $role = $_GET['value'];
                $pdo->prepare("UPDATE khachhang SET vai_tro = ? WHERE id_kh = ?")->execute([$role, $id]);
                $_SESSION['message'] = "Đã đổi vai trò ID $id thành: $role";
            }
            break;
    }

    header("Location: quanlynguoidung.php");
    exit;
}

// Tìm kiếm
$search = $_GET['q'] ?? "";
$sql = "SELECT * FROM khachhang WHERE 1";

if ($search != "") {
    $sql .= " AND (ho_ten LIKE :s OR email LIKE :s)";
}

$sql .= " ORDER BY id_kh ASC";


$stmt = $pdo->prepare($sql);
if ($search != "") {
    $stmt->bindValue(":s", "%$search%");
}
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/quanlynguoidung.css">
    <link rel="stylesheet" href="../css/menu.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert-box">
            <?= $_SESSION['message']; ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <h2>🛠️ QUẢN LÝ NGƯỜI DÙNG</h2>
    <div class="search-box">
        <form method="GET" class="search-form">
            <input type="text" name="q" class="search-input" placeholder="Tìm kiếm tên hoặc email..."
                value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <table>
        <tr>


            <th>ID</th>
            <th>Avatar</th>
            <th>Tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        <?php if (count($users) === 0): ?>
            <tr class="no-result-row">
                <td colspan="7" class="no-result">
                    ❌ Không tìm thấy người dùng nào!
                </td>
            </tr>
        <?php endif; ?>
        <?php foreach ($users as $u): ?>

            <?php
            // ============================================
            // Tính thời gian còn lại khi user bị mute
            // ============================================
        
            $muteRemaining = "";

            if ($u['is_muted'] == 1) {

                // Nếu muted_until có giá trị → Mute theo thời gian
                if (!empty($u['muted_until'])) {

                    date_default_timezone_set("Asia/Ho_Chi_Minh");

                    // Đồng bộ timezone MySQL ↔ PHP
                    $pdo->exec("SET time_zone = '+07:00'");

                    $now = time();
                    $end = strtotime($u['muted_until']);


                    $end = strtotime($u['muted_until']);

                    // Nếu đã hết hạn → tự unmute
                    if ($end <= $now) {
                        $pdo->prepare("UPDATE khachhang SET is_muted = 0, muted_until = NULL WHERE id_kh = ?")
                            ->execute([$u['id_kh']]);

                        $u['is_muted'] = 0;
                        $u['muted_until'] = null;

                    } else {
                        // Tính thời gian còn lại
                        $diff = $end - $now;
                        $days = floor($diff / 86400);
                        $hours = floor(($diff % 86400) / 3600);
                        $mins = floor(($diff % 3600) / 60);

                        if ($days > 0) {
                            $muteRemaining = "(Còn $days ngày $hours giờ)";
                        } elseif ($hours > 0) {
                            $muteRemaining = "(Còn $hours giờ $mins phút)";
                        } else {
                            $muteRemaining = "(Còn $mins phút)";
                        }
                    }

                } else {
                    // Mute vĩnh viễn
                    $muteRemaining = "(Vĩnh viễn)";
                }
            }
            ?>

            <tr>
                <td><?= $u['id_kh'] ?></td>

                <td>
                    <img src="<?= $u['avatar_url'] ?: '../img/avt.jpg' ?>" width="45" height="45"
                        style="border-radius:50%;">
                </td>

                <td><?= htmlspecialchars($u['ho_ten'] ?? 'Không tên') ?></td>

                <td><?= htmlspecialchars($u['email']) ?></td>

                <!-- Vai trò -->
                <td>
                    <?php if ($u['vai_tro'] === "QuanTri"): ?>
                        <span class="badge badge-admin">Admin</span>
                    <?php elseif ($u['vai_tro'] === "NhanVien"): ?>
                        <span class="badge badge-nv">Nhân viên</span>
                    <?php else: ?>
                        <span class="badge badge-khach">Khách</span>
                    <?php endif; ?>
                </td>

                <!-- Trạng thái -->
                <td class="status">
                    <?= $u['is_banned'] ? "<span class='banned'>Bị Ban</span>" : "Hoạt động" ?><br>

                    <?php if ($u['is_muted']): ?>
                        <span class="muted">
                            Cấm chat
                            <?php if (!empty($muteRemaining)): ?>
                                <small style="color:#ff8800;"> <?= $muteRemaining ?> </small>
                            <?php endif; ?>
                        </span>
                    <?php endif; ?>
                </td>

                <!-- Hành động -->
                <td>
                    <?php if ($u['vai_tro'] !== "QuanTri"): ?>

                        <!-- Ban / Unban -->
                        <?php if (!$u['is_banned']): ?>
                            <a class="btn ban" href="?action=ban&id=<?= $u['id_kh'] ?>">
                                Ban
                            </a>
                        <?php else: ?>
                            <a class="btn unban" href="?action=unban&id=<?= $u['id_kh'] ?>">
                                Unban
                            </a>
                        <?php endif; ?>

                        <!-- Mute / Unmute -->
                        <?php if ($u['is_muted'] == 0): ?>
                            <div class="mute-menu">
                                <button class="btn mute">Mute Chat ▼</button>
                                <div class="mute-options">
                                    <a href="?action=mute&id=<?= $u['id_kh'] ?>&time=5m">5 phút</a>
                                    <a href="?action=mute&id=<?= $u['id_kh'] ?>&time=1h">1 giờ</a>
                                    <a href="?action=mute&id=<?= $u['id_kh'] ?>&time=1d">1 ngày</a>
                                    <a href="?action=mute&id=<?= $u['id_kh'] ?>&time=7d">1 tuần</a>
                                    <a href="?action=mute&id=<?= $u['id_kh'] ?>&time=forever">Vĩnh viễn</a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a class="btn unmute" href="?action=unmute&id=<?= $u['id_kh'] ?>">
                                Unmute
                            </a>
                        <?php endif; ?>

                    <?php else: ?>
                        <span style="color:#888;">Không áp dụng</span>
                    <?php endif; ?>
                </td>

            </tr>

        <?php endforeach; ?>


    </table>
    <?php include '../partials/footer.php'; ?>
</body>

</html>
<?php
session_start();
require_once '../php/db.php';
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

// ✅ Chỉ cho admin hoặc nhân viên
if (
    !isset($_SESSION['username']) ||
    (
        $_SESSION['username'] !== 'admin'
        && ($_SESSION['user_role'] ?? '') !== 'NhanVien'
    )
) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>🚫 Bạn không có quyền truy cập trang này!</h2>";
    exit;
}

$id_kh = $_SESSION['user_id'] ?? null; // id khách hàng
if (!$id_kh) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>⚠️ Không xác định được tài khoản!</h2>";
    exit;
}
$stmtQ = $pdo->prepare("
    SELECT h.*, k.ho_ten 
    FROM hoi_dap h 
    JOIN khachhang k ON h.id_nguoi_hoi = k.id_kh
    WHERE id_chuyen_gia = ? AND cau_tra_loi IS NULL
");
$stmtQ->execute([$id_kh]);

$questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

// Lấy thông tin chuyên gia hiện tại
$stmt = $pdo->prepare("SELECT ho_ten, avatar_url, is_chuyen_gia, chuyen_mon, mo_ta_chuyen_gia 
                       FROM khachhang WHERE id_kh = ?");
$stmt->execute([$id_kh]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$info) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>⚠️ Không tìm thấy tài khoản!</h2>";
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_chuyen_gia = isset($_POST['is_chuyen_gia']) ? 1 : 0;
    $chuyen_mon = trim($_POST['chuyen_mon'] ?? '');
    $mo_ta = trim($_POST['mo_ta'] ?? '');

    // Nếu bật chuyên gia mà chưa nhập chuyên môn -> báo lỗi
    if ($is_chuyen_gia && $chuyen_mon === '') {
        $message = "<p style='color:red;'>⚠️ Vui lòng nhập chuyên môn của bạn!</p>";
    } else {
        $stmtUpdate = $pdo->prepare("
            UPDATE khachhang
            SET is_chuyen_gia = :is_chuyen_gia,
                chuyen_mon = :chuyen_mon,
                mo_ta_chuyen_gia = :mo_ta
            WHERE id_kh = :id_kh
        ");
        $stmtUpdate->execute([
            ':is_chuyen_gia' => $is_chuyen_gia,
            ':chuyen_mon' => $chuyen_mon ?: null,
            ':mo_ta' => $mo_ta ?: null,
            ':id_kh' => $id_kh
        ]);

        $message = "<p style='color:green;'>✅ Cập nhật hồ sơ chuyên gia thành công!</p>";

        // Cập nhật lại biến info
        $info['is_chuyen_gia'] = $is_chuyen_gia;
        $info['chuyen_mon'] = $chuyen_mon;
        $info['mo_ta_chuyen_gia'] = $mo_ta;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hồ sơ Chuyên gia sức khỏe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <?php include '../partials/logo.php'; ?>
    <link rel="stylesheet" href="../css/expert_profile.css">
    <link rel="stylesheet" href="../css/menu.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>

</head>

<body>
    <?php include '../partials/header.php'; ?>

    <div class="expert-container">
        <?php if (isset($_GET['sent_answer'])): ?>
            <div class="alert-success">
                🎉 <b>Đã gửi câu trả lời thành công!</b>
            </div>
        <?php endif; ?>

        <div class="expert-header">
            <img src="<?= htmlspecialchars($info['avatar_url'] ?: './img/avt.jpg') ?>" alt="Avatar">
            <div>
                <h1><?= htmlspecialchars($info['ho_ten'] ?: 'Chưa có tên') ?></h1>
                <?php if (!empty($info['is_chuyen_gia'])): ?>
                    <span>✅ Đang là Chuyên gia sức khỏe</span>
                <?php else: ?>
                    <span style="background:#fff3e0;color:#ef6c00;">❗ Chưa kích hoạt chuyên gia</span>
                <?php endif; ?>
            </div>
        </div>

        <?= $message ?>

        <form method="POST" class="expert-form">
            <div class="checkbox-row">
                <label>
                    <input type="checkbox" name="is_chuyen_gia" value="1" <?= !empty($info['is_chuyen_gia']) ? 'checked' : '' ?>>
                    Tôi muốn hiển thị như một <b>Chuyên gia sức khỏe</b> trên website.
                </label>
            </div>

            <label for="chuyen_mon">Chuyên môn chính (ví dụ: Dinh dưỡng, Tập luyện, Giấc ngủ...)</label>
            <input type="text" id="chuyen_mon" name="chuyen_mon"
                value="<?= htmlspecialchars($info['chuyen_mon'] ?? '') ?>">

            <label for="mo_ta">Giới thiệu ngắn về bạn (kinh nghiệm, chứng chỉ, phong cách tư vấn...)</label>
            <textarea id="mo_ta" name="mo_ta"><?= htmlspecialchars($info['mo_ta_chuyen_gia'] ?? '') ?></textarea>

            <button type="submit">Lưu hồ sơ</button>
        </form>

        <br>
        <?php if ($questions): ?>
            <h2>📩 Câu hỏi từ người dùng</h2>
            <?php foreach ($questions as $q): ?>
                <div class="qa-box">
                    <p><b><?= htmlspecialchars($q['ho_ten']) ?> hỏi:</b> <?= nl2br($q['cau_hoi']) ?></p>

                    <form action="../controller/send_answer.php" method="POST">
                        <input type="hidden" name="id" value="<?= $q['id'] ?>">
                        <textarea name="answer" placeholder="Nhập câu trả lời..." required></textarea>
                        <button type="submit" class="reply-btn">Trả lời</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>⛱️ Hiện chưa có câu hỏi nào cần trả lời.</p>
        <?php endif; ?>

        <br>
        <a class="back-link" href="./index.php">← Quay lại trang chủ</a>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>

</html>
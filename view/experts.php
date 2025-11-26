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
// Lấy danh sách chuyên gia (chỉ những ai is_chuyen_gia = 1)
$stmt = $pdo->prepare("
    SELECT id_kh, ho_ten, avatar_url, chuyen_mon, mo_ta_chuyen_gia
    FROM khachhang
    WHERE is_chuyen_gia = 1
    ORDER BY id_kh DESC
");
$stmt->execute();
$experts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chuyên gia sức khỏe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/experts.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/popup.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/popup.js"></script>

</head>

<body>
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/login.php'; ?>
    <div class="experts-wrapper">
        <div class="experts-title">
            <h1>👨‍⚕️ Chuyên gia sức khỏe</h1>
            <p>Đội ngũ cộng tác viên & nhân viên chia sẻ kiến thức sức khỏe đáng tin cậy.</p>
        </div>

        <?php if (!$experts): ?>
            <p style="text-align:center;">Hiện chưa có chuyên gia nào được hiển thị.</p>
        <?php else: ?>
            <div class="experts-grid">
                <?php foreach ($experts as $cg): ?>
                    <div class="expert-card">
                        <img src="<?= htmlspecialchars($cg['avatar_url'] ?: './img/avt.jpg') ?>" alt="Avatar">
                        <div class="expert-name"><?= htmlspecialchars($cg['ho_ten'] ?: 'Chưa có tên') ?></div>
                        <?php if (!empty($cg['chuyen_mon'])): ?>
                            <div class="expert-speciality">Chuyên môn: <?= htmlspecialchars($cg['chuyen_mon']) ?></div>
                        <?php endif; ?>
                        <?php if (!empty($cg['mo_ta_chuyen_gia'])): ?>
                            <div class="expert-desc">
                                <?= nl2br(htmlspecialchars(mb_strimwidth($cg['mo_ta_chuyen_gia'], 0, 140, '...'))) ?>
                            </div>
                        <?php endif; ?>
                        <a href="expert_detail.php?id=<?= (int) $cg['id_kh'] ?>">Xem hồ sơ & bài viết →</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>

</html>
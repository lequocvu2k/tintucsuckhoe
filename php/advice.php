<?php
session_start();
require_once './db.php'; // file bạn đã có

$ketqua = [];
$tu_khoa = '';

if (isset($_GET['symptom'])) {
    $tu_khoa = trim($_GET['symptom']);
    if ($tu_khoa !== '') {
        $sql = "
SELECT DISTINCT b.ma_bai_viet, b.tieu_de, b.duong_dan, b.anh_bv, b.ngay_dang, c.ten_chuyen_muc
FROM baiviet b
LEFT JOIN chuyenmuc c ON b.ma_chuyen_muc = c.ma_chuyen_muc
WHERE 
    b.tieu_de LIKE :kw 
    OR b.noi_dung LIKE :kw
    OR c.ten_chuyen_muc LIKE :kw
ORDER BY b.ngay_dang DESC LIMIT 12
";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':kw' => "%$tu_khoa%"]);
        $ketqua = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tư vấn sức khỏe theo triệu chứng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/advice.css">
    <link rel="stylesheet" href="../css/menu.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>

</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="advice-container">
        <h1><i class="fa-solid fa-stethoscope"></i> Tư vấn sức khỏe theo triệu chứng</h1>

        <p class="des">Nhập triệu chứng bạn đang gặp để nhận gợi ý:</p>

        <form method="GET">
            <input type="text" name="symptom" placeholder="Ví dụ: đau lưng, mất ngủ..."
                value="<?= htmlspecialchars($tu_khoa) ?>" required>
            <button type="submit">🔍 Tư vấn ngay</button>
        </form>

        <?php if ($tu_khoa !== ''): ?>
            <h2>Kết quả cho triệu chứng: <span class="highlight">“<?= htmlspecialchars($tu_khoa) ?>”</span></h2>

            <?php if ($ketqua): ?>
                <div class="advice-grid">
                    <?php foreach ($ketqua as $bv): ?>
                        <a class="advice-item" href="post.php?slug=<?= urlencode($bv['duong_dan']) ?>">
                            <img src="<?= htmlspecialchars($bv['anh_bv']) ?>" alt="">
                            <h3><?= htmlspecialchars($bv['tieu_de']) ?></h3>

                            <span class="tag-item"><?= htmlspecialchars($bv['ten_chuyen_muc']) ?></span>

                            <p><small>📅 <?= date("d/m/Y", strtotime($bv['ngay_dang'])) ?></small></p>

                        </a>

                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-result">❌ Không tìm thấy kết quả phù hợp. Vui lòng thử từ khóa khác.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php include '../partials/footer.php'; ?>

</body>

</html>
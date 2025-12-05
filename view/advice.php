<?php
session_start();
require_once '../php/db.php'; // file bạn đã có

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
include '../partials/menu.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tư vấn sức khỏe theo triệu chứng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <?php include '../partials/logo.php'; ?>
    <link rel="stylesheet" href="../css/advice.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/popup.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>

</head>

<body>
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/login.php'; ?>
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
                            <img src="/php/<?= htmlspecialchars($bv['anh_bv']) ?>" alt="">
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

<?php
require_once '../php/db.php';

$q = trim($_GET['q'] ?? '');

$stmt = $pdo->prepare("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published'
      AND (
            tieu_de LIKE ? 
            OR ma_chuyen_muc LIKE ?
          )
    ORDER BY ngay_dang DESC
");

$keyword = "%$q%";
$stmt->execute([$keyword, $keyword]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <title>Kết quả tìm kiếm</title>
</head>

<body>
    <h2>Kết quả tìm kiếm cho: <?= htmlspecialchars($q) ?></h2>

    <?php if ($results): ?>
        <?php foreach ($results as $r): ?>
            <div class="result-item">
                <a href="post.php?slug=<?= urlencode($r['duong_dan']) ?>">
                    <img src="/php/<?= $r['anh_bv'] ?>" width="120">
                    <h3><?= htmlspecialchars($r['tieu_de']) ?></h3>
                    <p>Mã chuyên mục: <?= htmlspecialchars($r['ma_chuyen_muc']) ?></p>
                </a>
            </div>
            <hr>
        <?php endforeach; ?>

    <?php else: ?>
        <p>Không tìm thấy bài viết.</p>
    <?php endif; ?>

</body>

</html>
<?php
session_start();
require '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    die("NOT_LOGIN");
}

$id_kh = $_SESSION['user_id'];
$noi_dung = trim($_POST['noi_dung'] ?? '');

/* ==========================
   ⭐ KIỂM TRA 24 GIỜ CHỈ 1 LẦN
   ========================== */

$check = $pdo->prepare("
    SELECT id FROM status 
    WHERE id_kh = ? 
      AND ngay_dang >= NOW() - INTERVAL 24 HOUR
    LIMIT 1
");
$check->execute([$id_kh]);

if ($check->rowCount() > 0) {
    echo "ONLY_ONE_PER_DAY"; // gửi về JS
    exit;
}

/* ==========================
   ⭐ NẾU CHƯA TỪNG ĐĂNG → CHO ĐĂNG
   ========================== */
$stmt = $pdo->prepare("
    INSERT INTO status(id_kh, noi_dung, ngay_dang) 
    VALUES(?, ?, NOW())
");
$stmt->execute([$id_kh, $noi_dung]);

echo "SUCCESS";
exit;

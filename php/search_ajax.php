<?php
require_once "./db.php";

$q = trim($_GET['q'] ?? '');

if ($q == "") {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT ma_bai_viet, tieu_de, duong_dan, anh_bv
    FROM baiviet
    WHERE trang_thai = 'published'
      AND (tieu_de LIKE ? OR ma_chuyen_muc LIKE ?)
    LIMIT 10
");

$search = "%$q%";
$stmt->execute([$search, $search]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data);
exit;
?>
<?php
session_start();
require '../php/db.php';

if (!isset($_SESSION['user_id'])) {
    echo "not_login";
    exit;
}

$id_kh = $_SESSION['user_id'];
$noi_dung = trim($_POST['noi_dung'] ?? "");
$anh = null;

// Nếu có upload ảnh trạng thái (tùy bạn thêm sau)
if (isset($_FILES['file']) && $_FILES['file']['error'] === 0) {
    $dir = "../uploads/status/";
    if (!is_dir($dir))
        mkdir($dir, 0777, true);

    $fileName = time() . "_" . $_FILES['file']['name'];
    $target = $dir . $fileName;
    move_uploaded_file($_FILES['file']['tmp_name'], $target);

    $anh = "uploads/status/" . $fileName;
}

if ($noi_dung === "") {
    echo "empty";
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO status (id_kh, noi_dung, anh_dinh_kem)
    VALUES (?, ?, ?)
");

if ($stmt->execute([$id_kh, $noi_dung, $anh])) {
    echo "ok";
} else {
    echo "error";
}

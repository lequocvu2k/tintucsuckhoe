<?php
session_start();
require_once '../php/db.php';

if (!isset($_SESSION['user_id'])) exit(json_encode(["status"=>"error"]));

$id_kh = $_SESSION['user_id'];
$ma_bai_viet = $_POST['ma_bai_viet'];

// 💙 Kiểm tra đã like chưa
$check = $pdo->prepare("SELECT * FROM likes WHERE id_kh=? AND ma_bai_viet=?");
$check->execute([$id_kh, $ma_bai_viet]);

if ($check->rowCount() == 0) {
    // Thêm like
    $pdo->prepare("INSERT INTO likes(id_kh, ma_bai_viet) VALUES (?, ?)")->execute([$id_kh, $ma_bai_viet]);

    // ✔ Cộng điểm + lưu lịch sử
    $pdo->prepare("UPDATE khachhang SET so_diem = so_diem + 12 WHERE id_kh = ?")->execute([$id_kh]);
    $pdo->prepare("
        INSERT INTO diemdoc(id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
        VALUES (?, ?, 12, 'like_bai', NOW())
    ")->execute([$id_kh, $ma_bai_viet]);
}

echo json_encode(["status" => "success"]);
?>

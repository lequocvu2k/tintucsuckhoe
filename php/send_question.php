<?php
session_start();
require_once './db.php';

if (!isset($_SESSION['user_id'])) {
    die("Không được phép!");
}

$id_user = $_SESSION['user_id'];
$id_chuyen_gia = $_POST['id_chuyen_gia'];
$cau_hoi = trim($_POST['question']);

if ($cau_hoi == '') {
    die("Câu hỏi không hợp lệ!");
}

$stmt = $pdo->prepare("INSERT INTO hoi_dap(id_nguoi_hoi, id_chuyen_gia, cau_hoi) VALUES(?,?,?)");
$stmt->execute([$id_user, $id_chuyen_gia, $cau_hoi]);

header("Location: expert_detail.php?id=" . $id_chuyen_gia . "&sent=1");
exit;

?>
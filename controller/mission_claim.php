<?php
session_start();
require_once "../php/db.php";

if (!isset($_SESSION['user_id']))
    exit;
$id_kh = $_SESSION['user_id'];
$mid = $_POST['mid'] ?? 0;

$today = date("Y-m-d");

// Lấy nhiệm vụ hôm nay
$stmt = $pdo->prepare("
    SELECT md.*, mp.reward 
    FROM mission_daily md
    JOIN (
        SELECT 1 AS id, 20 AS reward UNION
        SELECT 2, 40 UNION
        SELECT 3, 80 UNION
        SELECT 4, 30 UNION
        SELECT 5, 60
    ) mp ON mp.id = md.mission_id
    WHERE md.id_kh = ? AND md.mission_id = ? AND md.ngay = ?
");
$stmt->execute([$id_kh, $mid, $today]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$m || $m['da_nhan'] == 1)
    exit;

// Đánh dấu đã nhận
$pdo->prepare("UPDATE mission_daily SET da_nhan = 1 WHERE id = ?")
    ->execute([$m['id']]);

// Cộng điểm
$pdo->prepare("UPDATE khachhang SET so_diem = so_diem + ? WHERE id_kh = ?")
    ->execute([$m['reward'], $id_kh]);

$_SESSION['success'] = "🎉 Nhận {$m['reward']} điểm thành công!";
header("Location: ../view/index.php");
exit;
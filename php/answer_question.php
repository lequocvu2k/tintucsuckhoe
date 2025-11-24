<?php
session_start();
require_once './db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_hoi = $_POST['id'] ?? null;
    $answer = trim($_POST['answer'] ?? '');

    if (!$id_hoi || $answer === '') {
        header("Location: expert_profile.php");
        exit;
    }

    // 1️⃣ Lấy thông tin người hỏi + chuyên gia
    $stmt = $pdo->prepare("SELECT id_nguoi_hoi, id_chuyen_gia FROM hoi_dap WHERE id = ?");
    $stmt->execute([$id_hoi]);
    $q = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$q) {
        header("Location: expert_profile.php");
        exit;
    }

    $id_nguoi_hoi = $q['id_nguoi_hoi'];
    $id_chuyen_gia = $q['id_chuyen_gia'];

    // 2️⃣ Update câu trả lời
    $stmt = $pdo->prepare("UPDATE hoi_dap SET cau_tra_loi = ?, ngay_tra_loi = NOW() WHERE id = ?");
    $stmt->execute([$answer, $id_hoi]);

    // 3️⃣ Gửi thông báo cho người hỏi
    $msg = "🎉 Câu hỏi của bạn đã được chuyên gia trả lời! <a href='./user.php?view=history'>Xem ngay</a>";
    $stmtNotify = $pdo->prepare("INSERT INTO thongbao (id_kh, noi_dung, created_at, da_doc) VALUES (?, ?, NOW(), 0)");
    $stmtNotify->execute([$id_nguoi_hoi, $msg]);

    // 4️⃣ Thưởng uy tín cho chuyên gia
    $pdo->prepare("UPDATE khachhang SET xp = xp + 15 WHERE id_kh = ?")->execute([$id_chuyen_gia]);

    $_SESSION['success'] = "💬 Đã trả lời câu hỏi!";
    header("Location: expert_profile.php");
    exit;
}

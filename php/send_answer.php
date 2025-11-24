<?php
session_start();
require_once './db.php';

// Chỉ admin hoặc nhân viên mới được trả lời
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'NhanVien' && $_SESSION['user_role'] !== 'QuanTri')) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>🚫 Bạn không có quyền trả lời!</h2>";
    exit;
}

// Nhận dữ liệu gửi từ form
$id_hoi_dap = intval($_POST['id'] ?? 0);
$cau_tra_loi = trim($_POST['answer'] ?? '');

if ($id_hoi_dap <= 0 || $cau_tra_loi === '') {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>⚠️ Dữ liệu không hợp lệ!</h2>";
    exit;
}

// Cập nhật câu trả lời
$stmt = $pdo->prepare("UPDATE hoi_dap SET cau_tra_loi = :answer, ngay_tra_loi = NOW() WHERE id = :id");
$stmt->execute([
    ':answer' => $cau_tra_loi,
    ':id' => $id_hoi_dap
]);

// Lấy người hỏi
$stmtUser = $pdo->prepare("SELECT id_nguoi_hoi FROM hoi_dap WHERE id = ?");
$stmtUser->execute([$id_hoi_dap]);
$id_nguoi_hoi = $stmtUser->fetchColumn();

// 🎁 CỘNG ĐIỂM THƯỞNG CHO NGƯỜI HỎI
$stmtReward = $pdo->prepare("UPDATE hoi_dap SET diem_thuong = 10 WHERE id = ?");
$stmtReward->execute([$id_hoi_dap]);

$stmtAddPoint = $pdo->prepare("UPDATE khachhang SET so_diem = so_diem + 10 WHERE id_kh = ?");
$stmtAddPoint->execute([$id_nguoi_hoi]);

// 🔔 Gửi thông báo cho người hỏi + link đúng
$stmtNotify = $pdo->prepare("
    INSERT INTO thongbao (id_kh, noi_dung, id_hoi_dap, created_at)
    VALUES (?, ?, ?, NOW())
");
$stmtNotify->execute([
    $id_nguoi_hoi,
    "💬 Câu hỏi của bạn đã được chuyên gia trả lời. <a href='user.php?view=notifications#tb{$id_hoi_dap}'>Xem chi tiết</a>",
    $id_hoi_dap
]);

// 🔙 Quay lại
header("Location: expert_profile.php?sent_answer=1");
exit;

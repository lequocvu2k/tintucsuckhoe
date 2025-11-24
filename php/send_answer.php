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
$stmt = $pdo->prepare("UPDATE hoi_dap SET cau_tra_loi = :answer WHERE id = :id");
$stmt->execute([
    ':answer' => $cau_tra_loi,
    ':id' => $id_hoi_dap
]);

// Lấy thông tin người hỏi
$stmtUser = $pdo->prepare("SELECT id_nguoi_hoi FROM hoi_dap WHERE id = ?");
$stmtUser->execute([$id_hoi_dap]);
$id_nguoi_hoi = $stmtUser->fetchColumn();

// Gửi thông báo cho người hỏi
// Gửi thông báo cho người hỏi
if ($id_nguoi_hoi) {
    $stmtNotify = $pdo->prepare("
        INSERT INTO thongbao (id_kh, noi_dung, id_hoi_dap, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmtNotify->execute([$id_nguoi_hoi, '', $id_hoi_dap]);

    // 👉 Lấy ID thông báo vừa tạo
    $tb_id = $pdo->lastInsertId();

    // 👉 Tạo nội dung chứa link đúng ID thông báo
    $noi_dung = "💬 Câu hỏi của bạn đã được chuyên gia trả lời. ";

    // 👉 Cập nhật lại thông báo
    $stmtUpdate = $pdo->prepare("UPDATE thongbao SET noi_dung = ? WHERE id = ?");
    $stmtUpdate->execute([$noi_dung, $tb_id]);
}


// Quay lại trang chuyên gia
header("Location: expert_profile.php?sent_answer=1");
exit;

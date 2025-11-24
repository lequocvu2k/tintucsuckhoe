<?php
session_start();
require_once './db.php';

if (!isset($_SESSION['user_id'])) {
    die("<script>alert('⚠️ Bạn phải đăng nhập mới có thể đặt câu hỏi!'); history.back();</script>");
}

$id_user = $_SESSION['user_id'];
$id_chuyen_gia = $_POST['id_chuyen_gia'] ?? 0;
$cau_hoi = trim($_POST['question'] ?? '');

if ($cau_hoi === '') {
    die("<script>alert('❌ Nội dung câu hỏi không được để trống!'); history.back();</script>");
}

/* ===========================================================
   ⛔ GIỚI HẠN 1: 3 CÂU / 1 NGÀY / 1 CHUYÊN GIA
   =========================================================== */
$stmtCount = $pdo->prepare("
    SELECT COUNT(*) AS total 
    FROM hoi_dap 
    WHERE id_nguoi_hoi = ? 
      AND id_chuyen_gia = ?
      AND DATE(ngay_hoi) = CURDATE()
");
$stmtCount->execute([$id_user, $id_chuyen_gia]);
$total_daily = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];

if ($total_daily >= 3) {
    die("<script>alert('🚫 Bạn chỉ được hỏi tối đa 3 lần / ngày cho chuyên gia này!'); history.back();</script>");
}

/* ===========================================================
   ⏳ GIỚI HẠN 2: CHỜ 30 GIÂY MỚI ĐƯỢC GỬI TIẾP
   =========================================================== */
$stmtLast = $pdo->prepare("
    SELECT TIMESTAMPDIFF(SECOND, ngay_hoi, NOW()) AS seconds 
    FROM hoi_dap 
    WHERE id_nguoi_hoi = ? AND id_chuyen_gia = ? 
    ORDER BY ngay_hoi DESC LIMIT 1
");
$stmtLast->execute([$id_user, $id_chuyen_gia]);
$last = $stmtLast->fetch(PDO::FETCH_ASSOC);

if ($last && $last['seconds'] < 30) {
    $remain = 30 - $last['seconds'];
    die("<script>alert('⏳ Vui lòng chờ {$remain}s để gửi câu hỏi tiếp theo!'); history.back();</script>");
}

/* ===========================================================
   ✔️ NẾU ĐỦ ĐIỀU KIỆN → LƯU CÂU HỎI
   =========================================================== */
$stmt = $pdo->prepare("INSERT INTO hoi_dap(id_nguoi_hoi, id_chuyen_gia, cau_hoi, ngay_hoi) VALUES (?, ?, ?, NOW())");
$stmt->execute([$id_user, $id_chuyen_gia, $cau_hoi]);

/* ===========================================================
   🎁 CỘNG ĐIỂM + LƯU LỊCH SỬ
   =========================================================== */
$pdo->prepare("UPDATE khachhang SET so_diem = so_diem + 15 WHERE id_kh = ?")
    ->execute([$id_user]);

$pdo->prepare("
    INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
    VALUES (?, NULL, 15, 'dat_cau_hoi', NOW())
")->execute([$id_user]);

/* ===========================================================
   🔔 TẠO THÔNG BÁO CHO NGƯỜI HỎI
   =========================================================== */
$noi_dung_tb = "📩 Bạn đã gửi câu hỏi cho chuyên gia. Chờ phản hồi!";
$pdo->prepare("
    INSERT INTO thongbao (id_kh, noi_dung, created_at)
    VALUES (?, ?, NOW())
")->execute([$id_user, $noi_dung_tb]);

echo "<script>alert('🎉 Gửi câu hỏi thành công! Bạn được +15 điểm.'); window.location.href='expert_detail.php?id={$id_chuyen_gia}&sent=1';</script>";
exit;
?>
<?php
require_once "../php/db.php";

$limit = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("
    SELECT SQL_CALC_FOUND_ROWS 
        bv.ma_bai_viet,
        bv.tieu_de,
        bv.duong_dan,
        bv.noi_dung,
        bv.anh_bv,
        bv.ngay_dang,
        bv.luot_xem,
        cm.ten_chuyen_muc AS category,
        kh.ho_ten AS tac_gia
    FROM baiviet bv
    LEFT JOIN chuyenmuc cm ON bv.ma_chuyen_muc = cm.ma_chuyen_muc
    LEFT JOIN khachhang kh ON bv.id_kh = kh.id_kh
    WHERE bv.trang_thai = 'published'
      AND bv.danh_muc = 'LATEST POSTS'
    ORDER BY bv.ngay_dang DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = ceil($total / $limit);

echo json_encode([
    "posts" => $posts,
    "page" => $page,
    "totalPages" => $totalPages
], JSON_UNESCAPED_UNICODE);

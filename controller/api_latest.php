<?php
require_once "../php/db.php";

$limit = 6;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1)
    $page = 1;

$offset = ($page - 1) * $limit;

/* Lấy bài thuộc danh mục LATEST POSTS */
$stmt = $pdo->prepare("
    SELECT SQL_CALC_FOUND_ROWS bv.*, kh.ho_ten
    FROM baiviet bv
    LEFT JOIN khachhang kh ON bv.ma_tac_gia = kh.id_kh
    WHERE bv.trang_thai = 'published'
      AND bv.danh_muc = 'LATEST POSTS'
    ORDER BY bv.ngay_dang DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Đếm tổng số bài trong LATEST POSTS */
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$totalPages = ceil($total / $limit);

header('Content-Type: application/json');
echo json_encode([
    "posts" => $posts,
    "page" => $page,
    "totalPages" => $totalPages
]);
?>
<?php
session_start();
require_once "../php/db.php";

// 1. Tổng số bài viết
$total = $pdo->query("SELECT COUNT(*) FROM baiviet")->fetchColumn();

// 2. Theo trạng thái
$published = $pdo->query("SELECT COUNT(*) FROM baiviet WHERE trang_thai='published'")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM baiviet WHERE trang_thai='pending'")->fetchColumn();
$rejected = $pdo->query("SELECT COUNT(*) FROM baiviet WHERE trang_thai='rejected'")->fetchColumn();

// 3. Lượt xem
$views = $pdo->query("SELECT SUM(luot_xem) FROM baiviet")->fetchColumn() ?: 0;

// 4. Top tác giả
$topAuthors = $pdo->query("
    SELECT 
        kh.id_kh AS ma_tac_gia,
        kh.ho_ten,
        kh.avatar_url,
        COUNT(b.ma_bai_viet) AS total_posts
    FROM khachhang kh
    INNER JOIN baiviet b ON b.ma_tac_gia = kh.id_kh
    GROUP BY kh.id_kh, kh.ho_ten, kh.avatar_url
    ORDER BY total_posts DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// 5. Top bài viết xem nhiều nhất
$topPosts = $pdo->query("
    SELECT tieu_de, duong_dan, anh_bv, luot_xem
    FROM baiviet
    WHERE trang_thai='published'
    ORDER BY luot_xem DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>
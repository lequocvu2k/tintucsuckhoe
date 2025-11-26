<?php
session_start();
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['rating'])) {
    $id = (int) $_POST['id'];
    $rating = (int) $_POST['rating'];

    $stmt = $pdo->prepare("UPDATE hoi_dap SET danh_gia = :rating WHERE id = :id");
    $stmt->execute([
        ':rating' => $rating,
        ':id' => $id
    ]);

    $_SESSION['success'] = "🎉 Cảm ơn bạn đã đánh giá!";
}

header("Location: ../view/user.php?view=notifications");
exit;

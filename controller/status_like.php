<?php
session_start();
require '../php/db.php';

$id_kh = $_SESSION['user_id'];
$id_status = $_POST['id_status'];

// check đã like chưa
$check = $pdo->prepare("SELECT * FROM status_like WHERE id_status = ? AND id_kh = ?");
$check->execute([$id_status, $id_kh]);

if ($check->rowCount() == 0) {
    // thêm like
    $stmt = $pdo->prepare("INSERT INTO status_like (id_status, id_kh) VALUES (?, ?)");
    $stmt->execute([$id_status, $id_kh]);
    echo "liked";
} else {
    // bỏ like
    $stmt = $pdo->prepare("DELETE FROM status_like WHERE id_status = ? AND id_kh = ?");
    $stmt->execute([$id_status, $id_kh]);
    echo "unliked";
}

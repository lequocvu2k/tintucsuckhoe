<?php
require_once "../php/db.php";

$id = $_POST['id_kh'] ?? 0;

if ($id > 0) {
    $stmt = $pdo->prepare("
        UPDATE khachhang
        SET is_muted = 0, muted_until = NULL
        WHERE id_kh = ?
    ");
    $stmt->execute([$id]);
    echo "UNMUTED";
} else {
    echo "ERROR";
}

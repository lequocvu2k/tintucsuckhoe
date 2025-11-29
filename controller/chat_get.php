<?php
session_start();
require '../php/db.php';

$sql = "
    SELECT c.id, c.id_kh, c.message, c.created_at,
           kh.ho_ten,
           kh.avatar_url
    FROM chat_messages c
    JOIN khachhang kh ON kh.id_kh = c.id_kh
    ORDER BY c.created_at ASC
";
$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// build path avatar
foreach ($rows as &$r) {
    if (!empty($r['avatar_url'])) {
        $r['avatar_url'] = '/php/' . ltrim($r['avatar_url'], '/');
    } else {
        $r['avatar_url'] = '/php/img/avt.jpg';
    }
}
echo json_encode($rows);

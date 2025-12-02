<?php
session_start();
require '../php/db.php';

$sql = "
    SELECT 
        c.id, 
        c.id_kh, 
        c.message, 
        c.reply_to,
        c.created_at,
        kh.ho_ten,
        kh.avatar_url,

        parent.message AS reply_message,
        parent_kh.ho_ten AS reply_author

    FROM chat_messages c
    JOIN khachhang kh ON kh.id_kh = c.id_kh

    LEFT JOIN chat_messages parent ON parent.id = c.reply_to
    LEFT JOIN khachhang parent_kh ON parent_kh.id_kh = parent.id_kh

    ORDER BY c.created_at ASC
";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// build avatar path
foreach ($rows as &$r) {

    // avatar người gửi
    if (!empty($r['avatar_url'])) {
        $r['avatar_url'] = '/php/' . ltrim($r['avatar_url'], '/');
    } else {
        $r['avatar_url'] = '/img/avt.jpg';
    }

    // avatar của người được reply (nếu cần)
    if (!empty($r['reply_author'])) {
        // không cần avatar cha — chỉ cần text thôi
    }
}

echo json_encode($rows, JSON_UNESCAPED_UNICODE);
exit;

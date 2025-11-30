<?php
session_start();
require '../php/db.php';

$id_kh = $_SESSION['user_id'] ?? 0;

$sql = "
    SELECT 
        s.id,
        s.noi_dung,
        s.ngay_dang,
        kh.ho_ten,
        kh.avatar_url,
        kh.avatar_frame,
        (SELECT COUNT(*) FROM status_like WHERE id_status = s.id) AS total_like,
        (SELECT COUNT(*) FROM status_like WHERE id_status = s.id AND id_kh = ?) AS liked
    FROM status s
    JOIN khachhang kh ON kh.id_kh = s.id_kh
   WHERE s.ngay_dang >= NOW() - INTERVAL 24 HOUR   -- ⭐ CHỈ LẤY TRONG 24H
    ORDER BY s.ngay_dang DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_kh]);
$list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = [];

foreach ($list as $row) {

    /* AVATAR */
    if (!empty($row['avatar_url'])) {
        $row['avatar_url'] = "/php/uploads/avatars/" . $row['avatar_url'];
    } else {
        $row['avatar_url'] = "/img/avt.jpg";
    }

    /* FRAME */
    $row['avatar_frame_url'] = null;

    if (!empty($row['avatar_frame'])) {
        $possibleExt = ['gif', 'png', 'jpg', 'jpeg'];
        foreach ($possibleExt as $ext) {

            // File thật nằm ở: /frames/
            $serverPath = "../frames/" . $row['avatar_frame'] . "." . $ext;

            if (file_exists($serverPath)) {
                // Đường dẫn web CŨNG là /frames/
                $row['avatar_frame_url'] = "/frames/" . $row['avatar_frame'] . "." . $ext;
                break;
            }
        }
    }

    $output[] = $row;
}

echo json_encode($output);
?>
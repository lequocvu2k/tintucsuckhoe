<?php
// ====================== LẤY THÔNG TIN NGƯỜI DÙNG ======================
$user = null;
$tier = "Member";

if (isset($_SESSION['user_id'])) {
    $id_kh = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        SELECT kh.*, tk.ngay_tao
        FROM khachhang kh
        LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
        WHERE kh.id_kh = ?
    ");
    $stmt->execute([$id_kh]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        /* ============================
           AUTO UNMUTE KHI HẾT THỜI GIAN
        ============================ */
        /* ============================
    AUTO UNMUTE KHI HẾT THỜI GIAN
 ============================ */

        /* ============================
    AUTO UNMUTE KHI HẾT THỜI GIAN
 ============================ */

        if ($user['is_muted'] == 1 && !empty($user['muted_until'])) {

            $now = time();
            $end = strtotime($user['muted_until']);

            if ($end <= $now) {

                // Gỡ mute trong DB
                $pdo->prepare("
            UPDATE khachhang 
            SET is_muted = 0, muted_until = NULL 
            WHERE id_kh = ?
        ")->execute([$user['id_kh']]);

                // ⭐ Reload thông tin user từ DB
                $stmtReload = $pdo->prepare("
            SELECT kh.*, tk.ngay_tao
            FROM khachhang kh
            LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
            WHERE kh.id_kh = ?
        ");
                $stmtReload->execute([$id_kh]);
                $user = $stmtReload->fetch(PDO::FETCH_ASSOC);

                // ⭐ Xóa countdown + thông báo
                echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                let box = document.getElementById('muteBox');
                if (box) {
                    box.innerHTML = '<b style=\"color:#28a745\">🎉 Bạn đã được gỡ cấm chat!</b>';
                    box.style.background = '#e6ffe6';
                }
            });
        </script>";
            }
        }

        /* ============================
           TÍNH TIER NGƯỜI DÙNG
        ============================ */
        function xacDinhCapDo($so_diem)
        {
            if ($so_diem >= 10000)
                return 'Siêu Kim Cương';
            if ($so_diem >= 5000)
                return 'Kim Cương';
            if ($so_diem >= 1000)
                return 'Vàng';
            if ($so_diem >= 500)
                return 'Bạc';
            return 'Member';
        }

        $so_diem = is_numeric($user['so_diem']) ? $user['so_diem'] : 0;
        $tier = xacDinhCapDo($so_diem);
    }
}


?>
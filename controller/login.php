<?php
session_start();
require_once '../php/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Kiểm tra nhập thiếu
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = "❌ Vui lòng nhập đầy đủ thông tin!";
        header("Location: ../view/index.php");
        exit;
    }

    // Lấy thông tin tài khoản
    $stmt = $pdo->prepare("
        SELECT tk.id_tk, tk.username, tk.password, kh.id_kh, kh.is_banned
        FROM taotaikhoan tk
        JOIN khachhang kh ON tk.id_kh = kh.id_kh
        WHERE tk.username = ?
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // ❌ Nếu tài khoản bị BAN
        if ($user['is_banned'] == 1) {

            $contactLink = "http://localhost:3000/mail/formmail.php";

            $_SESSION['ban_message'] = 
                "⛔ Tài khoản của bạn đã bị BAN! ".
                "Vui lòng <a href='{$contactLink}' style='color:#007bff;font-weight:bold;'>liên hệ Admin</a> để mở khóa.";

            header("Location: ../view/index.php");
            exit;
        }

        // ✔ Kiểm tra mật khẩu (theo kiểu mật khẩu thuần)
        if ($password === $user['password']) {

            // Lưu session
            $_SESSION['user_id'] = $user['id_kh'];
            $_SESSION['username'] = $user['username'];

            // Ghi log đăng nhập
            $stmt = $pdo->prepare("
                INSERT INTO dangnhap (username, password, ngay_dn) 
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$username, $user['password']]);

            header("Location: ../view/index.php");
            exit;
        } else {
            $_SESSION['login_error'] = "❌ Sai mật khẩu!";
        }

    } else {
        $_SESSION['login_error'] = "❌ Không tìm thấy tài khoản!";
    }

    header("Location: ../view/index.php");
    exit;
}
?>

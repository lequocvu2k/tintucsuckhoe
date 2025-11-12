<?php
session_start();
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../php/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Lấy dữ liệu từ form
    $from = $_POST['to'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($from) || empty($subject) || empty($message)) {
        die("❌ Vui lòng điền đầy đủ thông tin.");
    }

    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8';

    try {
        // Cấu hình SMTP
        $mail->SMTPDebug = SMTP::DEBUG_OFF;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'vuliztva1@gmail.com';    // Gmail cố định
        $mail->Password = 'wufv pkus qmvp nisd';    // App Password Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('vuliztva1@gmail.com', 'Website AnniShop');
        $mail->addReplyTo($from, $from);
        $mail->addAddress('vuliztva1@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "📩 Email người gửi: <b>" . htmlspecialchars($from) . "</b><br><br>"
            . nl2br(htmlspecialchars($message));

        // Gửi email
        $mail->send();

        // Sau khi gửi mail thành công
        echo '
<style>
.video-bg {
    position: fixed;
    top: 0; left: 0;
    width: 100vw;
    height: 100vh;
    object-fit: cover;
    z-index: -1;
}
.message-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    padding: 0 20px;
    box-sizing: border-box;
    position: relative;
    z-index: 1;
}
.message {
    background-color: rgba(255, 255, 255, 0.85);
    padding: 35px 50px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    max-width: 420px;
    width: 100%;
    text-align: center;
    animation: fadeInUp 0.7s ease forwards;
    opacity: 0;
}
.message-text {
    font-size: 22px;
    font-weight: 600;
    color: #222;
    margin-bottom: 28px;
}
.message-button {
    display: inline-block;
    padding: 12px 38px;
    border: 2.5px solid #28a745;
    border-radius: 7px;
    color: #28a745;
    font-weight: 700;
    font-size: 17px;
    text-decoration: none;
    transition: all 0.3s ease;
    cursor: pointer;
}
.message-button:hover {
    background-color: #28a745;
    color: white;
    box-shadow: 0 4px 10px rgba(40,167,69,0.4);
}
@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(15px); }
    100% { opacity: 1; transform: translateY(0); }
}
</style>

<video autoplay muted loop class="video-bg">
    <source src="../video/background2.mp4" type="video/mp4">
    Your browser does not support the video tag.
</video>

<div class="message-wrapper">
    <div class="message">
        <div class="message-text">✅ Gửi mail thành công!</div>
        <a href="../php/index.php" class="message-button">Trở về Trang chủ</a>
    </div>
</div>';



        // --- Chèn dữ liệu vào bảng lienhe ---
        $id_kh = $_SESSION['user_id'] ?? null; // Nếu có đăng nhập
        $stmt = $pdo->prepare("INSERT INTO lienhe (id_kh, noidung, ngaygui) VALUES (?, ?, NOW())");
        $stmt->execute([$id_kh, "Email: $from\nTiêu đề: $subject\nNội dung: $message"]);

    } catch (Exception $e) {
        echo "❌ Lỗi gửi mail: {$mail->ErrorInfo}";
    }

} else {
    echo "⚠️ Yêu cầu không hợp lệ.";
}
?>
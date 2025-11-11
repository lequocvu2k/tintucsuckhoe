<?php
// Kết nối tới cơ sở dữ liệu
require_once './db.php'; // Đảm bảo đường dẫn đúng đến file kết nối CSDL của bạn

// Kiểm tra nếu có dữ liệu từ form gửi lên
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy các thông tin từ form
    $ho_ten = $_POST['ho_ten'] ?? '';
    $sdt = $_POST['sdt'] ?? '';
    $the_loai = $_POST['the_loai'] ?? '';

    // Kiểm tra nếu các trường bắt buộc không trống
    if (empty($ho_ten) || empty($sdt) || empty($the_loai)) {
        echo "<script>alert('Vui lòng điền đầy đủ thông tin yêu cầu.'); window.location.href = 'index.php';</script>";
        exit;
    }

    // Lấy id_kh từ session (giả sử người dùng đã đăng nhập)
    session_start();
    $id_kh = $_SESSION['user_id'] ?? null;

    // Kiểm tra nếu người dùng chưa đăng nhập
    if (!$id_kh) {
        echo "<script>alert('Vui lòng đăng nhập trước khi gửi yêu cầu.'); window.location.href = 'login.php';</script>";
        exit;
    }

    // Lưu thông tin vào bảng nhanvien_yc với trạng thái "chờ duyệt"
    try {
        // Thêm yêu cầu vào bảng nhanvien_yc với trạng thái "chờ duyệt"
        $stmt = $pdo->prepare("INSERT INTO nhanvien_yc (id_kh, ho_ten, sdt, the_loai, trang_thai) VALUES (?, ?, ?, ?, 'chờ duyệt')");
        $stmt->execute([$id_kh, $ho_ten, $sdt, $the_loai]);

        // Gửi thông báo thành công
        echo "<script>alert('Yêu cầu trở thành nhân viên đã được gửi thành công và đang chờ duyệt!'); window.location.href = 'user.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Đã có lỗi xảy ra. Vui lòng thử lại sau.'); window.location.href = 'user.php';</script>";
        error_log($e->getMessage());
    }
}
?>
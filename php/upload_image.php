<?php
// Kiểm tra xem tệp có được tải lên không
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "uploads/baiviet/";  // Thư mục lưu trữ ảnh

    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Tạo tên tệp duy nhất để tránh trùng lặp
    $fileName = uniqid() . "_" . basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Giới hạn kích thước tệp tải lên (ví dụ 2MB)
    $maxFileSize = 2 * 1024 * 1024;  // 2MB
    if ($_FILES['file']['size'] > $maxFileSize) {
        echo json_encode(['error' => 'Tệp quá lớn. Vui lòng tải lên tệp nhỏ hơn 2MB.']);
        exit;
    }

    // Cho phép chỉ tải lên ảnh
    if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        // Di chuyển tệp đến thư mục đích
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
            echo json_encode(['location' => $targetFile]);  // Trả về đường dẫn ảnh để TinyMCE chèn vào
        } else {
            echo json_encode(['error' => 'Lỗi khi tải ảnh lên.']);
        }
    } else {
        echo json_encode(['error' => 'Loại tệp không hợp lệ. Chỉ hỗ trợ tệp ảnh JPG, JPEG, PNG và GIF.']);
    }
} else {
    echo json_encode(['error' => 'Không có tệp nào được tải lên hoặc có lỗi khi tải lên.']);
}
?>
<?php
header('Content-Type: application/json');

// 📌 Thư mục lưu ảnh
$uploadDir = "../uploads/bio_images/";

// Tạo thư mục nếu chưa có
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Kiểm tra file upload
if (!isset($_FILES['upload']) || $_FILES['upload']['error'] !== 0) {
    echo json_encode([
        "error" => [
            "message" => "Không thể upload ảnh!"
        ]
    ]);
    exit;
}

$file = $_FILES['upload'];

// Lấy phần mở rộng
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

// Chỉ cho phép ảnh
$allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($ext, $allowed)) {
    echo json_encode([
        "error" => [
            "message" => "Chỉ cho phép upload ảnh (jpg, png, gif, webp)!"
        ]
    ]);
    exit;
}

// Đặt tên mới tránh trùng
$newName = time() . "_" . uniqid() . "." . $ext;
$targetPath = $uploadDir . $newName;

// Lưu file
if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode([
        "error" => [
            "message" => "Lưu ảnh thất bại!"
        ]
    ]);
    exit;
}

// 🔥 Trả kết quả đúng format CKEditor
echo json_encode([
    "url" => $targetPath
]);
exit;
?>
<?php
session_start();
session_unset();     // Xóa toàn bộ session
session_destroy();   // Hủy session
require_once '../php/db.php';

// Lưu thông báo vào session thay vì query string
$_SESSION["msg"] = "✅ Bạn đã đăng xuất thành công!";

header("Location: /view/index.php");
exit;

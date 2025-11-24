<?php
session_start();
require_once './db.php'; // file bạn đã có
$user_id = $_SESSION['user_id'] ?? null; // Đảm bảo user_id đã được lưu trong session
$members = [];
if (isset($_SESSION['user_id'])) {
    $id_kh = $_SESSION['user_id'];
    $stmt = $pdo->prepare("
        SELECT kh.*, tk.ngay_tao
        FROM khachhang kh
        LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
        WHERE kh.id_kh = :id
    ");
    $stmt->bindParam(':id', $id_kh);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        function tinhDiem($so_diem)
        {
            return floor($so_diem / 10000);
        }
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
try {
    // Lấy danh sách thành viên có vai_tro là QuanTri hoặc NhanVien
    $stmt = $pdo->query("
        SELECT ho_ten, vai_tro, avatar_url
        FROM khachhang
        WHERE vai_tro IN ('QuanTri', 'NhanVien')
        ORDER BY vai_tro DESC, ho_ten ASC
    ");
    $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Lỗi truy vấn: ' . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Giới thiệu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/about.css">
    <link rel="stylesheet" href="../css/menu.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/index.js"></script>
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <div class="about-hero" id="about">
        <h1>Về chúng tôi</h1>
        <p>
            “Tin tức Sức khỏe” là trang thông tin tổng hợp giúp bạn cập nhật kiến thức về tập luyện, dinh dưỡng,
            nghỉ ngơi và tinh thần — hướng đến một cuộc sống cân bằng và lành mạnh hơn.
        </p>
    </div>

    <main>
        <section id="mission">
            <h2>Tầm nhìn & Sứ mệnh</h2>
            <p><strong>Tầm nhìn:</strong> Trở thành nguồn tin cậy hàng đầu về sức khỏe, lan tỏa lối sống tích cực và
                khoa học.</p>
            <p><strong>Sứ mệnh:</strong> Cung cấp thông tin dễ hiểu, dễ áp dụng, mang lại giá trị thực tế cho mọi người.
            </p>
            <ul>
                <li>Đưa kiến thức y học đến gần với cộng đồng.</li>
                <li>Truyền cảm hứng về chăm sóc sức khỏe thể chất & tinh thần.</li>
                <li>Khuyến khích lối sống năng động, ăn uống lành mạnh.</li>
            </ul>
        </section>

        <section id="policy">
            <h2>Chính sách biên tập</h2>
            <p>
                Tất cả nội dung trên trang đều được biên soạn cẩn thận, đảm bảo tính trung thực, chính xác và dễ tiếp
                cận.
                Chúng tôi tuân thủ các nguyên tắc:
            </p>
            <ul>
                <li>Không đăng nội dung sai lệch hoặc thiếu nguồn gốc.</li>
                <li>Luôn ghi rõ nguồn tham khảo và ngày cập nhật.</li>
                <li>Không thay thế lời khuyên của bác sĩ chuyên khoa.</li>
            </ul>
        </section>

        <section id="team">
            <h2>Đội ngũ của chúng tôi</h2>
            <div class="team">
                <?php
                if (isset($error)) {
                    echo '<p style="color:red;">' . $error . '</p>';
                } elseif ($members) {

                    foreach ($members as $mem) {

                        // Avatar user
                        $avatar = (!empty($mem['avatar_url']) && file_exists($mem['avatar_url']))
                            ? htmlspecialchars($mem['avatar_url'])
                            : '../img/avt.jpg';

                        // Frame giống code bạn gửi
                        // Frame avatar (KHÔNG dùng $user, mà dùng $mem)
                        $frame = '';
                        if (!empty($mem['avatar_frame'])) {

                            $name = htmlspecialchars($mem['avatar_frame']); // vd: fire, ice, gold
                            $possibleExtensions = ['png', 'gif', 'jpg', 'jpeg'];

                            foreach ($possibleExtensions as $ext) {

                                $realPath = __DIR__ . '/../frames/' . $name . '.' . $ext;
                                $webPath = '../frames/' . $name . '.' . $ext;

                                // DEBUG — xem file có tồn tại không
                                if (!file_exists($realPath)) {
                                    // echo "<p style='color:red'>Không tìm thấy: $realPath</p>";
                                }

                                if (file_exists($realPath)) {
                                    $frame = $webPath;
                                    break;
                                }
                            }
                        }

                        // Role
                        $roleName = ($mem['vai_tro'] === 'QuanTri') ? 'Quản trị viên' : 'Nhân viên';
                        ?>

                        <div class="member">
                            <div class="avatar-container">
                                <!-- Avatar -->
                                <img src="<?= $avatar ?>" alt="Avatar" class="avatar">

                                <!-- Frame overlay -->
                                <?php if (!empty($frame)): ?>
                                    <img src="<?= $frame ?>" alt="Frame" class="frame-overlay">
                                <?php endif; ?>
                            </div>

                            <h4><?= htmlspecialchars($mem['ho_ten']) ?></h4>
                            <span><?= $roleName ?></span>
                        </div>

                        <?php
                    }

                } else {
                    echo '<p>Hiện chưa có thành viên nào trong đội ngũ.</p>';
                }
                ?>
            </div>
        </section>

    </main>

    <?php include '../partials/footer.php'; ?>

</body>

</html>
<?php
session_start();
require_once '../php/db.php';
/* ========== API Load + Sort Câu Hỏi ========== */
if (isset($_GET['api_questions']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $offset = (int) ($_GET['offset'] ?? 0);
    $sort = $_GET['sort'] ?? 'newest';

    switch ($sort) {
        case 'oldest':
            $order = "ORDER BY h.ngay_hoi ASC";
            break;
        case 'name_az':
            $order = "ORDER BY kh.ho_ten ASC";
            break;
        case 'name_za':
            $order = "ORDER BY kh.ho_ten DESC";
            break;
        default:
            $order = "ORDER BY h.ngay_hoi DESC";
            break;
    }

    $stmtQ = $pdo->prepare("
    SELECT 
        h.*, h.cau_tra_loi, h.ngay_tra_loi,

        -- Người hỏi
        kh.ho_ten, kh.avatar_url, kh.avatar_frame,

        -- Chuyên gia trả lời
        cg.ho_ten AS expert_name,
        cg.avatar_url AS expert_avatar,
        cg.avatar_frame AS expert_frame

    FROM hoi_dap h
    JOIN khachhang kh ON h.id_nguoi_hoi = kh.id_kh   -- người đặt câu hỏi
    JOIN khachhang cg ON h.id_chuyen_gia = cg.id_kh  -- chuyên gia

    WHERE h.id_chuyen_gia = ?
    $order
    LIMIT 5 OFFSET $offset
");

    $stmtQ->execute([$id]);
    
    echo json_encode($stmtQ->fetchAll(PDO::FETCH_ASSOC));
    exit;
}

$id_chuyen_gia = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id_chuyen_gia <= 0) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>⚠️ Chuyên gia không hợp lệ!</h2>";
    exit;
}

/* ====================== LẤY THÔNG TIN NGƯỜI DÙNG ĐĂNG NHẬP ====================== */
$user = null;
$tier = "Member";

$id_kh = $_SESSION['user_id'] ?? null; // người dùng đăng nhập

if ($id_kh) {
    $stmt = $pdo->prepare("
        SELECT kh.*, tk.ngay_tao
        FROM khachhang kh
        LEFT JOIN taotaikhoan tk ON kh.id_kh = tk.id_kh
        WHERE kh.id_kh = :id
    ");
    $stmt->execute([':id' => $id_kh]);
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

/* ====================== LẤY THÔNG TIN CHUYÊN GIA ====================== */
$stmt = $pdo->prepare("
    SELECT ho_ten, avatar_url, chuyen_mon, mo_ta_chuyen_gia, is_chuyen_gia
    FROM khachhang
    WHERE id_kh = ?
");
$stmt->execute([$id_chuyen_gia]);
$expert = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$expert || !$expert['is_chuyen_gia']) {
    echo "<h2 style='color:red;text-align:center;margin-top:50px;'>⚠️ Không tìm thấy chuyên gia hoặc chuyên gia không được hiển thị!</h2>";
    exit;
}

/* ====================== LẤY BÀI VIẾT CHUYÊN GIA ====================== */
$stmtPost = $pdo->prepare("
    SELECT ma_bai_viet, tieu_de, duong_dan, anh_bv, ngay_dang
    FROM baiviet
    WHERE id_kh = ? AND trang_thai = 'published'
    ORDER BY ngay_dang DESC
    LIMIT 20
");
$stmtPost->execute([$id_chuyen_gia]);
$posts = $stmtPost->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hồ sơ chuyên gia - <?= htmlspecialchars($expert['ho_ten']) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/expert_detail.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/popup.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/popup.js"></script>
    <script src="../js/post.js" defer></script>

</head>

<body data-id="<?= $id_chuyen_gia ?>">
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/login.php'; ?>
    <div class="expert-detail-wrapper">
        <!-- Thông tin chuyên gia -->
        <div class="expert-info-card">
            <?php if (isset($_GET['sent']) && $_GET['sent'] == 1): ?>
                <div class="alert-success">
                    🎉 <b>Bạn đã gửi câu hỏi thành công!</b> Vui lòng chờ chuyên gia trả lời.
                </div>
            <?php endif; ?>

            <div class="avatar">
                <img src="<?= htmlspecialchars($expert['avatar_url'] ?: './img/avt.jpg') ?>" alt="Avatar">
            </div>
            <h1><?= htmlspecialchars($expert['ho_ten'] ?: 'Chưa có tên') ?></h1>
            <?php if (!empty($expert['chuyen_mon'])): ?>
                <div class="expert-tag">Chuyên môn: <?= htmlspecialchars($expert['chuyen_mon']) ?></div>
            <?php endif; ?>

            <?php if (!empty($expert['mo_ta_chuyen_gia'])): ?>
                <p>Giới thiệu: <?= nl2br(htmlspecialchars($expert['mo_ta_chuyen_gia'])) ?></p>
            <?php else: ?>
                <p>Chuyên gia chưa cập nhật phần mô tả chi tiết.</p>
            <?php endif; ?>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="ask-box">
                    <h3>🗨️ Đặt câu hỏi cho chuyên gia</h3>
                    <form action="../controller/send_question.php" method="POST">
                        <input type="hidden" name="id_chuyen_gia" value="<?= $id_chuyen_gia ?>">
                        <textarea name="question" placeholder="Nhập câu hỏi của bạn về sức khỏe..." required></textarea>
                        <button type="submit" class="ask-btn">Gửi câu hỏi</button>
                    </form>
                </div>
            <?php else: ?>
                <p class="login-ask">⚠️ Vui lòng đăng nhập để đặt câu hỏi!</p>
            <?php endif; ?>

        </div>


        <!-- Danh sách bài viết -->
        <div class="expert-posts">
            <h2>Bài viết của chuyên gia</h2>
            <?php if (!$posts): ?>
                <p>Chuyên gia chưa có bài viết nào được hiển thị.</p>
            <?php else: ?>
                <?php foreach ($posts as $p): ?>
                    <div class="expert-post-item">
                        <?php if (!empty($p['anh_bv'])): ?>
                            <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                <img src="/php/<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                            </a>
                        <?php endif; ?>

                        <div>
                            <div class="expert-post-item-title">
                                <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                    <?= htmlspecialchars($p['tieu_de']) ?>
                                </a>
                            </div>
                            <div class="expert-post-meta">
                                Đăng ngày: <?= date("d/m/Y", strtotime($p['ngay_dang'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- ❓ Danh sách câu hỏi gửi cho chuyên gia -->
        <div class="question-list">
            <h3>💬 Câu hỏi từ cộng đồng</h3>

            <!-- Sắp xếp -->
            <div class="sort-question">
                <label>Sắp xếp: </label>
                <select id="sortQuestion">
                    <option value="newest">Mới nhất</option>
                    <option value="oldest">Cũ nhất</option>
                    <option value="name_az">Tên (A → Z)</option>
                    <option value="name_za">Tên (Z → A)</option>
                </select>
            </div>

            <div id="questionContainer"></div>
            <button class="load-more" id="loadMore" data-offset="0">🔽 Xem thêm</button>
        </div>

    </div>
    <script src="../js/expert_detail.js" defer></script>
    <?php include '../partials/footer.php'; ?>

</body>

</html>
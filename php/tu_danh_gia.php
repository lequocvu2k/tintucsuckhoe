<?php
session_start();
require_once './db.php'; // đường dẫn tới file kết nối DB của bạn
// ====================== LẤY THÔNG TIN NGƯỜI DÙNG ======================
$user = null; // Mặc định là khách
$tier = "Member";

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
// Biến lưu lỗi + kết quả
$errors = [];
$result = [];
$recommend_posts = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $height = isset($_POST['height']) ? floatval($_POST['height']) : 0; // cm
    $weight = isset($_POST['weight']) ? floatval($_POST['weight']) : 0; // kg
    $sleep = isset($_POST['sleep']) ? floatval($_POST['sleep']) : 0; // giờ / ngày
    $exercise = isset($_POST['exercise']) ? floatval($_POST['exercise']) : 0; // giờ / ngày
    $water = isset($_POST['water']) ? floatval($_POST['water']) : 0; // lít / ngày

    // Validate đơn giản
    if ($height <= 0)
        $errors[] = "Chiều cao không hợp lệ.";
    if ($weight <= 0)
        $errors[] = "Cân nặng không hợp lệ.";
    if ($sleep <= 0)
        $errors[] = "Số giờ ngủ không hợp lệ.";
    if ($exercise < 0)
        $errors[] = "Số giờ tập luyện không hợp lệ.";
    if ($water <= 0)
        $errors[] = "Lượng nước uống không hợp lệ.";

    if (empty($errors)) {
        // --- TÍNH BMI ---
        $height_m = $height / 100; // đổi cm -> m
        $bmi = $weight / ($height_m * $height_m);
        $bmi = round($bmi, 1);

        // Phân loại BMI theo chuẩn châu Á
        if ($bmi < 18.5) {
            $bmi_status = "Thiếu cân";
            $bmi_advice = "Bạn đang thiếu cân, nên tăng cường dinh dưỡng lành mạnh và bổ sung calo.";
        } elseif ($bmi < 23) {
            $bmi_status = "Bình thường";
            $bmi_advice = "Chỉ số BMI của bạn ở mức tốt, hãy duy trì chế độ ăn uống và tập luyện hiện tại.";
        } elseif ($bmi < 25) {
            $bmi_status = "Thừa cân";
            $bmi_advice = "Bạn hơi thừa cân, nên điều chỉnh ăn uống, hạn chế đồ ngọt và tăng vận động.";
        } else {
            $bmi_status = "Béo phì";
            $bmi_advice = "Bạn đang ở mức béo phì, nên xây dựng kế hoạch giảm cân lành mạnh và theo dõi sức khỏe.";
        }

        // --- GIẤC NGỦ ---
        // Khuyến nghị 7–9 tiếng
        if ($sleep < 6) {
            $sleep_status = "Thiếu ngủ";
            $sleep_advice = "Bạn đang ngủ khá ít, nên ngủ thêm để cơ thể hồi phục tốt hơn (7–9 tiếng/ngày).";
        } elseif ($sleep <= 9) {
            $sleep_status = "Tốt";
            $sleep_advice = "Thời lượng ngủ của bạn khá ổn. Hãy giữ thói quen này.";
        } else {
            $sleep_status = "Ngủ quá nhiều";
            $sleep_advice = "Bạn ngủ hơi nhiều, hãy cân đối lại để có thêm thời gian vận động và sinh hoạt.";
        }

        // --- TẬP LUYỆN ---
        // Gợi ý ~0.5–1h/ngày (30–60 phút)
        if ($exercise == 0) {
            $ex_status = "Hầu như không vận động";
            $ex_advice = "Bạn nên bắt đầu với những bài tập nhẹ 15–30 phút/ngày để cải thiện sức khỏe.";
        } elseif ($exercise < 0.5) {
            $ex_status = "Vận động ít";
            $ex_advice = "Bạn có vận động nhưng hơi ít. Thử tăng lên khoảng 30 phút/ngày nhé.";
        } elseif ($exercise <= 1.5) {
            $ex_status = "Tập luyện tốt";
            $ex_advice = "Thời lượng tập luyện của bạn khá ổn. Hãy duy trì đều đặn.";
        } else {
            $ex_status = "Tập luyện nhiều";
            $ex_advice = "Bạn tập khá nhiều, hãy chú ý nghỉ ngơi và tránh quá sức.";
        }

        // --- UỐNG NƯỚC ---
        // Gợi ý khoảng 30–35ml/kg => lít
        $water_recommend = round($weight * 0.035, 1); // lít / ngày (tương đối)
        if ($water < $water_recommend - 0.5) {
            $water_status = "Uống hơi ít nước";
            $water_advice = "Bạn nên uống khoảng {$water_recommend} lít nước/ngày để cơ thể hoạt động tốt.";
        } elseif ($water > $water_recommend + 0.8) {
            $water_status = "Uống khá nhiều nước";
            $water_advice = "Bạn uống hơi nhiều so với khuyến nghị, hãy uống rải đều trong ngày.";
        } else {
            $water_status = "Lượng nước tương đối tốt";
            $water_advice = "Lượng nước bạn uống khá ổn, hãy duy trì thói quen này.";
        }

        // Gộp kết quả để hiển thị
        $result = [
            'bmi' => $bmi,
            'bmi_status' => $bmi_status,
            'bmi_advice' => $bmi_advice,
            'sleep_status' => $sleep_status,
            'sleep_advice' => $sleep_advice,
            'ex_status' => $ex_status,
            'ex_advice' => $ex_advice,
            'water_status' => $water_status,
            'water_advice' => $water_advice,
            'water_recommend' => $water_recommend
        ];

        // ------------------------------------------------------------------
        // GỢI Ý BÀI VIẾT TƯƠNG ỨNG (ví dụ: dinh dưỡng, tập luyện, nghỉ ngơi)
        // ------------------------------------------------------------------
        $topics = [];

        // Dinh dưỡng
        if ($bmi_status === "Thiếu cân" || $bmi_status === "Thừa cân" || $bmi_status === "Béo phì") {
            $topics[] = "dinh dưỡng";
            $topics[] = "giảm cân";
            $topics[] = "tăng cân";
        }

        // Ngủ nghỉ
        if ($sleep_status !== "Tốt") {
            $topics[] = "giấc ngủ";
            $topics[] = "ngủ ngon";
        }

        // Tập luyện
        if ($ex_status !== "Tập luyện tốt") {
            $topics[] = "tập luyện";
            $topics[] = "bài tập";
        }

        // Uống nước
        if ($water_status !== "Lượng nước tương đối tốt") {
            $topics[] = "uống nước";
            $topics[] = "thói quen uống nước";
        }

        if (!empty($topics)) {
            // Xây chuỗi LIKE cho câu SQL
            // Ở đây mình giả sử bảng 'baiviet' có cột 'tieu_de', 'anh_bv', 'duong_dan', 'ngay_dang'
            // Nếu bạn có cột 'tag' hoặc 'chuyen_muc', có thể sửa lại cho chính xác hơn.
            $likeParts = [];
            $params = [];

            foreach ($topics as $t) {
                $likeParts[] = "tieu_de LIKE ?";
                $params[] = "%" . $t . "%";
            }

            $sql = "
                SELECT ma_bai_viet, tieu_de, duong_dan, anh_bv, ngay_dang
                FROM baiviet
                WHERE " . implode(" OR ", $likeParts) . "
                ORDER BY ngay_dang DESC
                LIMIT 6
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $recommend_posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tự đánh giá sức khỏe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/tu_danh_gia.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/popup.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>

</head>

<body>
    <!-- Nếu bạn có header/menu dùng chung, include ở đây -->
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/login.php'; ?>
    <div class="health-wrapper">
        <h1><i class="fa-solid fa-heart-pulse"></i> Tự đánh giá sức khỏe</h1>

        <p class="subtitle">Nhập thông tin cơ bản mỗi ngày để xem tình trạng hiện tại và nhận gợi ý bài viết phù hợp.
        </p>

        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $err): ?>
                    <div>• <?= htmlspecialchars($err) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="health-form">
            <div class="form-group">
                <label for="height">Chiều cao (cm)</label>
                <input type="number" step="0.1" min="80" max="250" id="height" name="height"
                    value="<?= isset($height) ? htmlspecialchars($height) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="weight">Cân nặng (kg)</label>
                <input type="number" step="0.1" min="20" max="300" id="weight" name="weight"
                    value="<?= isset($weight) ? htmlspecialchars($weight) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="sleep">Số giờ ngủ mỗi ngày</label>
                <input type="number" step="0.1" min="0" max="24" id="sleep" name="sleep"
                    value="<?= isset($sleep) ? htmlspecialchars($sleep) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="exercise">Số giờ tập luyện mỗi ngày</label>
                <input type="number" step="0.1" min="0" max="10" id="exercise" name="exercise"
                    value="<?= isset($exercise) ? htmlspecialchars($exercise) : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="water">Lượng nước uống (lít/ngày)</label>
                <input type="number" step="0.1" min="0" max="10" id="water" name="water"
                    value="<?= isset($water) ? htmlspecialchars($water) : '' ?>" required>
            </div>

            <div class="health-actions">
                <button type="submit">Đánh giá ngay</button>
            </div>
        </form>

        <?php if (!empty($result)): ?>
            <div class="reset-actions">
                <form method="POST">
                    <button type="submit" name="reset" class="reset-btn"><i class="fa-solid fa-rotate-left"></i> Xóa kết
                        quả</button>
                </form>
            </div>

            <h2>📊 Kết quả đánh giá</h2>

            <div class="result-grid">
                <!-- BMI -->
                <div class="result-card">
                    <h3>BMI & cân nặng</h3>
                    <div class="status">
                        BMI: <strong><?= $result['bmi'] ?></strong>
                        <?php
                        $badgeClass = 'mid';
                        if ($result['bmi_status'] === 'Bình thường')
                            $badgeClass = 'good';
                        elseif ($result['bmi_status'] === 'Béo phì')
                            $badgeClass = 'warn';
                        ?>
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($result['bmi_status']) ?>
                        </span>
                    </div>
                    <p><?= htmlspecialchars($result['bmi_advice']) ?></p>
                </div>

                <!-- Giấc ngủ -->
                <div class="result-card">
                    <h3>Giấc ngủ</h3>
                    <?php
                    $badgeClass = $result['sleep_status'] === 'Tốt' ? 'good' : 'warn';
                    ?>
                    <div class="status">
                        Trạng thái:
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($result['sleep_status']) ?>
                        </span>
                    </div>
                    <p><?= htmlspecialchars($result['sleep_advice']) ?></p>
                </div>

                <!-- Tập luyện -->
                <div class="result-card">
                    <h3>Tập luyện</h3>
                    <?php
                    $badgeClass = $result['ex_status'] === 'Tập luyện tốt' ? 'good' : 'mid';
                    ?>
                    <div class="status">
                        Trạng thái:
                        <span class="badge <?= $badgeClass ?>">
                            <?= htmlspecialchars($result['ex_status']) ?>
                        </span>
                    </div>
                    <p><?= htmlspecialchars($result['ex_advice']) ?></p>
                </div>

                <!-- Uống nước -->
                <div class="result-card">
                    <h3>Uống nước</h3>
                    <div class="status">
                        Trạng thái:
                        <span class="badge mid">
                            <?= htmlspecialchars($result['water_status']) ?>
                        </span>
                    </div>
                    <p><?= htmlspecialchars($result['water_advice']) ?></p>
                    <p style="margin-top:4px;font-size:12px;color:#6b7280;">
                        Gợi ý: khoảng <strong><?= $result['water_recommend'] ?> lít/ngày</strong> (tùy theo cân nặng và
                        hoạt động).
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($recommend_posts)): ?>
        <div class="recommend-section">
            <h2>📰 Bài viết đề xuất cho bạn</h2>
            <div class="recommend-list">
                <?php foreach ($recommend_posts as $p): ?>
                    <a class="recommend-item" href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                        <?php if (!empty($p['anh_bv'])): ?>
                            <img src="<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                        <?php endif; ?>
                        <div>
                            <h4><?= htmlspecialchars($p['tieu_de']) ?></h4>
                            <?php if (!empty($p['ngay_dang'])): ?>
                                <span class="date">
                                    <?= date("d/m/Y", strtotime($p['ngay_dang'])) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    </div>
    <?php include '../partials/footer.php'; ?>
</body>

</html>
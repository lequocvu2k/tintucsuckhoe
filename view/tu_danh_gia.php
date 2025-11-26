<?php
session_start();
require_once '../php/db.php'; // đường dẫn tới file kết nối DB của bạn
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
        // --- GỢI Ý DINH DƯỠNG THEO BMI ---
        switch ($bmi_status) {
            case "Thiếu cân":
                $nutrition_plan = "
            • Tăng lượng calo với thực phẩm lành mạnh: cơm, khoai, yến mạch, sữa, sữa chua, hạt.<br>
            • Ăn 3 bữa chính + 2 bữa phụ (9h và 15h).<br>
            • Ưu tiên thực phẩm giàu đạm: thịt, cá, trứng, đậu, sữa.<br>
            • Không bỏ bữa sáng.
        ";
                break;

            case "Bình thường":
                $nutrition_plan = "
            • Duy trì khẩu phần hiện tại nhưng ưu tiên đồ hấp, luộc.<br>
            • Hạn chế nước ngọt, chiên rán, đồ quá ngọt.<br>
            • Ăn đa dạng: rau xanh, trái cây, đạm tốt (thịt, cá, trứng, đậu).<br>
            • Uống đủ nước theo gợi ý bên dưới.
        ";
                break;

            case "Thừa cân":
                $nutrition_plan = "
            • Hạn chế nước ngọt, trà sữa, bánh kẹo, đồ chiên.<br>
            • Tăng rau, trái cây ít ngọt (bưởi, táo, dưa leo).<br>
            • Giảm cơm trắng, ưu tiên gạo lứt.<br>
            • Ăn chậm, nhai kỹ, không vừa ăn vừa xem điện thoại.
        ";
                break;

            case "Béo phì":
            default:
                $nutrition_plan = "
            • Giảm calo từ từ (không nhịn ăn cực đoan).<br>
            • Tránh thức ăn nhanh, chiên rán, đồ ngọt, nước có gas.<br>
            • Ăn nhiều chất xơ, chất béo tốt (cá, hạt, dầu oliu).<br>
            • Nếu có bệnh nền, nên tham khảo bác sĩ dinh dưỡng.
        ";
                break;
        }
        $result['nutrition_plan'] = $nutrition_plan;
        // Ngủ nghỉ
        if ($sleep_status !== "Tốt") {
            $topics[] = "giấc ngủ";
            $topics[] = "ngủ ngon";
        }
        // --- GỢI Ý NGỦ NGHỈ CHI TIẾT ---
        switch ($sleep_status) {
            case "Thiếu ngủ":
                $sleep_plan = "
            • Ngủ đủ từ 7–9 tiếng mỗi ngày.<br>
            • Ngủ trước 23h để chất lượng giấc ngủ tốt hơn.<br>
            • Hạn chế dùng điện thoại trước khi ngủ 30 phút.<br>
            • Không uống cà phê, trà, nước tăng lực sau 17h.
        ";
                break;

            case "Ngủ quá nhiều":
                $sleep_plan = "
            • Cố gắng dậy đúng giờ mỗi ngày, không ngủ nướng quá lâu.<br>
            • Tập thể dục nhẹ vào buổi sáng để tỉnh táo hơn.<br>
            • Tránh ngủ trưa quá 30 phút.<br>
            • Đi ngủ đúng giờ và tránh nằm xem điện thoại trên giường.
        ";
                break;

            default: // Tốt
                $sleep_plan = "
            • Duy trì thói quen ngủ đủ giấc từ 7–9 tiếng.<br>
            • Tránh thức khuya thường xuyên.<br>
            • Duy trì thời gian ngủ – dậy cố định mỗi ngày.
        ";
                break;
        }

        // thêm vào kết quả
        $result['sleep_plan'] = $sleep_plan;

        // Tập luyện
        if ($ex_status !== "Tập luyện tốt") {
            $topics[] = "tập luyện";
            $topics[] = "bài tập";
        }
        // --- GỢI Ý TẬP LUYỆN CHI TIẾT ---
        switch ($ex_status) {
            case "Hầu như không vận động":
                $exercise_plan = "
            • Bắt đầu với 10–15 phút đi bộ mỗi ngày.<br>
            • Tập các bài nhẹ: xoay khớp, kéo giãn, yoga cơ bản.<br>
            • Sau 1 tuần, tăng lên 20–30 phút mỗi ngày.<br>
            • Ưu tiên bài tập tại nhà: squat, plank, chống đẩy.
        ";
                break;

            case "Vận động ít":
                $exercise_plan = "
            • Tập 30 phút/ngày, ít nhất 4 ngày/tuần.<br>
            • Kết hợp đi bộ nhanh + bài tập nhẹ (plank, squat).<br>
            • Hạn chế ngồi lâu >1 giờ, đứng lên đi lại 3–5 phút.
        ";
                break;

            case "Tập luyện tốt":
                $exercise_plan = "
            • Duy trì 30–60 phút tập luyện mỗi ngày.<br>
            • Chọn bài tập đa dạng: cardio + sức mạnh + giãn cơ.<br>
            • Tránh tập quá sức, nhớ bổ sung nước và protein.
        ";
                break;

            case "Tập luyện nhiều":
            default:
                $exercise_plan = "
            • Giảm cường độ 1–2 ngày/tuần để tránh chấn thương.<br>
            • Ưu tiên bài giãn cơ, yoga, massage cơ.<br>
            • Bổ sung protein sau tập luyện.<br>
            • Nếu đau kéo dài, nên nghỉ ngơi và kiểm tra y tế.
        ";
                break;
        }

        $result['exercise_plan'] = $exercise_plan;

        // Uống nước
        if ($water_status !== "Lượng nước tương đối tốt") {
            $topics[] = "uống nước";
            $topics[] = "thói quen uống nước";
        }
        // --- GỢI Ý UỐNG NƯỚC CHI TIẾT ---
        switch ($water_status) {
            case "Uống hơi ít nước":
                $water_plan = "
            • Hãy uống nước đều trong ngày, không chờ khát mới uống.<br>
            • Mang theo bình nước để nhắc nhở bản thân uống đủ.<br>
            • Ưu tiên nước lọc, hạn chế nước ngọt và nước có gas.<br>
            • Nếu vận động nhiều, cần bổ sung thêm chất điện giải.
        ";
                break;

            case "Uống khá nhiều nước":
                $water_plan = "
            • Không nên uống quá nhiều trong thời gian ngắn.<br>
            • Uống từng ngụm nhỏ, chia đều cả ngày.<br>
            • Tránh uống quá 1 lít trong 1 giờ, dễ gây hạ natri máu.<br>
            • Nếu khát quá mức thường xuyên, nên kiểm tra sức khỏe.
        ";
                break;

            default: // Lượng nước tương đối tốt
                $water_plan = "
            • Tiếp tục duy trì lượng nước hiện tại.<br>
            • Uống nước ngay sau khi thức dậy buổi sáng.<br>
            • Ưu tiên nước lọc, hạn chế đồ uống có đường.<br>
            • Tăng lượng nước khi chơi thể thao hoặc trời nóng.
        ";
                break;
        }

        // Lưu vào kết quả
        $result['water_plan'] = $water_plan;

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
    <script src="../js/tu_danh_gia.js"></script>
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
                <?php if (!empty($result['nutrition_plan'])): ?>
                    <div class="nutrition-section">
                        <h2><i class="fa-solid fa-carrot"></i> Gợi ý chế độ dinh dưỡng</h2>
                        <p class="note">📌 Gợi ý tham khảo, không thay thế tư vấn chuyên môn.</p>
                        <div class="nutrition-box">
                            <?= $result['nutrition_plan']; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($result['plan3'])): ?>
                    <div class="workout-section">
                        <h2><i class="fa-solid fa-dumbbell"></i> Gợi ý lịch tập luyện</h2>
                        <p class="note">💡 Chọn số ngày để xem lịch tập gợi ý (chỉ mang tính tham khảo).</p>

                        <div class="workout-controls">
                            <label for="planDays">Chọn số ngày:</label>
                            <select id="planDays">
                                <option value="3">3 ngày</option>
                                <option value="7">7 ngày</option>
                            </select>
                        </div>

                        <ul class="workout-list" id="workoutList">
                            <?php foreach ($result['plan3'] as $line): ?>
                                <li><?= htmlspecialchars($line) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <script>
                        const plan3 = <?= json_encode($result['plan3'], JSON_UNESCAPED_UNICODE) ?>;
                        const plan7 = <?= json_encode($result['plan7'], JSON_UNESCAPED_UNICODE) ?>;

                        document.getElementById('planDays').addEventListener('change', function () {
                            const val = this.value;
                            const list = document.getElementById('workoutList');
                            list.innerHTML = '';
                            const data = (val === '7') ? plan7 : plan3;

                            data.forEach(line => {
                                const li = document.createElement('li');
                                li.textContent = line;
                                list.appendChild(li);
                            });
                        });
                    </script>
                <?php endif; ?>

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
                <?php if (!empty($result['sleep_plan'])): ?>
                    <div class="sleep-section">
                        <h2><i class="fa-solid fa-moon"></i> Gợi ý giấc ngủ</h2>
                        <p class="note">💡 Gợi ý giúp cải thiện chất lượng giấc ngủ.</p>
                        <div class="sleep-box">
                            <?= $result['sleep_plan']; ?>
                        </div>
                    </div>
                <?php endif; ?>

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
                <?php if (!empty($result['exercise_plan'])): ?>
                    <div class="exercise-section">
                        <h2><i class="fa-solid fa-dumbbell"></i> Gợi ý tập luyện</h2>
                        <p class="note">💡 Gợi ý giúp cải thiện sức khỏe vận động.</p>
                        <div class="exercise-box">
                            <?= $result['exercise_plan']; ?>
                        </div>
                    </div>
                <?php endif; ?>

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
                <?php if (!empty($result['water_plan'])): ?>
                    <div class="water-section">
                        <h2><i class="fa-solid fa-droplet"></i> Gợi ý uống nước</h2>
                        <p class="note">💡 Gợi ý theo cân nặng và thói quen hiện tại của bạn.</p>
                        <div class="water-box">
                            <?= $result['water_plan']; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
        <div class="ai-box">
            <h3><i class="fa-solid fa-robot"></i> Chat AI tư vấn sức khỏe</h3>
            <div class="ai-input">
                <input type="text" id="question" placeholder="Hỏi về BMI, dinh dưỡng, tập luyện...">
                <button onclick="askAI()">Hỏi AI</button>
            </div>
            <div id="reply"></div>
        </div>

    </div>

    <?php include '../partials/footer.php'; ?>
</body>

</html>
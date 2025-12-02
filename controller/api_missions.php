<?php
session_start();
require_once "../php/db.php";

$user_id = $_SESSION['user_id'] ?? 0;
if (!$user_id) {
    echo "<p style='color:#ff7878;text-align:center;'>Bạn cần đăng nhập để xem nhiệm vụ.</p>";
    exit;
}

$today = date("Y-m-d");

/* =======================
   Danh sách nhiệm vụ gốc
======================= */
$mission_pool = [
    1 => [
        "title" => "📖 Đọc 1 bài bất kỳ",
        "reward" => 20,
        "count_need" => 1,
        "sql" => "SELECT COUNT(*) FROM diemdoc WHERE id_kh = ? AND ngay_them >= CURDATE()"
    ],

    2 => [
        "title" => "🔥 Đọc 5 bài bất kỳ",
        "reward" => 40,
        "count_need" => 5,
        "sql" => "SELECT COUNT(*) FROM diemdoc WHERE id_kh = ? AND ngay_them >= CURDATE()"
    ],

    3 => [
        "title" => "📚 Đọc 10 bài bất kỳ",
        "reward" => 80,
        "count_need" => 10,
        "sql" => "SELECT COUNT(*) FROM diemdoc WHERE id_kh = ? AND ngay_them >= CURDATE()"
    ],

    4 => [
        "title" => "💬 Bình luận 1 bài viết",
        "reward" => 30,
        "count_need" => 1,
        "sql" => "SELECT COUNT(*) FROM binhluan WHERE id_kh = ? AND ngay_binhluan >= CURDATE()"
    ],

    5 => [
        "title" => "💬 Bình luận 3 bài viết",
        "reward" => 60,
        "count_need" => 3,
        "sql" => "SELECT COUNT(*) FROM binhluan WHERE id_kh = ? AND ngay_binhluan >= CURDATE()"
    ]
];

/* ============================
   1) Check xem hôm nay user đã random nhiệm vụ chưa
============================ */
$stmt = $pdo->prepare("SELECT mission_id, da_nhan FROM mission_daily WHERE id_kh = ? AND ngay = ?");
$stmt->execute([$user_id, $today]);
$today_missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($today_missions) == 0) {
    // Random 3 nhiệm vụ khác nhau
    $random_ids = array_rand($mission_pool, 3);

    $insert = $pdo->prepare("INSERT INTO mission_daily (id_kh, mission_id, ngay) VALUES (?, ?, ?)");

    foreach ($random_ids as $mid) {
        $insert->execute([$user_id, $mid, $today]);
    }

    // Load lại danh sách đã lưu
    $stmt->execute([$user_id, $today]);
    $today_missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ============================
   2) Hiển thị nhiệm vụ hôm nay
============================ */
foreach ($today_missions as $m):

    $mid = $m['mission_id'];
    $info = $mission_pool[$mid];

    // Đếm tiến độ thực tế
    $stmt2 = $pdo->prepare($info['sql']);
    $stmt2->execute([$user_id]);
    $count = (int) $stmt2->fetchColumn();

    // Tính tiến độ còn lại
    $remaining = max(0, $info['count_need'] - $count);

    $done = $count >= $info['count_need'];
    ?>
    <div class="mission-item">
        <div class="mission-title"><?= $info['title'] ?></div>

        <!-- ⭐ HIỂN THỊ TIẾN ĐỘ -->
        <p class="mission-progress">
            📌 Tiến độ:
            <b><?= $count ?></b> / <b><?= $info['count_need'] ?></b>
            <?php if (!$done): ?>
                — Còn <b><?= $remaining ?></b> nữa
            <?php endif; ?>
        </p>

        <div class="mission-reward">🎁 +<?= $info['reward'] ?> điểm</div>

        <?php if ($m['da_nhan'] == 1): ?>
            <button class="mission-btn-do" disabled style="opacity:.5;">Đã nhận thưởng ✔</button>

        <?php elseif ($done): ?>
            <form method="POST" action="../controller/mission_claim.php">
                <input type="hidden" name="mid" value="<?= $mid ?>">
                <button class="mission-btn-do">Nhận thưởng</button>
            </form>

        <?php else: ?>
            <button class="mission-btn-do" disabled style="opacity:.5;">Chưa hoàn thành</button>
        <?php endif; ?>
    </div>

<?php endforeach; ?>
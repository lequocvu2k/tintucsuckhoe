<?php
session_start();
require_once '../php/db.php'; // file bạn đã có
// Lấy thông tin user
$user_id = $_SESSION['user_id'] ?? null; // Đảm bảo user_id đã được lưu trong session
// --- Lấy thông tin tác giả ---
$stmt_author = $pdo->prepare("SELECT ho_ten, email, avatar_url, avatar_frame FROM khachhang WHERE id_kh = ?");
$stmt_author->execute([$user_id]);  // Sử dụng $user_id thay vì $post['id_kh']
$author = $stmt_author->fetch(PDO::FETCH_ASSOC);

// --- Gán mặc định để tránh lỗi ---
$author_name = $author && !empty($author['ho_ten']) ? htmlspecialchars($author['ho_ten']) : "Không rõ tác giả";
$author_email = $author && !empty($author['email']) ? htmlspecialchars($author['email']) : "";
$author_avatar = $author && !empty($author['avatar_url']) ? htmlspecialchars($author['avatar_url']) : "../img/avt.jpg";
$author_frame = $author && !empty($author['avatar_frame']) ? htmlspecialchars($author['avatar_frame']) : "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $ho_ten = trim($_POST["ho_ten"] ?? "");
    $email = trim($_POST["email"] ?? "");

    // Kiểm tra bắt buộc
    if ($username === "" || $password === "" || $confirm_password === "" || $ho_ten === "" || $email === "") {
        $_SESSION["signup_error"] = "❌ Vui lòng điền đầy đủ thông tin!";
        header("Location: index.php");
        exit;
    }

    if ($password !== $confirm_password) {
        $_SESSION["signup_error"] = "❌ Mật khẩu xác nhận không khớp!";
        header("Location: index.php");
        exit;
    }

    // Kiểm tra username đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT id_tk FROM taotaikhoan WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        $_SESSION["signup_error"] = "❌ Tên đăng nhập đã tồn tại!";
        header("Location: index.php");
        exit;
    }

    // Kiểm tra email đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT id_kh FROM khachhang WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $_SESSION["signup_error"] = "❌ Email đã được sử dụng!";
        header("Location: index.php");
        exit;
    }

    // Thêm khách hàng mới vào bảng khachhang trước
    $stmt = $pdo->prepare("INSERT INTO khachhang (ho_ten, email) VALUES (?, ?)");
    if (!$stmt->execute([$ho_ten, $email])) {
        $_SESSION["signup_error"] = "❌ Lỗi khi thêm khách hàng!";
        header("Location: index.php");
        exit;
    }

    // Lấy id_kh vừa tạo
    $id_kh = $pdo->lastInsertId();

    $hashedPassword = $password; // lưu mật khẩu chưa mã hóa (không khuyến nghị)

    // Thêm tài khoản vào taotaikhoan kèm id_kh làm khóa ngoại
    $stmt = $pdo->prepare("INSERT INTO taotaikhoan (username, password, id_kh) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $hashedPassword, $id_kh])) {
        $_SESSION["msg"] = "✅ Đăng ký thành công!";
        $_SESSION["username"] = $username;
    } else {
        $_SESSION["signup_error"] = "❌ Có lỗi xảy ra, vui lòng thử lại!";
    }

    header("Location: index.php");
    exit;
}

if ($user_id) {
    try {
        $stmt = $pdo->prepare("SELECT ho_ten, email, so_diem, dia_chi, sdt, avatar_url, avatar_frame, vai_tro FROM khachhang WHERE id_kh = ?");
        $stmt->execute([$user_id]);
        $fetchedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fetchedUser) {
            $user = $fetchedUser; // Gán dữ liệu thực tế vào biến $user
            $_SESSION['user_role'] = $user['vai_tro']; // Lưu vai trò vào session
        }
    } catch (PDOException $e) {
        die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
    }
}

function tinhDiem($so_diem)
{
    return floor($so_diem / 10000); // 1 điểm = 10.000đ
}

// Hàm xác định cấp độ
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

$so_diem = isset($user['so_diem']) && is_numeric($user['so_diem']) ? $user['so_diem'] : 0;

$diem = tinhDiem($so_diem);
$tier = xacDinhCapDo($so_diem);

// Editor’s Picks (3 bài thuộc danh mục EDITOR'S PICKS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = \"EDITOR'S PICKS\"
    ORDER BY ngay_dang DESC
    LIMIT 3
");

$editors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Main Highlights (6 bài thuộc danh mục HIGHLIGHT)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'MAIN HIGHLIGHTS'
    ORDER BY ngay_dang DESC
    LIMIT 6
");
$highlight = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Latest posts (8 bài thuộc danh mục LATEST POSTS)
/* ====================== LATEST POSTS — PHÂN TRANG ====================== */
$latestLimit = 6;
$latestPage = isset($_GET['lp']) ? max(1, intval($_GET['lp'])) : 1;
$latestOffset = ($latestPage - 1) * $latestLimit;

/* Đếm tổng số bài */
$stmt = $pdo->query("
    SELECT COUNT(*) 
    FROM baiviet
    WHERE trang_thai = 'published'
      AND danh_muc = 'LATEST POSTS'
");
$latestTotal = $stmt->fetchColumn();
$latestTotalPages = ceil($latestTotal / $latestLimit);

/* Lấy 6 bài theo trang */
$stmt = $pdo->prepare("
    SELECT * 
    FROM baiviet
    WHERE trang_thai = 'published'
      AND danh_muc = 'LATEST POSTS'
    ORDER BY ngay_dang DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $latestLimit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $latestOffset, PDO::PARAM_INT);
$stmt->execute();

$latest = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Popular posts (5 bài thuộc danh mục POPULAR POSTS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'POPULAR POSTS'
    ORDER BY ngay_dang DESC
    LIMIT 5
");
$popular = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ====================== RANKINGS ====================== */

/* --- TOP LIKE --- */
$stmt = $pdo->query("
    SELECT b.*, COUNT(l.id_like) AS total_likes
    FROM baiviet b
    LEFT JOIN likes l ON b.ma_bai_viet = l.ma_bai_viet
    WHERE b.trang_thai = 'published'
    GROUP BY b.ma_bai_viet
    ORDER BY total_likes DESC
    LIMIT 1
");
$topLike = $stmt->fetch(PDO::FETCH_ASSOC);

/* --- TOP COMMENT --- */
$stmt = $pdo->query("
    SELECT b.*, COUNT(c.id_binhluan) AS total_cmt
    FROM baiviet b
    LEFT JOIN binhluan c ON b.ma_bai_viet = c.ma_bai_viet
    WHERE b.trang_thai = 'published'
    GROUP BY b.ma_bai_viet
    HAVING total_cmt > 0
    ORDER BY total_cmt DESC
    LIMIT 1
");
$topComment = $stmt->fetch(PDO::FETCH_ASSOC);

/* --- TOP VIEW --- */
$stmt = $pdo->query("
    SELECT *
    FROM baiviet
    WHERE trang_thai = 'published'
    ORDER BY luot_xem DESC
    LIMIT 1
");
$topView = $stmt->fetch(PDO::FETCH_ASSOC);

/* Gộp lại */
$rankings = [
    "Top Like" => $topLike ?: null,
    "Top Comment" => $topComment ?: null,
    "Top View" => $topView ?: null
];


// Interviews (3 bài thuộc danh mục INTERVIEWS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'INTERVIEWS'
    ORDER BY ngay_dang DESC
    LIMIT 3
");
$interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recommendations (3 bài thuộc danh mục RECOMMENDATIONS)
$stmt = $pdo->query("
    SELECT * FROM baiviet
    WHERE trang_thai = 'published' 
      AND danh_muc = 'RECOMMENDATIONS'
    ORDER BY ngay_dang DESC
    LIMIT 3
");
$recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tin tức sức khỏe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/index.css">
    <link rel="stylesheet" href="../css/menu.css">
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>

</head>

<body>
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/login.php'; ?>
    <main class="container">
        <div class="top-grid">
            <!-- LEFT: Editor's Picks -->
            <section class="editors">
                <h2>EDITOR'S PICKS</h2>

                <?php foreach ($editors as $e): ?>

                    <?php
                    // 🔍 Lấy tên tác giả đúng theo id_kh trong bảng baiviet
                    $stmtAuthor = $pdo->prepare("
            SELECT ho_ten 
            FROM khachhang 
            WHERE id_kh = ?
            LIMIT 1
        ");
                    $stmtAuthor->execute([$e['id_kh']]);
                    $postAuthor = $stmtAuthor->fetchColumn() ?: "Unknown Author";
                    ?>

                    <div class="editor-item">
                        <a href="./post.php?slug=<?= urlencode($e['duong_dan'] ?? '') ?>">
                            <img src="/php/<?= htmlspecialchars($e['anh_bv'] ?? '') ?>" alt="">
                            <div class="editor-info">
                                <h3><?= htmlspecialchars($e['tieu_de'] ?? 'No Title') ?></h3>

                                <div class="author-date">
                                    <span>By <b><?= htmlspecialchars($postAuthor) ?></b></span> •
                                    <span><?= date("F d, Y", strtotime($e['ngay_dang'])) ?></span>
                                </div>
                            </div>
                        </a>
                    </div>

                <?php endforeach; ?>
            </section>


            <!-- RIGHT: Main Highlights -->
            <section class="highlights">
                <div class="slider-container">
                    <div class="slider">
                        <?php
                        // Chia $highlight thành nhóm 4 bài / slide
                        $chunks = array_chunk($highlight, 4);
                        foreach ($chunks as $group): ?>
                            <div class="slide">
                                <div class="slide-grid">
                                    <?php foreach ($group as $h): ?>
                                        <div class="slide-item">
                                            <a href="./post.php?slug=<?= urlencode($h['duong_dan']) ?>">
                                                <img src="/php/<?= htmlspecialchars($h['anh_bv']) ?>" alt="">
                                                <div class="overlay">
                                                    <h3><?= htmlspecialchars($h['tieu_de']) ?></h3>
                                                </div>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button class="prev">&#10094;</button>
                    <button class="next">&#10095;</button>
                </div>
            </section>

        </div>

        <!-- Bottom Section -->
        <div class="bottom-section">

            <section class="latest">

                <h2>LATEST POSTS</h2>

                <div id="latest-grid" class="latest-grid"> </div>

                <div class="pagination-minimal">
                    <a id="btnPrev" class="pag-btn">‹ NEWER POSTS</a>
                    <span class="separator">/</span>
                    <a id="btnNext" class="pag-btn">OLDER POSTS ›</a>
                </div>
            </section>


            <aside class="popular">
                <section class="latest">
                    <h2>POPULAR POSTS</h2>
                    <ul>
                        <?php foreach ($popular as $p): ?>

                            <?php
                            // 🔍 Lấy tên tác giả đúng của bài viết
                            $stmtAuthor = $pdo->prepare("
                    SELECT ho_ten 
                    FROM khachhang 
                    WHERE id_kh = ?
                    LIMIT 1
                ");
                            $stmtAuthor->execute([$p['id_kh']]);
                            $postAuthor = $stmtAuthor->fetchColumn() ?: "Unknown Author";
                            ?>

                            <li>
                                <a href="./post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                    <img src="/php/<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                                    <div>
                                        <p class="post-title"><?= htmlspecialchars($p['tieu_de']) ?></p>

                                        <p class="author-date">
                                            <span>By <b><?= htmlspecialchars($postAuthor) ?></b></span> •
                                            <span><?= date("F d, Y", strtotime($p['ngay_dang'])) ?></span>
                                        </p>
                                    </div>
                                </a>
                            </li>

                        <?php endforeach; ?>
                    </ul>
                </section>
            </aside>

        </div>
        <div class="triple-section">
            <!-- Rankings -->
            <section class="rankings">
                <h2>RANKINGS</h2>

                <?php foreach ($rankings as $label => $item): ?>
                    <div class="post-item<?= $item ? '' : ' empty' ?>">
                        <?php if ($item): ?>
                            <?php
                            // Lấy tên tác giả
                            $stmtAuthor = $pdo->prepare("SELECT ho_ten FROM khachhang WHERE id_kh = ? LIMIT 1");
                            $stmtAuthor->execute([$item['id_kh']]);
                            $authorName = $stmtAuthor->fetchColumn() ?: "Unknown Author";
                            ?>

                            <!-- ẢNH THUMB TRÁI -->
                            <a href="./post.php?slug=<?= urlencode($item['duong_dan']) ?>" class="thumb-link">
                                <img src="/php/<?= htmlspecialchars($item['anh_bv']) ?>" alt="">
                            </a>

                            <!-- NỘI DUNG PHẢI -->
                            <div class="post-body">
                                <a href="./post.php?slug=<?= urlencode($item['duong_dan']) ?>" class="rank-tag">
                                    Rankings • <?= htmlspecialchars($label) ?>
                                </a>
                                <a href="./post.php?slug=<?= urlencode($item['duong_dan']) ?>" class="post-title-link">
                                    <h3><?= htmlspecialchars($item['tieu_de']) ?></h3>
                                </a>
                                <p class="meta">
                                    by <b><?= htmlspecialchars($authorName) ?></b> |

                                    <?= date("F d, Y", strtotime($item['ngay_dang'])) ?>
                                </p>
                            </div>

                        <?php else: ?>
                            <!-- CARD KHI CHƯA CÓ DỮ LIỆU -->
                            <div class="post-body">
                                <p class="rank-tag">Rankings • <?= htmlspecialchars($label) ?></p>
                                <p class="meta">Chưa có dữ liệu</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </section>
            <section class="interviews">
                <h2>INTERVIEWS</h2>

                <?php foreach ($interviews as $i): ?>

                    <?php
                    // Lấy tên tác giả
                    $stmtAuthor = $pdo->prepare("SELECT ho_ten FROM khachhang WHERE id_kh = ? LIMIT 1");
                    $stmtAuthor->execute([$i['id_kh']]);
                    $authorName = $stmtAuthor->fetchColumn() ?: "Unknown";
                    ?>

                    <div class="interview-item">
                        <a href="./post.php?slug=<?= urlencode($i['duong_dan']) ?>" class="thumb">
                            <img src="/php/<?= htmlspecialchars($i['anh_bv']) ?>" alt="">
                        </a>

                        <div class="info">
                            <div class="tags">
                                <span class="tag">Interview</span>
                            </div>

                            <a href="./post.php?slug=<?= urlencode($i['duong_dan']) ?>" class="title">
                                <?= htmlspecialchars($i['tieu_de']) ?>
                            </a>

                            <p class="meta">
                                by <b><?= htmlspecialchars($authorName) ?></b> |
                                <?= date("F d, Y", strtotime($i['ngay_dang'])) ?>
                            </p>
                        </div>
                    </div>

                <?php endforeach; ?>
            </section>

            <!-- Recommendations -->
            <section class="recommendations">
                <h2>RECOMMENDATIONS</h2>
                <?php foreach ($recommendations as $rec): ?>
                    <div class="post-item">
                        <a href="./post.php?slug=<?= urlencode($r['duong_dan']) ?>" class="post-link" class="title">
                            <img src="/php/<?= htmlspecialchars($r['anh_bv']) ?>" alt="">
                            <div class="post-info">
                                <h3><?= htmlspecialchars($r['tieu_de']) ?></h3>
                            </div>
                        </a>
                        </h3>
                        <p class="meta">by <?= htmlspecialchars($rec['tac_gia']) ?> |
                            <?= date("F d, Y", strtotime($rec['ngay_dang'])) ?>
                        </p>
                    </div>
            </div>
        <?php endforeach; ?>
        </section>
        </div>
    </main>
    <script src="../js/index.js"></script>
    <script>

        document.addEventListener("DOMContentLoaded", function () {
            const slider = document.querySelector(".slider");
            const slides = document.querySelectorAll(".slide");
            const prevBtn = document.querySelector(".prev");
            const nextBtn = document.querySelector(".next");

            let index = 0;

            function showSlide(i) {
                index = (i + slides.length) % slides.length;
                slider.style.transform = `translateX(${-index * 100}%)`;
            }

            nextBtn.addEventListener("click", () => {
                showSlide(index + 1);
            });

            prevBtn.addEventListener("click", () => {
                showSlide(index - 1);
            });
        });

    </script>
    <?php include '../partials/footer.php'; ?>

</body>

</html>
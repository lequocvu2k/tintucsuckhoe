<?php
session_start();
require_once '../php/db.php';

function formatDateVN($dateString)
{
    if (!$dateString)
        return "";

    $ts = strtotime($dateString);

    $map = [
        "January" => "Tháng 1",
        "February" => "Tháng 2",
        "March" => "Tháng 3",
        "April" => "Tháng 4",
        "May" => "Tháng 5",
        "June" => "Tháng 6",
        "July" => "Tháng 7",
        "August" => "Tháng 8",
        "September" => "Tháng 9",
        "October" => "Tháng 10",
        "November" => "Tháng 11",
        "December" => "Tháng 12"
    ];

    $thang_en = date("F", $ts);
    $thang_vi = $map[$thang_en];

    return date("d ", $ts) . $thang_vi . date(", Y", $ts);
}


// --- Lấy slug --- 
$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    die("<h2 style='text-align:center;color:red;'>❌ Không tìm thấy bài viết!</h2>");
}

include '../partials/menu.php';
// --- Lấy bài viết theo slug ---
$stmt = $pdo->prepare("SELECT * FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die("<h2 style='text-align:center;color:red;'>❌ Bài viết không tồn tại hoặc đã bị ẩn!</h2>");
}

// --- Cập nhật lượt xem ---
$pdo->prepare("UPDATE baiviet SET luot_xem = luot_xem + 1 WHERE ma_bai_viet = ?")
    ->execute([$post['ma_bai_viet']]);

if (isset($_SESSION['user_id'])) {
    $id_kh = $_SESSION['user_id'];
    $ma_bai_viet = $post['ma_bai_viet'];

    // Kiểm tra nếu người dùng đã đọc bài trong vòng 24 giờ chưa
    $check = $pdo->prepare("
    SELECT COUNT(*) 
    FROM diemdoc 
    WHERE id_kh = :id_kh 
      AND ma_bai_viet = :ma_bai_viet 
      AND loai_giao_dich = 'xem_bai' 
      AND ngay_them >= NOW() - INTERVAL 1 DAY
");
    $check->execute(['id_kh' => $id_kh, 'ma_bai_viet' => $post['ma_bai_viet']]);
    $already_added = $check->fetchColumn();
    // Nếu chưa đọc trong 24 giờ, cộng điểm và ghi lại
    if ($already_added == 0) {
        // Cộng điểm cho người dùng
        $length = strlen(strip_tags($post['noi_dung'])); // độ dài thực tế (không tính HTML)

        if ($length < 1000) {
            $points_to_add = 50; // bài ngắn
        } elseif ($length < 3000) {
            $points_to_add = 100; // trung bình
        } elseif ($length < 6000) {
            $points_to_add = 200; // dài
        } else {
            $points_to_add = 400; // rất dài
        }

        // Cập nhật điểm trong bảng khachhang
        $stmt_update = $pdo->prepare("
        UPDATE khachhang 
        SET so_diem = so_diem + :diem 
        WHERE id_kh = :id_kh
    ");
        $stmt_update->execute(['diem' => $points_to_add, 'id_kh' => $id_kh]);

        // Ghi lại lịch sử cộng điểm
        $stmt_log = $pdo->prepare("
        INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
        VALUES (:id_kh, :ma_bai_viet, :diem_cong, 'xem_bai', NOW())
    ");
        $stmt_log->execute([
            'id_kh' => $id_kh,
            'ma_bai_viet' => $post['ma_bai_viet'],
            'diem_cong' => $points_to_add
        ]);

        // Thông báo cộng điểm
        echo "
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const popup = document.createElement('div');
            popup.textContent = '+{$points_to_add} điểm!';
            popup.style.position = 'fixed';
            popup.style.bottom = '80px';
            popup.style.right = '30px';
            popup.style.background = 'rgba(0, 200, 0, 0.9)';
            popup.style.color = '#fff';
            popup.style.padding = '10px 20px';
            popup.style.borderRadius = '10px';
            popup.style.fontWeight = 'bold';
            popup.style.fontSize = '18px';
            popup.style.zIndex = '9999';
            popup.style.boxShadow = '0 0 10px rgba(0,0,0,0.3)';
            popup.style.transition = 'all 0.5s ease';
            document.body.appendChild(popup);
            setTimeout(() => { popup.style.opacity = '0'; popup.style.transform = 'translateY(-50px)'; }, 2000);
            setTimeout(() => { popup.remove(); }, 2500);
        });
    </script>
    ";
    }
    if ($check->rowCount() == 0) {
        // Ghi lại lịch sử xem bài viết
        $insert = $pdo->prepare("
            INSERT INTO diemdoc (id_kh, ma_bai_viet, diem_cong, loai_giao_dich, ngay_them)
            VALUES (?, ?, 0, 'xem_bai', NOW())
        ");
        $insert->execute([$id_kh, $ma_bai_viet]);
    }
}

// --- Lấy thông tin tác giả ---
$stmt_author = $pdo->prepare("
    SELECT ho_ten, email, avatar_url, avatar_frame, bio, vai_tro
    FROM khachhang
    WHERE id_kh = ?
");

$stmt_author->execute([$post['id_kh']]);
$author = $stmt_author->fetch(PDO::FETCH_ASSOC);

// --- Gán mặc định để tránh lỗi ---
$author_name = $author && !empty($author['ho_ten']) ? htmlspecialchars($author['ho_ten']) : "Không rõ tác giả";
$author_email = $author && !empty($author['email']) ? htmlspecialchars($author['email']) : "";
$author_avatar = $author && !empty($author['avatar_url']) ? htmlspecialchars($author['avatar_url']) : "../img/avt.jpg";
$author_frame = $author && !empty($author['avatar_frame']) ? htmlspecialchars($author['avatar_frame']) : "";
$author_bio = !empty($author['bio'])
    ? nl2br(htmlspecialchars($author['bio']))
    : "Tác giả chưa có mô tả.";

$author_role = $author['vai_tro'] ?? "user";
// --- Lấy bài phổ biến ---
$stmt = $pdo->query("SELECT * FROM baiviet WHERE trang_thai='published' AND danh_muc='POPULAR POSTS' ORDER BY ngay_dang DESC LIMIT 5");
$popular = $stmt->fetchAll(PDO::FETCH_ASSOC);

// --- Lấy bài trước & tiếp theo ---
// Lấy bài trước
$stmt_prev = $pdo->prepare("SELECT * FROM baiviet WHERE ngay_dang < ? AND trang_thai='published' ORDER BY ngay_dang DESC LIMIT 1");
$stmt_prev->execute([$post['ngay_dang']]);
$prev_post = $stmt_prev->fetch(PDO::FETCH_ASSOC);

// Lấy bài tiếp theo
$stmt_next = $pdo->prepare("SELECT * FROM baiviet WHERE ngay_dang > ? AND trang_thai='published' ORDER BY ngay_dang ASC LIMIT 1");
$stmt_next->execute([$post['ngay_dang']]);
$next_post = $stmt_next->fetch(PDO::FETCH_ASSOC);

// --- Lấy bình luận ---
$orderBy = "ORDER BY c.ngay_binhluan DESC";
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'oldest':
            $orderBy = "ORDER BY c.ngay_binhluan ASC";
            break;
        case 'name_asc':
            $orderBy = "ORDER BY kh.ho_ten ASC";
            break;
        case 'name_desc':
            $orderBy = "ORDER BY kh.ho_ten DESC";
            break;
    }
}
$stmt_comments = $pdo->prepare("
    SELECT c.*, kh.ho_ten, kh.avatar_url, kh.avatar_frame 
    FROM binhluan c
    JOIN khachhang kh ON c.id_kh = kh.id_kh
    WHERE c.ma_bai_viet = ? $orderBy
");
$stmt_comments->execute([$post['ma_bai_viet']]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("
    SELECT b.*, c.ten_chuyen_muc
    FROM baiviet b
    LEFT JOIN chuyenmuc c ON b.ma_chuyen_muc = c.ma_chuyen_muc
    WHERE b.duong_dan = ? AND b.trang_thai = 'published'
");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
$ma_dm = $post['ma_chuyen_muc'];

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($post['tieu_de']) ?> - Tin tức sức khỏe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/fw.css">
    <link rel="stylesheet" href="../css/post.css">
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/popup.css">
    <?php include '../partials/logo.php'; ?>
    <script src="../resources/js/anime.min.js"></script>
    <link rel="stylesheet" href="../resources/css/fontawesome/css/all.min.css">
    <script src="../js/fireworks.js" async defer></script>
    <script src="../js/menu.js" defer></script>
    <script src="../js/popup.js"></script>
    <script src="../js/post.js" defer></script>
</head>

<body>
    <?php include '../partials/header.php'; ?>
    <?php include '../partials/login.php'; ?>

    <main class="post-container">
        <!-- Cột trái: bài viết -->
        <article class="post-content">

            <h1><?= htmlspecialchars($post['tieu_de']) ?></h1>

            <?php if (!empty($post['ten_chuyen_muc'])): ?>
                <div class="post-tags">
                    <span class="tag-item"><?= htmlspecialchars($post['ten_chuyen_muc']) ?></span>
                </div>
            <?php endif; ?>
            <br>
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="../controller/save_post.php">
                    <input type="hidden" name="ma_bai_viet" value="<?= $post['ma_bai_viet'] ?>">
                    <input type="hidden" name="slug" value="<?= htmlspecialchars($slug) ?>">

                    <?php
                    // Kiểm tra đã lưu chưa
                    $checkSaved = $pdo->prepare("SELECT COUNT(*) FROM saved_posts WHERE id_kh = ? AND ma_bai_viet = ?");
                    $checkSaved->execute([$_SESSION['user_id'], $post['ma_bai_viet']]);
                    $isSaved = $checkSaved->fetchColumn() > 0;
                    ?>

                    <button type="submit" class="save-btn">
                        <?php if ($isSaved): ?>
                            <i class="fa-solid fa-bookmark" style="color:#066a49"></i> Đã lưu
                        <?php else: ?>
                            <i class="fa-regular fa-bookmark"></i> Lưu bài viết
                        <?php endif; ?>
                    </button>
                </form>
            <?php endif; ?>

            <p><i class="fas fa-eye"></i> <?= $post['luot_xem'] ?> lượt xem</p>
            <?php
            // Đếm tổng like
            $stmt_likes = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE ma_bai_viet=?");
            $stmt_likes->execute([$post['ma_bai_viet']]);
            $totalLikes = $stmt_likes->fetchColumn();

            // Kiểm tra người dùng đã like chưa
            $liked = false;
            if (isset($_SESSION['user_id'])) {
                $checkLike = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE id_kh=? AND ma_bai_viet=?");
                $checkLike->execute([$_SESSION['user_id'], $post['ma_bai_viet']]);
                $liked = $checkLike->fetchColumn() > 0;
            }
            ?>

            <button class="like-btn" id="likeBtn" onclick="likePost(<?= $post['ma_bai_viet'] ?>)" <?= $liked ? 'disabled' : '' ?>>
                <i class="fa-solid fa-heart" style="color:<?= $liked ? '#ff004c' : '#888' ?>;"></i>
                <span id="likeCount"><?= $totalLikes ?></span> Thích
            </button>

            <!-- Thông tin bài viết -->
            <div class="post-meta">
                <span>By <?= $author_name ?></span> •
                <span><?= date("d/m/Y", strtotime($post['ngay_dang'])) ?></span>

            </div>

            <?php if (!empty($post['anh_bv'])): ?>
                <img src="/php/<?= htmlspecialchars($post['anh_bv']) ?>" alt="Ảnh bài viết" class="main-image">
            <?php endif; ?>

            <div class="post-body">
                <?= nl2br($post['noi_dung']) ?>
            </div>

            <div class="user-info">
                <div class="user-row">

                    <!-- AVATAR + FRAME (TRÁI) -->
                    <div class="avatar-container">
                        <img src="<?= $author_avatar ?>" alt="Avatar" class="avatar">

                        <?php
                        $framePath = "";
                        if (!empty($author_frame)) {
                            $extensions = ['png', 'gif', 'jpg', 'jpeg'];
                            foreach ($extensions as $ext) {
                                $path = "../frames/" . $author_frame . "." . $ext;
                                if (file_exists($path)) {
                                    $framePath = $path;
                                    break;
                                }
                            }
                        }
                        ?>

                        <?php if (!empty($framePath)): ?>
                            <img src="<?= $framePath ?>" class="frame-overlay">
                        <?php endif; ?>
                    </div>

                    <!-- TÊN + BIO (PHẢI) -->
                    <div class="user-text">

                        <!-- TÊN TÁC GIẢ -->
                        <div class="author-name">
                            <strong><?= htmlspecialchars($author_name) ?></strong>
                        </div>
                        <br>
                        <!-- BIO -->
                        <div class="author-bio">
                            <?= $author_bio ?>
                        </div>


                    </div>
                </div>
                <br>
                <hr>
                <!-- Hiển thị Bài trước và Bài tiếp theo -->
                <div class="post-navigation">
                    <?php if ($prev_post): ?>
                        <a href="post.php?slug=<?= urlencode($prev_post['duong_dan']) ?>" class="prev-post">Bài trước:
                            <?= htmlspecialchars($prev_post['tieu_de']) ?></a>
                    <?php else: ?>
                        <span class="no-prev">❌ Không có bài trước</span>
                    <?php endif; ?>

                    <?php if ($next_post): ?>
                        <a href="post.php?slug=<?= urlencode($next_post['duong_dan']) ?>" class="next-post">Bài tiếp theo:
                            <?= htmlspecialchars($next_post['tieu_de']) ?></a>
                    <?php else: ?>
                        <span class="no-next">❌ Không có bài tiếp theo</span>
                    <?php endif; ?>
                </div>
                <section class="related-posts">
                    <h2>BẠN CÓ THỂ THÍCH</h2>
                    <div class="related-grid">
                        <?php
                        // Lấy các bài viết liên quan theo danh mục giống bài hiện tại
                        $stmt_related = $pdo->prepare("
            SELECT * FROM baiviet 
            WHERE ma_bai_viet != ? 
              AND ma_chuyen_muc = ? 
              AND trang_thai = 'published'
            ORDER BY RAND()
            LIMIT 4
        ");
                        $stmt_related->execute([$post['ma_bai_viet'], $post['ma_chuyen_muc']]);

                        $related = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($related as $r): ?>
                            <div class="related-item">
                                <a href="post.php?slug=<?= urlencode($r['duong_dan']) ?>">
                                    <img src="/php/<?= htmlspecialchars($r['anh_bv']) ?>" alt="">
                                    <h3><?= htmlspecialchars($r['tieu_de']) ?></h3>

                                    <!-- ⭐ NGÀY TIẾNG VIỆT -->
                                    <p><?= formatDateVN($r['ngay_dang']) ?></p>
                                </a>
                            </div>

                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
            <br>
            <?php
            // =========================
//  CHECK COMMENT COOLDOWN (30 seconds)
// =========================
            $commentCooldown = 30; // thời gian chờ 30 giây
            $can_comment = true;
            $remaining_comment_sec = 0;

            $stmt_last = $pdo->prepare("SELECT last_comment_at FROM khachhang WHERE id_kh = ?");
            $stmt_last->execute([$_SESSION['user_id']]);
            $last_comment_at = $stmt_last->fetchColumn();

            if ($last_comment_at) {
                $elapsed = time() - strtotime($last_comment_at);
                if ($elapsed < $commentCooldown) {
                    $can_comment = false;
                    $remaining_comment_sec = $commentCooldown - $elapsed;
                }
            }
            ?>

            <div class="comment-section">
                <h3>THAM GIA BÌNH LUẬN</h3>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <?php if ($user['is_banned'] == 1): ?>
                        <div class="login-prompt" style="color:red; font-weight:bold;">
                            ⛔ Tài khoản của bạn đã bị BAN — không thể bình luận.
                        </div>

                    <?php elseif ($user['is_muted'] == 1): ?>

                        <?php
                        // ===============================
                        // Tính thời gian còn lại (có giây)
                        // ===============================
                
                        $muteMessage = "Bạn đang bị cấm CHAT — không thể bình luận.";

                        if (!empty($user['muted_until'])) {

                            date_default_timezone_set("Asia/Ho_Chi_Minh");

                            $now = time();
                            $end = strtotime($user['muted_until']);

                            if ($end > $now) {

                                $diff = $end - $now;

                                $days = floor($diff / 86400);
                                $hours = floor(($diff % 86400) / 3600);
                                $mins = floor(($diff % 3600) / 60);
                                $secs = $diff % 60;   // ⭐ GIÂY CÒN LẠI
                
                                if ($days > 0) {
                                    $muteMessage = "Bạn đang bị cấm CHAT — còn $days ngày $hours giờ $mins phút $secs giây nữa.";
                                } elseif ($hours > 0) {
                                    $muteMessage = "Bạn đang bị cấm CHAT — còn $hours giờ $mins phút $secs giây nữa.";
                                } elseif ($mins > 0) {
                                    $muteMessage = "Bạn đang bị cấm CHAT — còn $mins phút $secs giây nữa.";
                                } else {
                                    // Dưới 1 phút chỉ còn giây
                                    $muteMessage = "Bạn đang bị cấm CHAT — còn $secs giây nữa.";
                                }
                            }

                        } else {
                            $muteMessage = "Bạn đang bị cấm CHAT vĩnh viễn — không thể bình luận.";
                        }
                        ?>

                        <?php
                        $remainingSeconds = 0;

                        if ($user['is_muted'] == 1 && !empty($user['muted_until'])) {
                            $remainingSeconds = strtotime($user['muted_until']) - time();
                            if ($remainingSeconds < 0)
                                $remainingSeconds = 0;
                        }
                        ?>

                        <div class="mute-warning" id="muteBox">
                            <i class="fa-solid fa-ban"></i>
                            <span id="muteText"><?= $muteMessage ?></span>
                        </div>

                        <script>
                            window.MUTE_REMAINING = <?= $remainingSeconds ?>;
                            window.USER_ID = <?= $user['id_kh'] ?>;
                        </script>


                    <?php else: ?>

                        <?php if ($can_comment): ?>

                            <form class="comment-form" action="../controller/comment.php?slug=<?= htmlspecialchars($slug) ?>"
                                method="POST">
                                <textarea name="comment_text" placeholder="Leave a comment..." required></textarea>
                                <button type="submit" class="submit-btn">Gửi bình luận</button>
                            </form>

                        <?php else: ?>

                            <div class="mute-warning" id="commentCooldownBox" style="margin-top:15px;">
                                <i class="fa-solid fa-clock"></i>
                                <span>Vui lòng chờ <b id="commentCD"><?= $remaining_comment_sec ?></b>
                                    giây... để bình luận tiếp</span>
                            </div>

                            <script>
                                let cSec = <?= $remaining_comment_sec ?>;
                                const cdBox = document.getElementById("commentCD");

                                const timer = setInterval(() => {
                                    cSec--;
                                    if (cSec <= 0) {
                                        cdBox.innerText = 0;
                                        clearInterval(timer);
                                        location.reload(); // tự reload để hiện form bình luận
                                    } else {
                                        cdBox.innerText = cSec;
                                    }
                                }, 1000);
                            </script>

                        <?php endif; ?>

                    <?php endif; ?>

                <?php else: ?>
                    <div class="login-prompt">
                        <p>Please login or register to comment.</p>
                        <label for="showLogin" class="login-link">Sign in</label> |
                        <label for="showSignup" class="signup-link">Sign up</label>
                    </div>
                <?php endif; ?>


                <!-- Dropdown sắp xếp -->
                <div class="sort-comments">
                    <label for="sort">Sắp xếp bình luận: </label>
                    <select name="sort" id="sort"
                        onchange="window.location.href = 'post.php?slug=<?= urlencode($slug) ?>&sort=' + this.value;">
                        <option value="newest" <?= ($_GET['sort'] ?? '') === 'newest' ? 'selected' : '' ?>>Mới nhất
                        </option>
                        <option value="oldest" <?= ($_GET['sort'] ?? '') === 'oldest' ? 'selected' : '' ?>>Cũ nhất
                        </option>
                        <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Tên (A →
                            Z)
                        </option>
                        <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Tên (Z
                            →
                            A)</option>
                    </select>
                </div>

                <!-- Hiển thị bình luận -->
                <div id="comments-container">
                    <?php
                    if ($comments):
                        foreach ($comments as $comment):
                            ?>
                            <div class="comment" id="comment-<?= $comment['id_binhluan'] ?>">

                                <div class="avatar-container">

                                    <!-- AVATAR NGƯỜI BÌNH LUẬN -->
                                    <?php
                                    $cm_avt = !empty($comment['avatar_url'])
                                        ? "/php/" . htmlspecialchars($comment['avatar_url'])
                                        : "../img/avt.jpg";
                                    ?>
                                    <img src="<?= $cm_avt ?>" alt="Avatar" class="avatar">

                                    <!-- FRAME NGƯỜI BÌNH LUẬN -->
                                    <?php
                                    $cm_frame = '';
                                    if (!empty($comment['avatar_frame'])) {
                                        $extensions = ['png', 'gif', 'jpg', 'jpeg'];
                                        foreach ($extensions as $ext) {
                                            $path = "../frames/" . $comment['avatar_frame'] . "." . $ext;
                                            if (file_exists($path)) {
                                                $cm_frame = $path;
                                                break;
                                            }
                                        }
                                    }
                                    ?>
                                    <?php if (!empty($cm_frame)): ?>
                                        <img src="<?= $cm_frame ?>" class="frame-overlay">
                                    <?php endif; ?>

                                </div>

                                <div class="comment-text">
                                    <br>
                                    <strong><?= htmlspecialchars($comment['ho_ten']) ?></strong>

                                    <span class="comment-time">
                                        <?= date("d/m/Y H:i", strtotime($comment['ngay_binhluan'])) ?>
                                    </span>

                                    <p><?= nl2br(htmlspecialchars($comment['noi_dung'])) ?></p>

                                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['id_kh']): ?>
                                        <a href="javascript:void(0);" class="edit-comment"
                                            onclick="editComment(<?= $comment['id_binhluan'] ?>)">Sửa</a>
                                        <a href="javascript:void(0);" class="delete-comment"
                                            onclick="deleteComment(<?= $comment['id_binhluan'] ?>, '<?= urlencode($slug) ?>')">Xóa</a>
                                    <?php endif; ?>

                                </div>

                            </div>

                            <br>
                        </div>
                        <?php
                        endforeach;
                    else:
                        echo "<p>Chưa có bình luận nào.</p>";
                    endif;
                    ?>
            </div>
            </div>

        </article>

        <!-- Cột phải: bài phổ biến -->
        <aside class="sidebar">
            <h3>POPULAR POSTS</h3>
            <ul class="popular-list">
                <?php foreach ($popular as $p): ?>
                    <li class="popular-item">

                        <!-- Bọc ảnh trong thẻ <a> -->
                        <a href="post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                            <img src="/php/<?= htmlspecialchars($p['anh_bv']) ?>" alt="">
                        </a>

                        <div class="info">
                            <!-- Tiêu đề vẫn là 1 liên kết -->
                            <a href="post.php?slug=<?= urlencode($p['duong_dan']) ?>">
                                <?= htmlspecialchars($p['tieu_de']) ?>
                            </a>

                            <!-- ⭐ NGÀY DẠNG TIẾNG VIỆT -->
                            <p class="date"><?= formatDateVN($p['ngay_dang']) ?></p>
                        </div>

                    </li>
                <?php endforeach; ?>
            </ul>


            <div class="ads">

                <div class="ad-box">Advertisement</div>
                <br>
                <div class="ad-box">Advertisement</div>
            </div>
        </aside>
    </main>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            if (typeof window.MUTE_REMAINING === "undefined") return;
            let sec = window.MUTE_REMAINING;

            const muteBox = document.getElementById("muteBox");
            const muteText = document.getElementById("muteText");

            if (!muteBox) return;

            function updateText() {
                if (sec <= 0) {
                    muteText.innerHTML = "⏳ Đang kiểm tra…";
                    unmuteNow();
                    return;
                }

                let d = Math.floor(sec / 86400);
                let h = Math.floor((sec % 86400) / 3600);
                let m = Math.floor((sec % 3600) / 60);
                let s = sec % 60;

                if (d > 0)
                    muteText.innerHTML = `Bạn đang bị cấm CHAT — còn ${d} ngày ${h} giờ ${m} phút ${s} giây`;
                else if (h > 0)
                    muteText.innerHTML = `Bạn đang bị cấm CHAT — còn ${h} giờ ${m} phút ${s} giây`;
                else if (m > 0)
                    muteText.innerHTML = `Bạn đang bị cấm CHAT — còn ${m} phút ${s} giây`;
                else
                    muteText.innerHTML = `Bạn đang bị cấm CHAT — còn ${s} giây`;

                sec--;
            }

            function unmuteNow() {
                fetch("../controller/unmute.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: "id_kh=" + window.USER_ID
                })
                    .then(res => res.text())
                    .then(result => {
                        if (result === "UNMUTED") {
                            muteText.innerHTML = "🎉 Bạn đã được gỡ cấm chat!";
                            setTimeout(() => {
                                muteBox.style.display = "none";
                                location.reload();
                            }, 1500);
                        }
                    });
            }

            updateText();
            setInterval(updateText, 1000);

        });
    </script>

    <?php include '../partials/footer.php'; ?>

</body>

</html>
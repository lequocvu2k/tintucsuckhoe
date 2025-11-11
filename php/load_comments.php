<?php
require_once './db.php';

$slug = $_GET['slug'] ?? '';
if (empty($slug)) {
    echo "Không có bài viết.";
    exit;
}

$stmt_post = $pdo->prepare("SELECT ma_bai_viet FROM baiviet WHERE duong_dan = ? AND trang_thai = 'published'");
$stmt_post->execute([$slug]);
$post = $stmt_post->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    echo "Bài viết không tồn tại.";
    exit;
}

$stmt_comments = $pdo->prepare("SELECT c.*, kh.ho_ten, kh.avatar_url FROM binhluan c
                                JOIN khachhang kh ON c.id_kh = kh.id_kh
                                WHERE c.ma_bai_viet = ? ORDER BY c.ngay_binhluan DESC");
$stmt_comments->execute([$post['ma_bai_viet']]);
$comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);

if ($comments):
    foreach ($comments as $comment):
        ?>
        <div class="comment">
            <div class="comment-avatar">
                <img src="<?= !empty($comment['avatar_url']) ? htmlspecialchars($comment['avatar_url']) : '../img/avt.jpg' ?>"
                    alt="Avatar">
            </div>
            <div class="comment-text">
                <p><strong><?= htmlspecialchars($comment['ho_ten']) ?></strong> <span
                        class="comment-time"><?= date("F d, Y H:i", strtotime($comment['ngay_binhluan'])) ?></span></p>
                <p><?= nl2br(htmlspecialchars($comment['noi_dung'])) ?></p>
            </div>
        </div>
    <?php endforeach;
else:
    echo '<p>Chưa có bình luận nào.</p>';
endif;
?>
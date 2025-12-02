<div class="action-buttons">
    <?php
    $can_post = true;

    $check = $pdo->prepare("
    SELECT id FROM status 
    WHERE id_kh = ? 
      AND ngay_dang >= NOW() - INTERVAL 24 HOUR
    LIMIT 1
");
    $check->execute([$_SESSION['user_id'] ?? 0]);

    if ($check->rowCount() > 0) {
        $can_post = false;
    }
    ?>

    <a href="<?= $can_post ? 'index.php?action=status' : '#' ?>" class="btn-status <?= $can_post ? '' : 'disabled' ?>">
        Up trạng thái <i class="fa-solid fa-comment-dots"></i>
    </a>

    <a href="index.php?action=chat" class="btn-chat">
        Phòng chat <i class="fa-solid fa-comments"></i>
    </a>

</div>


<!-- Danh sách trạng thái -->
<div id="statusList" class="status-list"></div>

<?php if (isset($_SESSION['user_id'])): ?>
    <!-- Popup đăng trạng thái -->
    <div id="statusPopup" class="status-popup">
        <div class="popup-content">

            <h2>Đăng trạng thái mới</h2>

            <input type="text" id="statusInput" maxlength="50" placeholder="Bạn đang nghĩ gì..." class="status-input">

            <div class="avatar-wrapper">
                <div class="avatar-container">
                    <img src="<?= $avatar ?>" class="user-avatar">
                    <?php if ($frame): ?>
                        <img src="<?= $frame ?>" class="avatar-frame">
                    <?php endif; ?>
                </div>
            </div>

            <p id="charCount">0/50</p>
            <p class="note">Nội dung sẽ tự động xoá sau 24h.</p>

            <div class="btn-group">
                <button id="cancelStatus" class="btn-cancel">Cancel</button>
                <button id="shareStatus" class="btn-share">Share</button>
            </div>

        </div>
    </div>

    <?php if (isset($_SESSION['open_status_popup'])): ?>
        <style>
            #statusPopup {
                display: flex;
            }
        </style>
        <?php unset($_SESSION['open_status_popup']); ?>
    <?php endif; ?>


<?php endif; ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const popup = document.getElementById("statusPopup");
        const cancelBtn = document.getElementById("cancelStatus");

        if (cancelBtn) {
            cancelBtn.addEventListener("click", function () {
                popup.style.display = "none";
            });
        }
    });
</script>
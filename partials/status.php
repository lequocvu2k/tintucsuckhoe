<div class="action-buttons">
    <a href="#" class="btn-status">
        Up trạng thái <i class="fa-solid fa-comment-dots"></i>
    </a>

    <a href="../view/chat_messages.php" class="btn-chat">
        Phòng chat <i class="fa-solid fa-comments"></i>
    </a>

</div>

<!-- Danh sách trạng thái -->
<div id="statusList" class="status-list"></div>

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
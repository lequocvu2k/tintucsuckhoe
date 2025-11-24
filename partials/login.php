<!-- Các Radio Buttons -->
<input type="radio" name="popup" id="showLogin" hidden>
<input type="radio" name="popup" id="showSignup" hidden>
<input type="radio" name="popup" id="hidePopup" hidden checked>

<!-- Popup Login -->
<div class="popup" id="loginPopup">
    <div class="popup-content">
        <!-- Thêm hình ảnh tròn -->
        <div class="avatar-container">
            <img src="../img/yuuka.png" alt="Avatar" class="avatar-circle">
        </div>
        <h2>Đăng nhập</h2>
        <form method="post" action="./login.php" autocomplete="off">
            <input type="text" name="username" placeholder="Tên đăng nhập" required><br><br>

            <div class="password-wrapper">
                <input type="password" name="password" id="loginPassword" placeholder="Mật khẩu" required>
                <span class="toggle-password" data-target="loginPassword"><i class="fa fa-eye"></i></span>
            </div>

            <button type="submit">Đăng nhập</button>
        </form>
        <label for="hidePopup" class="close-btn">Đóng</label>
        <label for="showSignup" class="switch-link">Chưa có tài khoản? Đăng ký</label>
    </div>
</div>

<!-- Popup Signup -->
<div class="popup" id="signupPopup">
    <div class="popup-content">
        <!-- Thêm hình ảnh tròn -->
        <div class="avatar-container">
            <img src="../img/yuuka.png" alt="Avatar" class="avatar-circle">
        </div>
        <h2>Đăng ký</h2>
        <form method="POST" action="./signup.php" autocomplete="off">
            <input type="text" name="username" placeholder="Tên đăng nhập" required><br><br>
            <input type="text" name="ho_ten" placeholder="Họ và tên" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>

            <div class="password-wrapper">
                <input type="password" name="password" id="signupPassword" placeholder="Mật khẩu" required>
                <span class="toggle-password" data-target="signupPassword"><i class="fa fa-eye"></i></span>
            </div>

            <div class="password-wrapper">
                <input type="password" name="confirm_password" id="signupConfirmPassword"
                    placeholder="Xác nhận mật khẩu" required>
                <span class="toggle-password" data-target="signupConfirmPassword"><i class="fa fa-eye"></i></span>
            </div>

            <button type="submit">Đăng ký</button>
        </form>
        <label for="hidePopup" class="close-btn">Đóng</label>
        <br>
        <label for="showLogin" class="switch-link">Đã có tài khoản? Đăng nhập</label>
    </div>
</div>

<br>
<?php if (isset($_SESSION['error'])): ?>
    <div class="message-error">
        <?= htmlspecialchars($_SESSION['error']); ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php elseif (isset($_SESSION['signup_error'])): ?>
    <div class="message-error">
        <?= htmlspecialchars($_SESSION['signup_error']); ?>
    </div>
    <?php unset($_SESSION['signup_error']); ?>
<?php elseif (isset($_SESSION['login_error'])): ?>
    <div class="message-error">
        <?= htmlspecialchars($_SESSION['login_error']); ?>
    </div>
    <?php unset($_SESSION['login_error']); ?>
<?php elseif (isset($_SESSION['msg'])): ?>
    <div class="message-success">
        <?= htmlspecialchars($_SESSION['msg']); ?>
    </div>
    <?php unset($_SESSION['msg']); ?>
<?php endif; ?>
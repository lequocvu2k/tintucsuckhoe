<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");
$pdo->exec("SET time_zone = '+07:00'");

?>
<canvas class="fireworks"></canvas>
<!-- Nút cuộn lên đầu trang -->
<div id="backToTop">
    <img src="../img/toTop.Cuiv4RfP.svg" alt="Back to Top">
</div>

<!-- ✅ HEADER -->
<header class="site-header">
    <!-- LOGO -->
    <div class="left">
        <a href="index.php" class="logo-link">
            <img src="/img/health-logo.png" alt="Logo" class="logo-img" />
        </a>
    </div>

    <!-- NAVIGATION -->
    <nav class="main-nav" aria-label="Main navigation">
        <ul class="nav-menu">
            <li><a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a></li>

            <!-- KHÁM PHÁ -->
            <li class="dropdowns">
                <a href="#"><i class="fa-solid fa-compass"></i> Khám phá ▾</a>
                <ul class="dropdown-nav">
                    <li><a href="./experts.php"><i class="fa-solid fa-user-nurse"></i> Chuyên gia</a></li>
                    <li><a href="./advice.php"><i class="fa-solid fa-stethoscope"></i> Tư vấn theo triệu chứng</a>
                    </li>
                    <li><a href="./tu_danh_gia.php"><i class="fa-solid fa-heart-circle-check"></i> Tự đánh giá sức
                            khỏe</a></li>
                </ul>
            </li>

            <!-- SỨC KHỎE -->
            <li class="dropdowns">
                <a href="#"><i class="fa-solid fa-heart-pulse"></i> Sức khỏe ▾</a>
                <ul class="dropdown-nav">
                    <li><a href="./category.php?id=1"><i class="fa-solid fa-newspaper"></i> Tin tức</a></li>
                    <li><a href="./category.php?id=2"><i class="fa-solid fa-apple-whole"></i> Dinh dưỡng</a></li>
                    <li><a href="./category.php?id=3"><i class="fa-solid fa-dumbbell"></i> Khỏe đẹp</a></li>
                    <li><a href="./category.php?id=4"><i class="fa-solid fa-user-doctor"></i> Tư vấn</a></li>
                    <li><a href="./category.php?id=5"><i class="fa-solid fa-hospital"></i> Dịch vụ y tế</a></li>
                    <li><a href="./category.php?id=6"><i class="fa-solid fa-virus-covid"></i> Các bệnh</a></li>
                </ul>
            </li>
            <!-- XẾP HẠNG -->
            <li class="dropdowns">
                <a href="#"><i class="fa-solid fa-ranking-star"></i> Xếp hạng ▾</a>
                <ul class="dropdown-nav">

                    <li><a href="./ranking.php?type=likes">
                            <i class="fa-solid fa-thumbs-up"></i> Nhiều lượt like nhất
                        </a></li>

                    <li><a href="./ranking.php?type=weekview">
                            <i class="fa-solid fa-rocket"></i> Xem nhiều 7 ngày gần nhất
                        </a></li>

                    <li><a href="./ranking.php?type=comments">
                            <i class="fa-solid fa-comments"></i> Bài có nhiều bình luận
                        </a></li>

                </ul>
            </li>


            <li class="dropdowns">
                <a href="#"><i class="fa-solid fa-circle-info"></i> Thông tin ▾</a>
                <ul class="dropdown-nav">
                    <li><a href="./about.php#about"><i class="fa-solid fa-users"></i> Về chúng tôi</a></li>
                    <li><a href="./about.php#mission"><i class="fa-solid fa-bullseye"></i> Tầm nhìn & Sứ mệnh</a></li>
                    <li><a href="./about.php#policy"><i class="fa-solid fa-scale-balanced"></i> Chính sách biên tập</a>
                    </li>
                    <li><a href="./about.php#team"><i class="fa-solid fa-people-group"></i> Đội ngũ</a></li>
                </ul>
            </li>
            <li class="dropdowns">
                <a href="#"><i class="fa-solid fa-envelope-circle-check"></i> Liên hệ ▾</a>
                <ul class="dropdown-nav">
                    <li><a href="/mail/formmail.php"><i class="fa-solid fa-pen-to-square"></i> Gửi phản hồi</a></li>
                    <li><a href="mailto:vuliztva1@gmail.com"><i class="fa-solid fa-envelope"></i> Email hỗ trợ</a></li>
                    <li><a href="https://www.facebook.com/Shiroko412/" target="_blank"><i
                                class="fa-brands fa-facebook"></i> Fanpage</a></li>
                    <li><a href="https://zalo.me/0332138297" target="_blank"><i class="fa-brands fa-zhihu"></i> Zalo
                            liên hệ</a></li>
                </ul>
            </li>

        </ul>
    </nav>


    <!-- PHẦN BÊN PHẢI -->
    <div class="right">
        <!-- Nút tìm kiếm -->
        <button class="icon-btn" id="openSearch" aria-label="Tìm kiếm">
            <i class="fas fa-search"></i>
        </button>

        <!-- Thanh tìm kiếm -->
        <div class="search-bar" id="searchBar">
            <input type="text" placeholder="Tìm kiếm bài viết..." id="searchInput">
            <ul id="searchSuggestions" class="search-suggestions"></ul>
            <button id="searchSubmit"><i class="fas fa-arrow-right"></i></button>
        </div>

        <!-- USER INFO -->
        <?php if (isset($_SESSION['username'])): ?>
            <div class="header-user">
                <div class="avatar-container">
                    <?php
                    // 🖼 Avatar
                    $avatar = (!empty($user['avatar_url']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $user['avatar_url']))
                        ? htmlspecialchars($user['avatar_url'])
                        : '/img/avt.jpg';

                    // 🎨 Frame (nằm ngoài thư mục php)
                    $frame = '';
                    if (!empty($user['avatar_frame'])) {
                        $possibleExtensions = ['png', 'gif', 'jpg', 'jpeg'];
                        foreach ($possibleExtensions as $ext) {
                            $relativePath = '/frames/' . htmlspecialchars($user['avatar_frame']) . '.' . $ext;
                            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $relativePath)) {
                                $frame = $relativePath;
                                break;
                            }
                        }
                    }

                    // ✔ Hiển thị ảnh
                    echo '<img src="' . $avatar . '" alt="Avatar" class="avatar">';
                    if ($frame) {
                        echo '<img src="' . $frame . '" alt="Frame" class="frame-overlay">';
                    }
                    ?>

                </div>

                <div class="account-info">
                    <div class="name-container">
                        <p class="name"><?= htmlspecialchars($user['ho_ten']) ?></p>
                        <div class="user-email">
                            <?php if ($user['email'] == 'baka@gmail.com'): ?>
                                <span class="role-badge">ADMIN</span>
                            <?php else: ?>
                            <?php endif; ?>

                            <!-- Ẩn VIP tier nếu là admin -->
                            <?php if ($user['email'] != 'baka@gmail.com'): ?>
                                <p>
                                    <b class="vip-tier <?= strtolower(str_replace(' ', '-', $tier)) ?>">
                                        <?= htmlspecialchars($tier) ?>
                                    </b>
                                </p>
                            <?php endif; ?>
                        </div>

                        <!-- Dropdown menu -->
                        <div class="dropdown-menu">
                            <ul>
                                <li>
                                    <a href="./user.php">
                                        <i class="fas fa-user"></i> Tài khoản
                                        <!-- Kiểm tra nếu người dùng là ADMIN, hiển thị ADMIN -->
                                        <b
                                            class="vip-tier <?= ($_SESSION['username'] === 'admin') ? 'admin' : strtolower(str_replace(' ', '-', $tier)) ?>">
                                            <?php
                                            if ($_SESSION['username'] === 'admin') {
                                                echo '<span class="role-badge">ADMIN</span>';  // Hiển thị "ADMIN" cho người dùng admin
                                            } else {
                                                echo htmlspecialchars($tier);  // Hiển thị cấp độ thành viên cho người dùng khác
                                            }
                                            ?>
                                        </b>

                                    </a>
                                </li>

                                <li><a href="./user.php?view=history"><i class="fas fa-history"></i> Lịch sử</a></li>
                                <li><a href="./user.php?view=saved"><i class="fas fa-bookmark"></i> Đã lưu</a></li>
                                <li><a href="./user.php?view=notifications"><i class="fas fa-bell"></i> Thông báo</a>
                                </li>
                                <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'NhanVien' || $_SESSION['user_role'] === 'QuanTri')): ?>
                                    <li><a href="./expert_profile.php"><i class="fa-solid fa-user-doctor"></i> Hồ sơ Chuyên
                                            gia</a></li>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'QuanTri' || $_SESSION['user_role'] === 'NhanVien')): ?>
                                    <li class="dropdown">
                                        <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-cogs"></i> Quản
                                            lý</a>
                                        <ul class="dropdown-content">
                                            <li>
                                                <a href="./quanlybv.php">
                                                    <i class="fas fa-pencil-alt"></i> Quản lý bài viết
                                                </a>
                                            </li>

                                            <?php if ($_SESSION['user_role'] === 'QuanTri'): ?>
                                                <li>
                                                    <a href="./quanlyyeucau.php">
                                                        <i class="fas fa-list"></i> Quản lý yêu cầu
                                                    </a>
                                                </li>

                                                <li>
                                                    <a href="./hethongduyetbai.php">
                                                        <i class="fas fa-check-circle"></i> Duyệt bài viết
                                                    </a>
                                                </li>

                                                <!-- ⭐ Thêm mục quản lý người dùng -->
                                                <li>
                                                    <a href="./quanlynguoidung.php">
                                                        <i class="fas fa-users"></i> Quản lý người dùng
                                                    </a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>

                                    </li>
                                <?php endif; ?>

                                <li><a href="../controller/logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <label for="showLogin">Đăng nhập</label>
        <?php endif; ?>
    </div>
</header>
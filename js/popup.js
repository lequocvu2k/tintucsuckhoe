document.addEventListener("DOMContentLoaded", function () {
    // Hàm thay đổi kiểu hiển thị mật khẩu
    const passwordToggles = document.querySelectorAll(".toggle-password");

    passwordToggles.forEach(toggle => {
        toggle.addEventListener("click", function () {
            const target = document.getElementById(toggle.getAttribute("data-target"));
            if (target.type === "password") {
                target.type = "text"; // Hiển thị mật khẩu
                toggle.innerHTML = '<i class="fa fa-eye-slash"></i>'; // Thay đổi thành biểu tượng "ẩn"
            } else {
                target.type = "password"; // Ẩn mật khẩu
                toggle.innerHTML = '<i class="fa fa-eye"></i>'; // Thay đổi thành biểu tượng "hiển thị"
            }
        });
    });
});

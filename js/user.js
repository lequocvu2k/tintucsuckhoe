function togglePass(id, el) {
  const input = document.getElementById(id);
  const isPassword = input.type === "password";
  input.type = isPassword ? "text" : "password";

  // đổi icon
  el.classList.toggle("fa-eye");
  el.classList.toggle("fa-eye-slash");
}
function openXPModal() {
  document.getElementById("xpModal").style.display = "block";
}
function closeXPModal() {
  document.getElementById("xpModal").style.display = "none";
}
// Đóng popup khi click ra ngoài
window.onclick = function (event) {
  const modal = document.getElementById("xpModal");
  if (event.target === modal) {
    modal.style.display = "none";
  }
};
function toggleHistory() {
  const box = document.querySelector(".history-box");
  const btn = document.querySelector(".hide-btn");

  // Toggle class để ẩn/hiện
  box.classList.toggle("collapsed");

  // Đổi text nút
  if (box.classList.contains("collapsed")) {
    btn.textContent = "Hiển thị";
  } else {
    btn.textContent = "Ẩn bớt";
  }
}
function openEmployeeModal() {
  document.getElementById("employeeModal").style.display = "block";
}

function closeEmployeeModal() {
  document.getElementById("employeeModal").style.display = "none";
}

// Đóng modal nếu người dùng nhấp ra ngoài modal
window.onclick = function (event) {
  if (event.target == document.getElementById("employeeModal")) {
    closeEmployeeModal();
  }
};
document.addEventListener("DOMContentLoaded", function () {
  const tabs = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");

  // Lấy giá trị `view` từ URL và cập nhật active tab
  const urlParams = new URLSearchParams(window.location.search);
  const view = urlParams.get("view") || "info"; // Mặc định 'info' nếu không có giá trị view trong URL

  // Đảm bảo hiển thị nội dung của tab dựa trên giá trị `view`
  tabContents.forEach((content) => {
    if (content.id === view) {
      content.classList.add("active");
    } else {
      content.classList.remove("active");
    }
  });

  // Đánh dấu tab tương ứng là active
  const activeTab = document.querySelector(`.tab-btn[data-tab="${view}"]`);
  if (activeTab) {
    activeTab.classList.add("active");
  }

  // Xử lý khi người dùng nhấn vào tab
  tabs.forEach((tab) => {
    tab.addEventListener("click", function () {
      const activeTab = this.getAttribute("data-tab");

      // Cập nhật tham số URL khi nhấn tab
      window.history.pushState({}, "", `?view=${activeTab}`);

      // Cập nhật lại active class cho các tab
      tabs.forEach((t) => t.classList.remove("active"));
      this.classList.add("active");

      // Tải lại trang
      location.reload();
    });
  });
});

// ===========================
// 🔍 HIỆN / ẨN THANH TÌM KIẾM
// ===========================
document.addEventListener("DOMContentLoaded", () => {
  const searchBtn = document.getElementById("openSearch");
  const searchBar = document.getElementById("searchBar");

  if (searchBtn && searchBar) {
    searchBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      searchBar.classList.toggle("active");

      // Focus ô input khi mở
      if (searchBar.classList.contains("active")) {
        const input = searchBar.querySelector("input");
        if (input) input.focus();
      }
    });

    // Ẩn thanh tìm kiếm khi click ra ngoài
    document.addEventListener("click", (e) => {
      if (!searchBar.contains(e.target) && e.target !== searchBtn) {
        searchBar.classList.remove("active");
      }
    });
  }

  // ===============================
  // 👤 DROPDOWN USER (TÀI KHOẢN)
  // ===============================
  const accountInfo = document.querySelector(".account-info");
  const nameContainer = accountInfo?.querySelector(".name-container");
  const dropdown = nameContainer?.querySelector(".dropdown-menu");

  if (nameContainer && dropdown) {
    // Khi click vào tên hoặc avatar (nếu thêm sau này)
    nameContainer.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("show");
    });

    // Ẩn dropdown khi click ra ngoài
    document.addEventListener("click", (e) => {
      if (!accountInfo.contains(e.target)) {
        dropdown.classList.remove("show");
      }
    });
  }
});
document.querySelectorAll(".tab-btn").forEach((btn) => {
  btn.addEventListener("click", () => {
    document
      .querySelectorAll(".tab-btn")
      .forEach((b) => b.classList.remove("active"));
    btn.classList.add("active");

    document.querySelectorAll(".tab-content").forEach((tab) => {
      tab.classList.remove("active");
      tab.style.opacity = 0;
      tab.style.transform = "translateY(10px)";
    });

    const activeTab = document.getElementById(btn.dataset.tab);
    activeTab.classList.add("active");
    activeTab.style.opacity = 1;
    activeTab.style.transform = "translateY(0)";
  });
});
function togglePass(id, el) {
  const input = document.getElementById(id);
  const isPassword = input.type === "password";
  input.type = isPassword ? "text" : "password";

  // đổi icon
  el.classList.toggle("fa-eye");
  el.classList.toggle("fa-eye-slash");
}

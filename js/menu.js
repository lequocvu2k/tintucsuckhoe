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
document.addEventListener("DOMContentLoaded", function () {
  const dropdownBtn = document.querySelector(".dropdown-btn");
  const dropdownContent = document.querySelector(".dropdown-content");

  // Toggle dropdown khi nhấn vào nút
  dropdownBtn.addEventListener("click", function () {
    dropdownContent.style.display =
      dropdownContent.style.display === "block" ? "none" : "block";
  });

  // Đóng dropdown nếu nhấn ở nơi khác
  window.addEventListener("click", function (e) {
    if (
      !dropdownBtn.contains(e.target) &&
      !dropdownContent.contains(e.target)
    ) {
      dropdownContent.style.display = "none";
    }
  });
});
const searchInput = document.getElementById("searchInput");
const suggestionsBox = document.getElementById("searchSuggestions");

// Gợi ý theo thời gian thực
searchInput.addEventListener("input", function () {
  const keyword = searchInput.value.trim();

  if (keyword.length < 1) {
    suggestionsBox.innerHTML = "";
    suggestionsBox.style.display = "none";
    return;
  }

  fetch(`search_ajax.php?q=${encodeURIComponent(keyword)}`)
    .then((res) => res.json())
    .then((data) => {
      suggestionsBox.innerHTML = "";
      suggestionsBox.style.display = "block";

      if (data.length === 0) {
        suggestionsBox.innerHTML = "<li class='no-result'>Không tìm thấy</li>";
        return;
      }

      data.forEach((item) => {
        const li = document.createElement("li");
        li.classList.add("suggest-item");
        li.innerHTML = `
                    <img src="${item.anh_bv}" alt="">
                    <span>${item.tieu_de}</span>
                `;
        li.onclick = () => {
          window.location.href =
            "post.php?slug=" + encodeURIComponent(item.duong_dan);
        };
        suggestionsBox.appendChild(li);
      });
    });
});

// Nhấn Enter → sang trang search
searchInput.addEventListener("keypress", function (e) {
  if (e.key === "Enter") {
    const keyword = searchInput.value.trim();
    window.location.href = "search.php?q=" + encodeURIComponent(keyword);
  }
});

const slider = document.querySelector(".slider");
const slides = document.querySelectorAll(".slide");
const prevBtn = document.querySelector(".prev");
const nextBtn = document.querySelector(".next");
let index = 0;

function showSlide(i) {
  if (!slides.length) return;
  index = (i + slides.length) % slides.length;
  slider.style.transform = `translateX(${-index * 100}%)`;
}

// Nút chuyển thủ công
nextBtn.addEventListener("click", () => showSlide(index + 1));
prevBtn.addEventListener("click", () => showSlide(index - 1));

document.querySelectorAll(".toggle-password").forEach((toggle) => {
  toggle.addEventListener("click", function () {
    const targetId = this.getAttribute("data-target");
    const input = document.getElementById(targetId);
    if (input.type === "password") {
      input.type = "text";
      this.innerHTML = '<i class="fa fa-eye-slash"></i>';
    } else {
      input.type = "password";
      this.innerHTML = '<i class="fa fa-eye"></i>';
    }
  });
});
document.addEventListener("DOMContentLoaded", () => {
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

// ==================== SEARCH BAR ====================
const openSearch = document.getElementById("openSearch");
const searchBar = document.getElementById("searchBar");

if (openSearch && searchBar) {
  openSearch.addEventListener("click", (e) => {
    e.stopPropagation();
    searchBar.classList.toggle("show");
    const input = document.getElementById("searchInput");
    if (input && searchBar.classList.contains("show")) {
      input.focus();
    }
  });

  document.addEventListener("click", (e) => {
    if (!searchBar.contains(e.target) && e.target !== openSearch) {
      searchBar.classList.remove("show");
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
let currentPage = 1;

function loadLatest(page = 1) {
  fetch(`../controller/api_latest.php?page=${page}`)
    .then((res) => res.json())
    .then((data) => {
      currentPage = data.page;

      const grid = document.getElementById("latest-grid");
      grid.innerHTML = "";

      data.posts.forEach((p) => {
        grid.innerHTML += `
                <div class="latest-item">
                    <a href="./post.php?slug=${encodeURIComponent(
                      p.duong_dan
                    )}">
                        <img src="/php/${p.anh_bv}" alt="">
                        <p class="post-title">${p.tieu_de}</p>
                        <div class="author-date">
                             <span>By <b>${p.tac_gia ?? "Unknown"}</b></span>
                            <span>${new Date(p.ngay_dang).toDateString()}</span>
                        </div>
                    </a>
                </div>`;
      });

      // Disable buttons if needed
      document.getElementById("btnPrev").style.opacity = page > 1 ? "1" : "0.3";
      document.getElementById("btnNext").style.opacity =
        page < data.totalPages ? "1" : "0.3";

      // Save total pages
      window.latestTotalPages = data.totalPages;
    });
}

// Nút chuyển trang
document.getElementById("btnPrev").addEventListener("click", () => {
  if (currentPage > 1) loadLatest(currentPage - 1);
});
document.getElementById("btnNext").addEventListener("click", () => {
  if (currentPage < window.latestTotalPages) loadLatest(currentPage + 1);
});

// Load lần đầu
loadLatest();
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

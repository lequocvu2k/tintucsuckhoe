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

/* Xóa tag HTML khỏi nội dung */
function cleanHTML(html) {
    return html.replace(/<[^>]*>?/gm, "");
}

/* Rút gọn mô tả */
function excerpt(text, limit = 150) {
    return text.length > limit ? text.substring(0, limit) + "..." : text;
}

function loadLatest(page = 1) {

    fetch(`../controller/api_latest.php?page=${page}`)
        .then(res => res.json())
        .then(data => {

            const grid = document.getElementById("latest-grid");
            grid.innerHTML = "";

            data.posts.forEach(p => {

                let contentText = cleanHTML(p.noi_dung ?? "");
                let shortDesc = excerpt(contentText, 130);

                grid.innerHTML += `
                <div class="latest-card">

                    <a href="./post.php?slug=${encodeURIComponent(p.duong_dan)}">
                        <img src="/php/${p.anh_bv}" class="latest-thumb">
                    </a>

                    <div class="latest-info">

                        <div class="latest-cat">
                            ${p.category ?? "News"}
                        </div>

                        <a href="./post.php?slug=${encodeURIComponent(p.duong_dan)}">
                            <h3 class="latest-title">${p.tieu_de}</h3>
                        </a>

                        <div class="latest-meta">
                            By <b>${p.tac_gia ?? "Unknown"}</b> • 
                            ${new Date(p.ngay_dang).toDateString()}
                        </div>

                        <p class="latest-excerpt">${shortDesc}</p>

                        <div class="latest-share">
                            <i class="fa-brands fa-facebook"></i>
                            <i class="fa-brands fa-x-twitter"></i>
                            <i class="fa-brands fa-instagram"></i>
                            <i class="fa-solid fa-link"></i>
                        </div>

                    </div>
                </div>
                `;
            });

            // cập nhật trang hiện tại
            currentPage = data.page;
            window.latestTotalPages = data.totalPages;

            // Bật / tắt nút phân trang
            document.getElementById("btnPrev").style.opacity =
                currentPage > 1 ? "1" : "0.3";

            document.getElementById("btnNext").style.opacity =
                currentPage < data.totalPages ? "1" : "0.3";
        });
}


// ============================
// Nút phân trang
// ============================

document.getElementById("btnPrev").addEventListener("click", () => {
    if (currentPage > 1) loadLatest(currentPage - 1);
});

document.getElementById("btnNext").addEventListener("click", () => {
    if (currentPage < window.latestTotalPages) loadLatest(currentPage + 1);
});


// Load khi vào trang
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

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
    .then((res) => res.json())
    .then((data) => {
      const grid = document.getElementById("latest-grid");
      grid.innerHTML = "";

      data.posts.forEach((p) => {
        let contentText = cleanHTML(p.noi_dung ?? "");
        let shortDesc = excerpt(contentText, 130);

        grid.innerHTML += `
                <div class="latest-card">

                    <a href="./post.php?slug=${encodeURIComponent(
                      p.duong_dan
                    )}">
                        <img src="/php/${p.anh_bv}" class="latest-thumb">
                    </a>

                    <div class="latest-info">

                        <div class="latest-cat">
                            ${p.category ?? "News"}
                        </div>

                        <a href="./post.php?slug=${encodeURIComponent(
                          p.duong_dan
                        )}">
                            <h3 class="latest-title">${p.tieu_de}</h3>
                        </a>

                        <div class="latest-meta">
                            By <b>${p.tac_gia ?? "Unknown"}</b> • 
                            ${new Date(p.ngay_dang).toDateString()}
                        </div>

                        <p class="latest-excerpt">${shortDesc}</p>

                         <div class="latest-share">
            <i class="fa-brands fa-facebook" data-url="./post.php?slug=${encodeURIComponent(
              p.duong_dan
            )}"></i>
            <i class="fa-brands fa-x-twitter" data-url="./post.php?slug=${encodeURIComponent(
              p.duong_dan
            )}"></i>
            <i class="fa-brands fa-instagram" data-url="./post.php?slug=${encodeURIComponent(
              p.duong_dan
            )}"></i>
            <i class="fa-solid fa-link" data-url="./post.php?slug=${encodeURIComponent(
              p.duong_dan
            )}"></i>
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
document.addEventListener("click", function (e) {
  if (e.target.closest(".latest-share i")) {
    let icon = e.target;
    let postUrl = icon.dataset.url;
    let fullUrl = window.location.origin + "/view/" + postUrl.replace("./", "");

    // FACEBOOK
    if (icon.classList.contains("fa-facebook")) {
      window.open(
        `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(
          fullUrl
        )}`,
        "_blank"
      );
    }

    // TWITTER (X)
    if (icon.classList.contains("fa-x-twitter")) {
      window.open(
        `https://twitter.com/share?url=${encodeURIComponent(fullUrl)}`,
        "_blank"
      );
    }

    // INSTAGRAM (không có API share trực tiếp)
    if (icon.classList.contains("fa-instagram")) {
      alert(
        "Instagram không hỗ trợ chia sẻ trực tiếp. Hãy dùng nút Copy Link!"
      );
    }

    // COPY LINK
    if (icon.classList.contains("fa-link")) {
      navigator.clipboard.writeText(fullUrl);
      icon.style.color = "#ff0055";
      alert("📋 Đã sao chép liên kết!");
    }
  }
});
// mở popup
document.querySelector(".btn-status").addEventListener("click", () => {
  document.getElementById("statusPopup").classList.add("active");
});

// đóng popup
document.getElementById("cancelStatus").addEventListener("click", () => {
  document.getElementById("statusPopup").classList.remove("active");
});

// đếm ký tự
document.getElementById("statusInput").addEventListener("input", function () {
  document.getElementById("charCount").innerText = `${this.value.length}/50`;
});

// gửi trạng thái
document.getElementById("shareStatus").addEventListener("click", () => {
  let text = document.getElementById("statusInput").value.trim();
  if (text.length === 0) {
    alert("Bạn chưa nhập gì!");
    return;
  }

  fetch("../controller/add_status.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "noi_dung=" + encodeURIComponent(text),
  })
    .then((res) => res.text())
    .then((result) => {
      if (result === "ok") {
        document.getElementById("statusPopup").classList.remove("active");
        document.getElementById("statusInput").value = "";
        loadStatus(); // ⬅️ Hiện lại danh sách trạng thái
      } else {
        alert("Lỗi đăng trạng thái!");
      }
    });
});

function loadStatus() {
  fetch("../controller/get_status.php")
    .then((res) => res.json())
    .then((list) => {
      const wrap = document.getElementById("statusList");
      wrap.innerHTML = "";

      list.forEach((st) => {
        // Avatar đã được backend trả đúng: ../uploads/avatars/xxx.png
        let avatar = st.avatar_url;

        // Frame đã được backend trả đúng: ../frames/xxx.gif
        let frame = st.avatar_frame_url;

        wrap.innerHTML += `
    <div class="status-item">

        <div class="status-top">
            <div class="avatar-container1">
                <img src="${avatar}" class="status-avatar">
                ${frame ? `<img src="${frame}" class="status-frame">` : ""}
            </div>
        </div>
<br>
        <div class="status-info">
            <b>${st.ho_ten}</b>
            <div class="status-text">${st.noi_dung}</div>
            <div class="status-time">${new Date(
              st.ngay_dang
            ).toLocaleString()}</div>
            
<div class="status-remaining" id="remain-${st.id}">
    ...
</div>
        </div>

        <div class="status-like ${st.liked ? "liked" : ""}" data-id="${st.id}">
            <i class="fa-solid fa-heart"></i>
            <span>${st.total_like}</span>
        </div>

    </div>
`;
        startCountdown(st.id, st.ngay_dang); // ⬅ THÊM DÒNG NÀY
      });

      attachLikeEvents();
    });
}
loadStatus();
// xử lý click nút tim
function attachLikeEvents() {
  document.querySelectorAll(".status-like").forEach((btn) => {
    btn.addEventListener("click", function () {
      let id_status = this.dataset.id;

      fetch("../controller/status_like.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "id_status=" + id_status,
      })
        .then((res) => res.text())
        .then((res) => {
          loadStatus(); // reload để cập nhật số like
        });
    });
  });
}

loadStatus();
function startCountdown(id, startTime) {
  const box = document.getElementById("remain-" + id);
  if (!box) return;

  function update() {
    const now = new Date();
    const start = new Date(startTime);
    const expire = start.getTime() + 24 * 60 * 60 * 1000; // 24h

    const diff = expire - now;

    if (diff <= 0) {
      box.innerHTML = "<span class='expired'>Đã hết hạn</span>";
      return;
    }

    const h = Math.floor(diff / 3600000);
    const m = Math.floor((diff % 3600000) / 60000);
    const s = Math.floor((diff % 60000) / 1000);

    // Hiển thị đẹp
    if (h > 0) box.innerHTML = `⏳ Còn ${h}h ${m}m`;
    else if (m > 0) box.innerHTML = `⏳ Còn ${m} phút`;
    else box.innerHTML = `⏳ Còn ${s} giây`;
  }

  update();
  setInterval(update, 1000);
}

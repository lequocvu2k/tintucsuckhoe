document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("questionContainer");
  const loadBtn = document.getElementById("loadMore");
  const collapseBtn = document.getElementById("collapseBtn");
  const sortSelect = document.getElementById("sortQuestion");

  const expertID = document.body.getAttribute("data-id");
  const CURRENT_USER_ID = document.body.getAttribute("data-current");

  let offset = 0;
  let sort = "newest";

  /* ================== XÓA CÂU HỎI ================== */
  window.deleteQuestion = function (id) {
    if (!confirm("🗑️ Xóa câu hỏi này?")) return;

    fetch("../controller/delete_question.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id_hoi=" + id,
    })
      .then((res) => res.text())
      .then((data) => {
        if (data.trim() === "OK") {
          offset = 0;
          loadQuestions(true);
        } else {
          alert("❌ Lỗi: " + data);
        }
      });
  };

  /* ================== LOAD CÂU HỎI ================== */
  function loadQuestions(reset = false) {
    fetch(
      `../view/expert_detail.php?api_questions=1&id=${expertID}&offset=${offset}&sort=${sort}`
    )
      .then((res) => res.json())
      .then((data) => {
        if (reset) container.innerHTML = "";

        if (!data || data.length === 0) {
          if (offset === 0) {
            container.innerHTML = "<p>❌ Chưa có câu hỏi nào.</p>";
          }
          loadBtn.style.display = "none";
          collapseBtn.style.display = offset > 0 ? "block" : "none";
          return;
        }

        // Hiện hoặc ẩn nút xem thêm
        loadBtn.style.display = data.length < 5 ? "none" : "block";

        // Hiện nút thu gọn nếu offset > 0
        collapseBtn.style.display = offset > 0 ? "block" : "none";

        data.forEach((q) => {
          const avatarUser = q.nguoi_hoi_avatar
            ? `/php/${q.nguoi_hoi_avatar}`
            : "../img/avt.jpg";

          const avatarExpert = q.expert_avatar
            ? `/php/${q.expert_avatar}`
            : "../img/avt.jpg";

          const isExpert = CURRENT_USER_ID == expertID;

          container.innerHTML += `
            <div class="question-item">

                <div class="question-user">
                    <img src="${avatarUser}" class="avatar-sm">
                    <span>${q.nguoi_hoi_ten}</span>
                </div>

                <p class="question-text">${q.cau_hoi}</p>

                <div class="question-date">📅 
                  ${new Date(q.ngay_hoi).toLocaleDateString("vi-VN")}
                </div>

                ${
                  isExpert
                    ? `<button class="btn-del-q" onclick="deleteQuestion(${q.id_hoi})">🗑 Xóa</button>`
                    : ""
                }

                ${
                  q.cau_tra_loi
                    ? `
                  <div class="answer-box">
                      <div class="answer-user">
                          <img src="${avatarExpert}" class="avatar-sm answer-avatar">
                          <b>${q.expert_name}</b>
                      </div>
                      <p class="answer-text"><b>Trả lời:</b> ${q.cau_tra_loi}</p>
                      <div class="answer-date">📌 
                        ${new Date(q.ngay_tra_loi).toLocaleDateString("vi-VN")}
                      </div>
                  </div>
                `
                    : `<p class="waiting-answer">⏳ Chưa có câu trả lời...</p>`
                }

            </div>
          `;
        });
      });
  }

  /* ================== EVENT: XEM THÊM ================== */
  loadBtn.addEventListener("click", () => {
    offset += 5;
    loadQuestions();
  });

  /* ================== EVENT: THU GỌN ================== */
  collapseBtn.addEventListener("click", () => {
    offset = 0;
    loadQuestions(true);
    collapseBtn.style.display = "none";
    loadBtn.style.display = "block";
  });

  /* ================== EVENT: SORT ================== */
  sortSelect.addEventListener("change", () => {
    sort = sortSelect.value;
    offset = 0;
    loadQuestions(true);
  });

  loadQuestions();
});

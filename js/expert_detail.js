document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("questionContainer");
  const loadBtn = document.getElementById("loadMore");
  const sortSelect = document.getElementById("sortQuestion");

  const expertID = document.body.getAttribute("data-id");
  let offset = 0;
  let sort = "newest";

  function loadQuestions(reset = false) {
    fetch(`../view/expert_detail.php?api_questions=1&id=${expertID}&offset=${offset}&sort=${sort}`)
      .then((res) => res.json())
      .then((data) => {
        if (reset) container.innerHTML = "";

        if (data.length === 0 && offset === 0) {
          container.innerHTML = "<p>❌ Chưa có câu hỏi nào.</p>";
          loadBtn.style.display = "none";
          return;
        }

        loadBtn.style.display = data.length < 5 ? "none" : "block";

        data.forEach((q) => {
          container.innerHTML += `
        <div class="question-item">
            <div class="question-user">
                <img src="${q.avatar_url || "../img/avt.jpg"}" class="avatar-sm">
                <span>${q.ho_ten}</span>
            </div>
            <p class="question-text">${q.cau_hoi}</p>
            <div class="question-date">📅 Hỏi lúc: ${new Date(q.ngay_hoi).toLocaleDateString("vi-VN")}</div>

            ${
              q.cau_tra_loi
                ? `
        <div class="answer-box">
            <div class="answer-user">
                <img src="${q.expert_avatar || "../img/avt.jpg"}" class="avatar-sm answer-avatar">
                <b>${q.expert_name}</b>
            </div>
            <p class="answer-text"><b>Trả lời:</b> ${q.cau_tra_loi}</p>
            <div class="answer-date">📌 ${new Date(q.ngay_tra_loi).toLocaleDateString("vi-VN")}</div>
        </div>`
                : `<p class="waiting-answer">⏳ Chưa có câu trả lời...</p>`
            }
        </div>`;
        });
      });
  }

  loadBtn.addEventListener("click", () => {
    offset += 5;
    loadQuestions();
  });

  sortSelect.addEventListener("change", () => {
    sort = sortSelect.value;
    offset = 0;
    loadQuestions(true);
  });

  loadQuestions();
});

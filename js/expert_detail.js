let sort = "newest";
let offset = 0;
let id = document.body.getAttribute("data-id");

function loadQuestions(reset = false) {
  if (reset) {
    offset = 0;
    document.getElementById("questionContainer").innerHTML = "";
  }

  fetch(
    `expert_detail.php?api_questions=1&id=${id}&offset=${offset}&sort=${sort}`
  )
    .then((res) => res.json())
    .then((data) => {
      if (data.length === 0 && offset === 0) {
        document.getElementById("questionContainer").innerHTML =
          "<p>💬 Chưa có câu hỏi nào.</p>";
        document.getElementById("loadMore").style.display = "none";
        return;
      }

      if (data.length === 0) {
        document.getElementById("loadMore").innerText = "✔️ Hết câu hỏi";
        document.getElementById("loadMore").disabled = true;
        return;
      }

      data.forEach((q) => {
        let avatar = q.avatar_url || "../img/avt.jpg";
        let answerHTML = "";

        if (q.cau_tra_loi) {
          answerHTML = `
                        <div class="answer-block">
                            <p><b>Trả lời:</b> ${q.cau_tra_loi}</p>
                        </div>
                    `;
        } else {
          answerHTML = `<p class="waiting">⏳ Chưa có câu trả lời</p>`;
        }

        let item = `
                    <div class="question-item">
                        <div class='question-user'>
                            <img src='${avatar}' class='avatar'>
                            <div>
                                <strong>${q.ho_ten}</strong>
                                <span class='time'>${q.ngay_hoi}</span>
                            </div>
                        </div>
                        <p> ${q.cau_hoi}</p>
                        ${answerHTML}
                    </div>`;
        document
          .getElementById("questionContainer")
          .insertAdjacentHTML("beforeend", item);
      });

      offset += 5;
    });
}

loadQuestions();

document.getElementById("loadMore").onclick = () => loadQuestions();
document.getElementById("sortQuestion").onchange = function () {
  sort = this.value;
  loadQuestions(true);
};
window.onscroll = function () {
  if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
    loadQuestions();
  }
};

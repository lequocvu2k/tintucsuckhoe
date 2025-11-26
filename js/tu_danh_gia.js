function askAI() {
  const q = document.getElementById("question").value.trim();
  if (q === "") return alert("❓ Vui lòng nhập câu hỏi!");

  document.getElementById("reply").style.display = "block";
  document.getElementById("reply").innerHTML = "⏳ Đang phân tích...";

  fetch("../controller/health_faq.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "question=" + encodeURIComponent(q),
  })
    .then((res) => res.json())
    .then((data) => {
      document.getElementById("reply").innerHTML = data.answer;
    })
    .catch(() => {
      document.getElementById("reply").innerHTML =
        "⚠️ Không thể kết nối đến AI!";
    });
}

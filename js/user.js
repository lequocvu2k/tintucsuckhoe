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

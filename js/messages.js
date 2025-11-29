// ============ JS PHÒNG CHAT ============

// id user hiện tại (để phân biệt tin của mình)
const CURRENT_USER_ID = parseInt(document.body.dataset.userId || "0", 10);

/** Escape HTML để tránh XSS */
function escapeHtml(str) {
  return str
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

const chatBody = document.getElementById("chatBody");
const chatInput = document.getElementById("chatInput");
const chatForm = document.getElementById("chatForm");
const btnSend = document.getElementById("btnSend");
const editNotice = document.getElementById("editNotice");
const cancelEdit = document.getElementById("cancelEdit");
let editingMessageId = null; // ID tin nhắn đang sửa (null = gửi mới)

/** ===========================
 *  LOAD DANH SÁCH TIN NHẮN
 * =========================== */
function loadMessages(scrollToBottom = false) {
  fetch("../controller/chat_get.php")
    .then((res) => res.json())
    .then((list) => {
      chatBody.innerHTML = "";

      list.forEach((msg) => {
        const own = parseInt(msg.id_kh, 10) === CURRENT_USER_ID;

        const wrapper = document.createElement("div");
        wrapper.className = "message-item" + (own ? " own" : "");
        wrapper.dataset.id = msg.id;

        const avatar = document.createElement("img");
        avatar.className = "msg-avatar";
        avatar.src = msg.avatar_url || "/php/img/avt.jpg";

        const bubble = document.createElement("div");
        bubble.className = "msg-bubble";

        const meta = document.createElement("div");
        meta.className = "msg-meta";

        const name = document.createElement("span");
        name.className = "msg-name";
        name.textContent = msg.ho_ten || "Ẩn danh";

        const time = document.createElement("span");
        time.className = "msg-time";
        time.textContent = new Date(msg.created_at).toLocaleString();

        meta.appendChild(name);
        meta.appendChild(time);

        const text = document.createElement("div");
        text.className = "msg-text";
        text.innerHTML = escapeHtml(msg.message || "");

        bubble.appendChild(meta);
        bubble.appendChild(text);

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);

        // ---------- NÚT SỬA / XÓA ----------
        if (own) {
          const actions = document.createElement("div");
          actions.className = "msg-actions";

          actions.innerHTML = `
              <button class="edit-msg"><i class="fa-solid fa-pen"></i></button>
              <button class="delete-msg"><i class="fa-solid fa-trash"></i></button>
          `;

          wrapper.appendChild(actions);
        }

        chatBody.appendChild(wrapper);
      });

      if (scrollToBottom) {
        chatBody.scrollTop = chatBody.scrollHeight;
      }
    })
    .catch((err) => {
      console.error("Load chat error:", err);
    });
}

/** =======================================================
 *  GỬI TIN NHẮN HOẶC LƯU TIN NHẮN ĐÃ SỬA
 * ======================================================= */
chatForm.addEventListener("submit", function (e) {
  e.preventDefault();
  const text = chatInput.value.trim();
  if (!text) return;

  /** ------------------------
   *  CHẾ ĐỘ SỬA TIN NHẮN
   * ------------------------ */
  if (editingMessageId !== null) {
    fetch("../controller/edit_message.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + editingMessageId + "&message=" + encodeURIComponent(text),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          chatInput.value = "";
          editingMessageId = null;

          // Trả nút về trạng thái Gửi
          btnSend.innerHTML = `<i class="fa-solid fa-paper-plane"></i> Gửi`;

          loadMessages(true);
        }
      });

    return;
  }

  /** ------------------------
   *  CHẾ ĐỘ GỬI TIN NHẮN MỚI
   * ------------------------ */
  const params = new URLSearchParams();
  params.append("message", text);

  fetch("../controller/chat_send.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((res) => res.text())
    .then((res) => {
      if (res.trim() === "ok") {
        chatInput.value = "";
        loadMessages(true);
      }
    })
    .catch((err) => console.error("Send chat error:", err));
});

/** ===========================
 *  XÓA TIN NHẮN
 * =========================== */
document.addEventListener("click", function (e) {
  if (e.target.closest(".delete-msg")) {
    let msg = e.target.closest(".message-item");
    let id = msg.dataset.id;

    if (!confirm("Xóa tin nhắn này?")) return;

    fetch("../controller/delete_message.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "id=" + id,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") msg.remove();
      });
  }
});

/** ===========================
 *  BẬT CHẾ ĐỘ SỬA TIN NHẮN
 * =========================== */
document.addEventListener("click", function (e) {
  if (e.target.closest(".edit-msg")) {
    let msg = e.target.closest(".message-item");
    let id = msg.dataset.id;
    let oldText = msg.querySelector(".msg-text").innerText;
    editingMessageId = id;
    chatInput.value = oldText;
    chatInput.focus();

    // đổi nút thành Sửa
    btnSend.innerHTML = `<i class="fa-solid fa-pen"></i> Sửa`;

    // hiện thông báo “Đang sửa...”
    editNotice.style.display = "flex";

    chatBody.scrollTop = chatBody.scrollHeight;
  }
});
cancelEdit.addEventListener("click", function () {
  editingMessageId = null; // thoát chế độ sửa
  chatInput.value = ""; // xóa nội dung đang sửa
  btnSend.innerHTML = `<i class="fa-solid fa-paper-plane"></i> Gửi`; // đổi nút về Gửi
  editNotice.style.display = "none"; // ẩn thông báo
});

// =================== AUTO LOAD ===================
loadMessages(true);
setInterval(loadMessages, 3000);


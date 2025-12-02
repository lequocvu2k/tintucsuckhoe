// ============ JS PHÒNG CHAT ============

// id user hiện tại (để phân biệt tin của mình)
const CURRENT_USER_ID = parseInt(document.body.dataset.userId || "0", 10);

// id tin nhắn đang được trả lời
let replyToId = null;

/** Escape HTML để tránh XSS */
function escapeHtml(str) {
  return String(str || "")
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
const replyBox = document.getElementById("replyBox");
const replyText = document.getElementById("replyText");
const cancelReply = document.getElementById("cancelReply");

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

        // ---------- NÚT HÀNH ĐỘNG ----------
        const actions = document.createElement("div");
        actions.className = "msg-actions";

        // Tin của NGƯỜI KHÁC -> chỉ có Reply
        if (!own) {
          actions.innerHTML += `
            <button class="reply-msg" title="Trả lời">
              <i class="fa-solid fa-reply"></i>
            </button>
          `;
        }

        // Tin của MÌNH -> Sửa + Xoá
        if (own) {
          actions.innerHTML += `
            <button class="edit-msg" title="Sửa">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button class="delete-msg" title="Xóa">
              <i class="fa-solid fa-trash"></i>
            </button>
          `;
        }

        // Chỉ append nếu có ít nhất 1 nút
        if (actions.innerHTML.trim() !== "") {
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

  // ----- CHẾ ĐỘ SỬA TIN NHẮN -----
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
          btnSend.innerHTML = `<i class="fa-solid fa-paper-plane"></i> Gửi`;
          editNotice.style.display = "none";
          loadMessages(true);
        }
      });

    return;
  }

  // ----- CHẾ ĐỘ GỬI TIN NHẮN MỚI -----
  const params = new URLSearchParams();
  params.append("message", text);
  params.append("reply_to", replyToId ? replyToId : ""); // CHỈ 1 biến reply_to

  fetch("../controller/chat_send.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: params.toString(),
  })
    .then((res) => res.text())
    .then((res) => {
      if (res.trim() === "ok") {
        chatInput.value = "";
        // reset trạng thái trả lời
        replyToId = null;
        if (replyBox) replyBox.style.display = "none";
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

    btnSend.innerHTML = `<i class="fa-solid fa-pen"></i> Sửa`;
    editNotice.style.display = "flex";

    chatBody.scrollTop = chatBody.scrollHeight;
  }
});

if (cancelEdit) {
  cancelEdit.addEventListener("click", function (e) {
    e.preventDefault();
    editingMessageId = null;
    chatInput.value = "";
    btnSend.innerHTML = `<i class="fa-solid fa-paper-plane"></i> Gửi`;
    editNotice.style.display = "none";
  });
}

/** ===========================
 *  TRẢ LỜI TIN NHẮN
 * =========================== */
document.addEventListener("click", function (e) {
  if (e.target.closest(".reply-msg")) {
    let msg = e.target.closest(".message-item");
    replyToId = msg.dataset.id;

    let author = msg.querySelector(".msg-name").innerText;
    let content = msg.querySelector(".msg-text").innerText.substring(0, 40);

    // Hiện hộp reply phía trên
    if (replyBox && replyText) {
      replyBox.style.display = "flex";
      replyText.innerHTML =
        `Trả lời <b>${escapeHtml(author)}</b>: "` +
        escapeHtml(content) +
        '..."';
    }

    // ⭐⭐ Thêm @Tên vào textarea ⭐⭐
    chatInput.value = `@${author}: `;
    chatInput.focus();
  }
});

if (cancelReply) {
  cancelReply.addEventListener("click", function (e) {
    e.preventDefault();
    replyToId = null;
    if (replyBox) replyBox.style.display = "none";
  });
}

// =================== AUTO LOAD ===================
loadMessages(true);
setInterval(loadMessages, 3000);

// Hàm sửa bình luận
function editComment(commentId) {
  // Tạo một prompt để người dùng nhập lại bình luận mới
  var newCommentText = prompt("Nhập bình luận mới:");
  if (newCommentText) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "../controller/edit_comment.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        // Nếu sửa thành công, cập nhật nội dung bình luận trên trang mà không cần F5
        alert("Bình luận đã được sửa");
        var commentTextElement = document.getElementById(
          "comment-text-" + commentId
        );
        commentTextElement.innerText = newCommentText; // Cập nhật nội dung bình luận
      }
    };
    xhr.send(
      "id=" + commentId + "&comment_text=" + encodeURIComponent(newCommentText)
    );
  }
}
// Hàm xóa bình luận
function deleteComment(commentId, slug) {
  if (confirm("Bạn có chắc chắn muốn xóa bình luận này không?")) {
    var xhr = new XMLHttpRequest();
    xhr.open(
      "GET",
      "../controller/delete_comment.php?id=" + commentId + "&slug=" + slug,
      true
    );
    xhr.onreadystatechange = function () {
      if (xhr.readyState == 4 && xhr.status == 200) {
        // Nếu xóa thành công, xóa bình luận khỏi trang mà không tải lại
        alert("Bình luận đã được xóa");
        var commentElement = document.getElementById("comment-" + commentId); // Lấy phần tử bình luận
        commentElement.remove(); // Xóa bình luận khỏi giao diện
      }
    };
    xhr.send();
  }
}
function sortComments() {
  var sortValue = document.getElementById("sort").value;
  var slug = window.location.search.split("=")[1]; // Lấy slug từ URL

  // Gửi yêu cầu AJAX
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "../view/post.php?slug=" + slug + "&sort=" + sortValue, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      // Cập nhật lại phần bình luận
      document.getElementById("comments-container").innerHTML =
        xhr.responseText;
    }
  };
  xhr.send();
}
// JavaScript không cần nhiều thay đổi vì chúng ta đã xử lý việc thay đổi lựa chọn và gửi dữ liệu qua URL.
function sortComments() {
  var sortValue = document.getElementById("sort").value;
  var slug = window.location.search.split("=")[1]; // Lấy slug từ URL

  // Thực hiện chuyển hướng lại với tham số sort
  window.location.href = "../view/post.php?slug=" + slug + "&sort=" + sortValue;
}
function likePost(id) {
  fetch("../controller/like_post.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "ma_bai_viet=" + id,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.status === "success") {
        let count = document.getElementById("likeCount");
        count.textContent = parseInt(count.textContent) + 1;

        // đổi style nút
        let btn = document.getElementById("likeBtn");
        btn.querySelector("i").style.color = "#ff004c";
        btn.disabled = true;

        // popup +12 điểm
        const popup = document.createElement("div");
        popup.textContent = "+12 điểm!";
        popup.style.position = "fixed";
        popup.style.bottom = "80px";
        popup.style.right = "30px";
        popup.style.background = "rgba(0, 200, 0, 0.9)";
        popup.style.color = "#fff";
        popup.style.padding = "10px 20px";
        popup.style.borderRadius = "10px";
        popup.style.fontWeight = "bold";
        popup.style.fontSize = "18px";
        popup.style.zIndex = "9999";
        popup.style.boxShadow = "0 0 10px rgba(0,0,0,0.3)";
        document.body.appendChild(popup);
        setTimeout(() => {
          popup.style.opacity = "0";
          popup.style.transform = "translateY(-50px)";
        }, 1500);
        setTimeout(() => {
          popup.remove();
        }, 2000);
      } else {
        alert("Hãy đăng nhập để like bài viết!");
      }
    });
}

document.addEventListener("DOMContentLoaded", () => {
  let box = document.getElementById("muteBox");
  let text = document.getElementById("muteText");

  if (!box || window.muteRemaining === undefined) return;

  let time = window.muteRemaining;

  function updateCountdown() {
    if (time <= 0) {
      text.innerHTML = "Bạn đã được gỡ cấm chat! Hãy tải lại trang.";
      box.style.background = "#28a745";
      box.style.color = "#fff";
      return;
    }

    let days = Math.floor(time / 86400);
    let hours = Math.floor((time % 86400) / 3600);
    let mins = Math.floor((time % 3600) / 60);
    let secs = time % 60;

    let msg = "Bạn đang bị cấm CHAT — còn ";

    if (days > 0) {
      msg += `${days} ngày ${hours} giờ ${mins} phút ${secs} giây`;
    } else if (hours > 0) {
      msg += `${hours} giờ ${mins} phút ${secs} giây`;
    } else if (mins > 0) {
      msg += `${mins} phút ${secs} giây`;
    } else {
      msg += `${secs} giây`;
    }

    msg += " nữa.";

    text.innerHTML = msg;
    time--;
  }

  updateCountdown();

  setInterval(updateCountdown, 1000);
});

// Hàm sửa bình luận
function editComment(commentId) {
  // Tạo một prompt để người dùng nhập lại bình luận mới
  var newCommentText = prompt("Nhập bình luận mới:");
  if (newCommentText) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "edit_comment.php", true);
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
      "delete_comment.php?id=" + commentId + "&slug=" + slug,
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
  xhr.open("GET", "post.php?slug=" + slug + "&sort=" + sortValue, true);
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
  window.location.href = "post.php?slug=" + slug + "&sort=" + sortValue;
}

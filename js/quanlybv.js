const searchInput = document.getElementById("searchInput");
searchInput.addEventListener("keyup", function () {
  const filter = this.value.toLowerCase();
  const rows = document.querySelectorAll("#articleTable tbody tr");
  rows.forEach((row) => {
    const title = row
      .querySelector("td:nth-child(2)")
      .textContent.toLowerCase();
    row.style.display = title.includes(filter) ? "" : "none";
  });
});
document.getElementById("anh_bv").addEventListener("change", function () {
  const fileName = this.files.length ? this.files[0].name : "Chưa chọn ảnh nào";
  document.getElementById("file-name").textContent = fileName;
});
tinymce.init({
  selector: 'textarea[name="noi_dung"]', // Chọn textarea cần thay thế
  height: 300,
  plugins: "advlist autolink lists link image charmap print preview anchor",
  toolbar:
    "undo redo | bold italic | alignleft aligncenter alignright | code | image link",
  content_style: "body { font-family:Arial, sans-serif; font-size:14px }",
  images_upload_url: "upload_image.php", // URL của script xử lý ảnh
  automatic_uploads: true, // Tự động tải ảnh lên khi người dùng chèn ảnh

  setup: function (editor) {
    // Đảm bảo rằng TinyMCE sẽ cập nhật nội dung vào textarea khi thay đổi
    editor.on("change", function () {
      tinymce.triggerSave(); // Đồng bộ hóa nội dung vào textarea
    });
  },
});

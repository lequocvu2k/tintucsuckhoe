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

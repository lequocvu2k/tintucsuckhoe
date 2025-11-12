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

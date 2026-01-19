document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("actlog-table").class = "recent-system-logs";

  const div = document.getElementById("asset-distribution");

  fetch("../../src/handlers/act-log.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
  .then(res => res.json())
  .then(data => {
    const totalAssetCnt = document.createElement("h2");
    totalAssetCnt.textContent = data.
    div.querySelector("#total-assets");
  })
  .catch(err => console.error("Error fetching system logs: ", err));
});
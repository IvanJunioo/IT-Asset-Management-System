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
    totalAssetCnt.textContent = data.assetsTotal;
    div.querySelector("#total-assets").prepend(totalAssetCnt);

    const availAssetCnt = document.createElement("h2");
    availAssetCnt.textContent = data.assetsAvail;
    div.querySelector("#total-assets").prepend(availAssetCnt);
        
    const totalUserCnt = document.createElement("h2");
    totalUserCnt.textContent = data.usersTotal;
    div.querySelector("#total-assets").prepend(totalUserCnt);

    const availUserCnt = document.createElement("h2");
    availUserCnt.textContent = data.usersActive;
    div.querySelector("#total-assets").prepend(availUserCnt);
        
  })
  .catch(err => console.error("Error fetching system logs: ", err));
});
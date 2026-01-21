document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("actlog-table").className = "recent-system-logs";

  const div = document.getElementById("asset-distribution");

  fetch("../../src/handlers/dashboard.php", {
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
    div.querySelector("#avail-assets").prepend(availAssetCnt);
        
    const totalUserCnt = document.createElement("h2");
    totalUserCnt.textContent = data.usersTotal;
    div.querySelector("#total-users").prepend(totalUserCnt);

    const activeUserCnt = document.createElement("h2");
    activeUserCnt.textContent = data.usersActive;
    div.querySelector("#active-users").prepend(activeUserCnt);
        
  })
  .catch(err => console.error("Error fetching: ", err));
});
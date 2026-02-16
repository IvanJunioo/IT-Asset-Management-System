function link(privilege, entityClass) {
  const isAdmin = privilege === "SuperAdmin" || privilege === "Admin";
  
  const routes = {
    asset: isAdmin ? "asset-manager.php" : "assets.php",
    user: isAdmin ? "user-manager.php" : "users.php"
  };

  return routes[entityClass] || "";
}

document.addEventListener("DOMContentLoaded", () => {
  document.getElementById("actlog-table").className = "recent-system-logs";

  const sect = document.getElementById("asset-distribution");

  fetch(`${window.location.origin}/src/handlers/dashboard.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
  .then(res => res.json())
  .then(data => {
    const totalAssetCnt = document.createElement("h2");
    totalAssetCnt.textContent = data.assetsTotal;
    sect.querySelector("#total-assets").prepend(totalAssetCnt);

    const availAssetCnt = document.createElement("h2");
    availAssetCnt.textContent = data.assetsAvail;
    sect.querySelector("#avail-assets").prepend(availAssetCnt);
        
    const totalUserCnt = document.createElement("h2");
    totalUserCnt.textContent = data.usersTotal;
    sect.querySelector("#total-users").prepend(totalUserCnt);

    const activeUserCnt = document.createElement("h2");
    activeUserCnt.textContent = data.usersActive;
    sect.querySelector("#active-users").prepend(activeUserCnt);
        
  })
  .catch(err => console.error("Error fetching: ", err));

  fetch(`${window.location.origin}/src/handlers/user-info.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
  .then(res => res.json())
  .then(data => {
    sessionStorage.setItem("user-info", JSON.stringify(data));
    sect.querySelector("#total-assets").href = link(data["privilege"], "asset");
    sect.querySelector("#avail-assets").href = link(data["privilege"], "asset");
    sect.querySelector("#total-users").href = link(data["privilege"], "user");
    sect.querySelector("#active-users").href = link(data["privilege"], "user");
  })
  .catch(err => console.error("Error fetching: ", err));
});
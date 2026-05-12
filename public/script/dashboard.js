import { LogTable } from "./components.js";
import { getAssetStats, getUserStats } from "./api.js";

const sect = document.getElementById("asset-distribution");

const logTable = new LogTable({container: document.getElementById("activity-log")});
logTable.table.className = "recent-system-logs";

try {
  const [assetStats, userStats] = await Promise.all([getAssetStats(), getUserStats()]);

  const totalAssetCnt = document.createElement("h2");
  totalAssetCnt.textContent = assetStats.assetsTotal;
  sect.querySelector("#total-assets").prepend(totalAssetCnt);

  const availAssetCnt = document.createElement("h2");
  availAssetCnt.textContent = assetStats.assetsAvail;
  sect.querySelector("#avail-assets").prepend(availAssetCnt);

  const totalUserCnt = document.createElement("h2");
  totalUserCnt.textContent = userStats.usersTotal;
  sect.querySelector("#total-users").prepend(totalUserCnt);

  const activeUserCnt = document.createElement("h2");
  activeUserCnt.textContent = userStats.usersActive;
  sect.querySelector("#active-users").prepend(activeUserCnt);
}
catch (err) {
  console.error("Error fetching Database statistics:", err);
}

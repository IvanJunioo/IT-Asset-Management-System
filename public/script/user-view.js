import { fetchLogs } from "./act-log.js";
import { returnAsset } from "./asset-router.js";

const cardDiv = document.querySelector(".user-card");
const headerDiv = cardDiv.querySelector(".log-header");
const assetTable = cardDiv.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

const userData = JSON.parse(sessionStorage.getItem("userData"));
const user = Array.isArray(userData) ? userData[0] : userData;

headerDiv.querySelector(".user-name").textContent = `${user.FName} ${user.LName}`;

const userBadge = document.createElement("span");
userBadge.className = `badge ${user.ActiveStatus.toLowerCase()}`;
userBadge.textContent = user.ActiveStatus;
headerDiv.querySelector(".user-info").appendChild(userBadge);

let assetData = new Map();
let latest = 0; // latest fetch id to avoid race conditions

fetchAssets();
fetchLogs({actorID: user.EmpID});

cardDiv.addEventListener("click", (e) => {
  if (e.target.closest(".back-link")) {
    window.history.back();
  }
});

assetTableBody.addEventListener("click", (e) => {
  const tr = e.target.closest("tr");
  if (!tr) return;

  if (e.target.closest(".select-btn")) {
    returnAsset([tr.dataset.propNum]);
    return;
  }
});

async function fetchAssets() {    
  const fetchID = ++latest;

  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assignment",
    action: "fetch",
    user: user.EmpID,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

    const data = await resp.json();
    assetData = new Map(data.map(asset => [asset.PropNum, asset]));
    
    if (fetchID !== latest) return;
    showAssets();
    assetTableBody.dispatchEvent(new CustomEvent("assetsLoaded"));
  } catch (err) {
    console.error("Error fetching assets: ", err);
  }
}

function showAssets() {
  if (assetData.size <= 0) {
    assetTableBody.innerHTML = `
      <tr>
        <td colSpan="${assetTable.querySelector("thead tr").children.length}" style="text-align: center;"> No assets to display. </td>
      </tr>
    `;
    return;
  }

  // Add another header
  const hr = document.querySelector(".asset-table thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  assetTableBody.innerHTML = "";
  
  for (const [_, asset] of assetData) {
    const tr = document.createElement('tr');

    // Store id
    tr.dataset.propNum = asset.PropNum;

    for (const col of [
      asset.ProcNum,
      asset.PropNum,
      asset.PurchaseDate,
      asset.Specs,
      parseFloat(asset.Price).toFixed(2),
      `<span class="badge ${asset.Status.toLowerCase()}">${asset.Status}</span>`,
    ]) {
      const td = document.createElement("td");
      td.innerHTML = col;
      tr.appendChild(td);
    }

    // Action button
    const actBtn = document.createElement("button");
    actBtn.className = "select-btn";
    actBtn.textContent = "Return";
    const td = document.createElement("td");
    td.append(actBtn);
    tr.append(td);

    assetTableBody.appendChild(tr);
  }
}

import { fetchAsset } from "./api.js";
import { LogTable } from "./components.js";

const urlParams = new URLSearchParams(window.location.search);
const assetData = await fetchAsset(urlParams.get("propNum"));
const assetView = document.querySelector("#asset-info"); 

const asset = Array.isArray(assetData) ? assetData[0] : assetData;
fillPage();

const logTable = new LogTable({
  container: document.getElementById("activity-log"),
  metadata: asset["PropNum"],
});
logTable.table.className = "asset-view-table";

function fillPage() {
  const data = {
    'pnum': asset['PropNum'],
    'prnum': asset['ProcNum'],
    'snum': asset['SerialNum'],
    'pdate': asset['PurchaseDate'],
    'price': parseFloat(asset['Price']).toFixed(2),
    'specs': asset['Specs'],
    'desc': asset['ShortDesc'],
    'remarks': asset['Remarks'],
    'sd_url': `<a href="${asset['Url']}">${asset['Url']}</a>`,
    'stats': `<span class="badge ${asset['Status'].toLowerCase()}">${asset['Status']}</span>`,
  };

  for (const [k,v] of Object.entries(data)) {
    const div = assetView.querySelector(`#${k}`);
    div.innerHTML += v;
  }
}
  
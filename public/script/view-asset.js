import { fetchLogs } from "./act-log.js";

const assetData = JSON.parse(sessionStorage.getItem("viewAssetData"));
const assetView = document.querySelector(".asset-info"); 

fillPage(Array.isArray(assetData) ? assetData[0] : assetData);

document.querySelector("#actlog-table").className = "asset-view-table";

function fillPage(asset) {
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

  fetchLogs({metadata: data["pnum"]});
}
  
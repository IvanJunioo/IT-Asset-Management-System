import { fetchLogs } from "./act-log.js";

document.addEventListener("DOMContentLoaded", () => {
  const assetData = JSON.parse(sessionStorage.getItem("viewAssetData"));
  const assetView = document.querySelector(".asset-info"); 

  if (!assetData) return;

  fillPage(Array.isArray(assetData) ? assetData[0] : assetData);
  // sessionStorage.removeItem("viewAssetData");

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
			'stats': asset['Status'],
    };

    for (const [k,v] of Object.entries(data)) {
      const div = assetView.querySelector(`#${k}`);
      div.innerHTML += v;
    }

    fetchLogs(data["pnum"]);
  }
});
		
document.addEventListener("DOMContentLoaded", () => {
  const assetData = JSON.parse(localStorage.getItem("viewAssetData"));
  const assetView = document.querySelector(".asset-info"); 

  console.log(assetData);
  if (!assetData) return;

  fillPage(Array.isArray(assetData) ? assetData[0] : assetData);
  localStorage.removeItem("viewAssetData");

  function fillPage(asset) {
    const data = {
      'pnum': asset['PropNum'],
      'prnum': asset['ProcNum'],
      'snum': asset['SerialNum'],
      'pdate': asset['PurchaseDate'],
      'price': asset['Price'],
      'specs': asset['Specs'],
      'desc': asset['ShortDesc'],
      'remarks': asset['Remarks'],
      'img_url': asset['Url'],
			'stats': asset['Status'],
			'alog' : asset['ActLog']
    };

    childrenInput = assetView.querySelectorAll('span');
    for (const child of childrenInput) {
			child.textContent = data[child.id];
    }

  }
});
		
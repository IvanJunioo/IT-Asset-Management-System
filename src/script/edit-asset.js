document.addEventListener("DOMContentLoaded", () => {
  const assetData = JSON.parse(localStorage.getItem("assetData"));
  const assetForm = document.querySelector(".add-asset-form"); 

  console.log(assetData);
  if (!assetData) return;

  fillForm(Array.isArray(assetData) ? assetData[0] : assetData);
  localStorage.removeItem("assetData");

  function fillForm(asset) {
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
    };

    childrenInput = assetForm.querySelectorAll('input');
    for (const child of childrenInput) {
      if (child.id in data) {
        child.value = data[child.id];
      } else if (child.id === asset.Status) {
        child.checked = true;
      }
    }

    const childrenText = assetForm.querySelectorAll('textarea');
    for (const child of childrenText) {
      if (child.id in data) {
        child.value = data[child.id];
      }
    }
  }
});
		
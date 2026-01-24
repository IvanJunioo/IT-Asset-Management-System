document.addEventListener("DOMContentLoaded", () => {
  const assetData = JSON.parse(sessionStorage.getItem("assetData"));
  const assetForm = document.querySelector(".asset-form"); 
  const form = assetForm.querySelector("form");

  form.action = "../../src/handlers/edit-asset-form.php"; 
  form.method = "post";

  form.querySelector("input#pnum").readOnly = true;
  form.querySelector("input#prnum").readOnly = true;
  form.querySelector("input#snum").readOnly = true;

  if (!assetData) return;

  fillForm(Array.isArray(assetData) ? assetData[0] : assetData);
  // sessionStorage.removeItem("assetData");

  function fillForm(asset) {
    const data = {
      'pnum': asset['PropNum'],
      'prnum': asset['ProcNum'],
      'snum': asset['SerialNum'],
      'pdate': asset['PurchaseDate'],
      'price': asset['Price'],
      'specs': asset['Specs'],
      'desc': asset['ShortDesc'],
      'img_url': asset['Url'],
    };

    const childrenInput = assetForm.querySelectorAll('input');
    for (const child of childrenInput) {
      if (child.id in data) {
        child.value = data[child.id];
      } else if (child.value === asset['Status']) {
        child.checked = true; 
      }
    }

    const childrenText = assetForm.querySelectorAll('textarea');
    for (const child of childrenText) {
      if (child.id in data) {
        child.value = data[child.id];
      }
    }

    const statusGroup = assetForm.querySelector('#status-group');
    if (asset['Status'] === "Assigned") {
      statusGroup.innerHTML = '';
      const assignedLabel = document.createElement('label');
      const assignedRadio = document.createElement('input');
      assignedRadio.type = 'radio';
      assignedRadio.name = 'asset-status';
      assignedRadio.value = 'Assigned';
      assignedRadio.checked = true;
      assignedLabel.appendChild(assignedRadio);
      assignedLabel.appendChild(document.createTextNode(' Assigned'));
      statusGroup.appendChild(assignedLabel);
    }
  }

});
		
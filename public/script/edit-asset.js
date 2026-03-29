const assetData = JSON.parse(sessionStorage.getItem("assetData"));
const assetForm = document.querySelector("#asset-form"); 

assetForm.action = `${window.location.origin}/api/index.php?resource=assets&action=edit`; 
assetForm.method = "post";

fillForm(Array.isArray(assetData) ? assetData[0] : assetData);

assetForm.querySelector("input#pnum").readOnly = true;
assetForm.querySelector("input#prnum").readOnly = true;
const snum = assetForm.querySelector("input#snum"); 
if (snum.value.trim() !== "") snum.readOnly = true;

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

    const assignedSpan = document.createElement("span");
    assignedSpan.className = "badge assigned";
    assignedSpan.textContent = "Assigned";
    assignedLabel.appendChild(assignedSpan);

    statusGroup.appendChild(assignedLabel);
  }
}

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  fillForm(Array.isArray(assetData) ? assetData[0] : assetData);
})

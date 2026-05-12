import {relayPage} from "./asset-router.js";
import { fetchAsset, condemnAsset} from "./api.js";

const urlParams = new URLSearchParams(window.location.search);
const assetData = await fetchAsset(urlParams.get("propNum"));

const main = document.querySelector("main");
const assetForm = main.querySelector("#asset-form"); 

assetForm.action = `${window.location.origin}/api/index.php?resource=assets&action=edit&redirect=${encodeURIComponent("index.php?page=asset-manager")}`; 
assetForm.method = "post";

const asset = Array.isArray(assetData) ? assetData[0] : assetData;

fillForm(asset);
addAssignmentButtons();

assetForm.querySelector("input#pnum").readOnly = true;
assetForm.querySelector("input#prnum").readOnly = true;
const snum = assetForm.querySelector("input#snum"); 
if (snum.value.trim() !== "") snum.readOnly = true;

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  fillForm(Array.isArray(assetData) ? assetData[0] : assetData);
});

main.addEventListener("click", async (e) => {
  if (e.target.closest(".assign-btn")) {
    const btn = e.target.closest(".assign-btn");
    
    switch (btn.value) {
      case "assign":
        relayPage("assign-user", {
          "redirect": "index.php" + window.location.search,
          "propNums[]": asset.PropNum,
        });
        break;
      case "reassign":
        relayPage("assign-user", {
          "redirect": "index.php" + window.location.search,
          "propNums[]": asset.PropNum,
          "reassign": true,
        });
        break;
      case "return":
        relayPage("return-form", {
          "redirect": "index.php" + window.location.search,
          "propNums[]": asset.PropNum,
        });
        break;
      case "condemn":
        if (confirm("Condemn asset?")) {
          await condemnAsset(asset.PropNum);
          location.reload();
        }
        break;
    }
  }
});

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

function addAssignmentButtons() {
  if (asset.Status === "Unassigned") {
    const btn = document.createElement("button");
    btn.className = "assign-btn";
    btn.value = "assign";
    btn.innerHTML = `
      <span class="material-icons">assignment_ind</span>
      Assign
    `;
    main.appendChild(btn);
  }
  if (asset.Status === "Assigned") {
    const btn = document.createElement("button");
    btn.className = "assign-btn";
    btn.value = "reassign";
    btn.innerHTML = `
      <span class="material-icons">assignment_ind</span>
      Reassign
    `;
    main.appendChild(btn);

    const btn2 = document.createElement("button");
    btn2.className = "assign-btn";
    btn2.value = "return";
    btn2.innerHTML = `
      <span class="material-icons">assignment_return</span>
      Return    
    `;
    main.appendChild(btn2);
  }
  if (asset.Status === "ToCondemn") {
    const btn = document.createElement("button");
    btn.className = "assign-btn";
    btn.value = "condemn";
    btn.innerHTML = `
      <span class="material-icons">block</span>
      Condemn    
    `;
    main.appendChild(btn);
  }
}

import { tableData, setAssetSelectFunc } from "./asset-table.js";

const assetTable = document.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");

// Override Select Button behavior
setAssetSelectFunc((propNum) => {
  sessionStorage.setItem("assetsToAssign", JSON.stringify([propNum]));
  window.location.href = `${window.location.origin}/index.php?page=assignment-form`;
});

document.getElementById("export")?.remove();

assetTableBody.addEventListener("assetsLoaded", () => {
  for (const tr of assetTableBody.querySelectorAll("tr")) {
    tr.lastElementChild.innerHTML = "";
  }
  addActionsButton();
});

function addActionsButton() {
  const hr = assetTable.querySelector("thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  for (const tr of assetTableBody.querySelectorAll("tr")) {
    if (tableData.get(tr.dataset.propNum).Status !== "Unassigned"){
      continue;
    }

    tr.lastElementChild.innerHTML = `
      <button class="select-btn">
        Select
      </button>
    `;
  }
}

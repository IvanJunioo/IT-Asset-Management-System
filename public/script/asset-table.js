import { relayPage } from "./nav.js";
import { searchAssets } from "./api.js";

const leftAsset = document.querySelector(".left-page");
const tableContainer = leftAsset.querySelector(".table-container");
const assetTable = tableContainer.querySelector(".asset-table");
const assetTableBody = assetTable.querySelector("tbody");
const searchInput = document.getElementById("search-input");
// const filterBox = document.getElementsByClassName("filter-box");
const exportButton = document.getElementById("export");

export let tableData = new Map();
let latest = 0; // latest fetch id to avoid race conditions
let currentSortKey = "PropNum"; // track which column is sorted
let sortOrder = "asc";
let assetSelectFunc = (propNum) => relayPage("asset-view", {"propNum": propNum});

// Immediately add header for actions
const hr = assetTable.querySelector("thead tr");
if (!hr.querySelector("#actionsth")) {
  const actionsth = document.createElement("th");
  actionsth.id = "actionsth";
  hr.appendChild(actionsth);
} 

const tableFuncs = document.createElement("div");
tableFuncs.className = "table-func";
tableFuncs.innerHTML = `
  <button id="reverse-sort">
    <span class="material-icons">north</span>
    Reverse
  </button>
  <button id="sort-by">
    <span class="material-icons"> sort </span>
    Sort by
  </button>

  <div id="sort-menu" class="sort-menu">
    <a class="menu-item" data-sort="PropNum">Property No</a>
    <a class="menu-item" data-sort="ProcNum">Procurement No</a>
    <a class="menu-item" data-sort="PurchaseDate">Purchase Date</a>
    <a class="menu-item" data-sort="Price">Price</a>
    <a class="menu-item" data-sort="AssignedTo">Assigned User</a>
  </div>
`;
leftAsset.insertBefore(tableFuncs, tableContainer);

// ----- EVENT LISTENERS (KEEP MINIMAL) -----
window.addEventListener("pageshow", (_) => {  // Triggers after window loads form state cache (i.e. after going back a window)
  fetchAssets();
});

document.addEventListener("click", (e) => {
  const sortBtn = e.target.closest("#sort-by");
  if (sortBtn) {
    e.stopPropagation();
    const menu = document.getElementById("sort-menu");
    const isVisible = menu.style.display === "flex";

    document.querySelectorAll(".sort-menu").forEach(m => m.style.display = "none");

    if (!isVisible) {
      const boundingRect = sortBtn.getBoundingClientRect();
      const gap = 8;

      menu.style.visibility = "hidden";
      menu.style.display = "flex";
      const menuWidth = menu.offsetWidth;
      menu.style.visibility = "";

      const overflowsRight = boundingRect.right + gap + menuWidth > window.innerWidth;

      menu.style.top = `${boundingRect.top - gap}px`;

      if (overflowsRight) {
        menu.style.left = `${boundingRect.left - gap - menuWidth}px`;
      } else {
        menu.style.left = `${boundingRect.right + gap}px`;
      }

      menu.style.display = "flex";
    }
    return;
  }

  const menuBtn = e.target.closest(".menu-item[data-sort]");
  if (menuBtn) {
    currentSortKey = menuBtn.dataset.sort;
    sortAssets();
    showAssets();
  }

  const reverseBtn = e.target.closest("#reverse-sort");
  if (reverseBtn) {
    reverseBtn.classList.toggle('active');
    sortOrder = sortOrder === "asc" ? "desc" : "asc";
    sortAssets();
    showAssets();
    reverseBtn.querySelector(".material-icons").textContent = sortOrder === "asc"? "north": "south";
  }

  document.querySelectorAll(".sort-menu").forEach(menu => {
    menu.style.display = "none";
  });

});

searchInput.addEventListener("input", fetchAssets)

document.querySelectorAll(".filter-box .body-filter").forEach(box => {
  box.addEventListener("change", fetchAssets);
});

document.querySelectorAll(".reset-filter").forEach(btn => {
  btn.addEventListener("click", () => {
    document.querySelectorAll(".filter-box input[name='status']").forEach(cb => cb.checked = false);
    document.getElementById("date-from").value = "";
    document.getElementById("date-to").value = "";
    fetchAssets();
  });
});

exportButton.addEventListener("click", () => {
  window.open(
    `${window.location.origin}/api/index.php?resource=export&action=user-assets`,
    "_blank"
  );
})

assetTableBody.addEventListener("click", (e) => {
  const tr = e.target.closest("tr");
  if (!tr) return;

  if (e.target.closest(".select-btn")) {
    assetSelectFunc(tr.dataset.propNum);
    return;
  }
});

// ----- FUNCTION DEFINITIONS -----
async function fetchAssets() {    
  const dateFrom = document.getElementById("date-from").value;
  const dateTo = document.getElementById("date-to").value;

  const date = new Date();
  const today = `${date.getFullYear().toString()}-${(date.getMonth() + 1).toString().padStart(2,'0')}-${date.getDate().toString().padStart(2,'0')}`;

  const fetchID = ++latest;
  try {
    const data = await searchAssets({
      search: searchInput.value,
      status: [...new Set([...document.querySelectorAll(".filter-box input[name='status']:checked")].map(cb => cb.value))],
      base_date: dateFrom || "0001-01-01",
      end_date: dateTo || today,
    });

    if (fetchID !== latest) return;
  
    tableData = new Map(data.map(asset => [asset.PropNum, asset]));
    
    showAssets();
  }
  catch (err) {
    console.error("Error fetching assets:", err);
  }
}

function showAssets() {
  for (const tableFunc of tableFuncs.querySelectorAll("button")) {
    tableFunc.disabled = tableData.size <= 0;
  }  
  
  if (tableData.size <= 0) {
    assetTableBody.innerHTML = `
      <tr>
        <td colSpan="${assetTable.querySelector("thead tr").children.length}"> No assets to display. </td>
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
  
  for (const [_, asset] of tableData) {
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
      asset.Assignee,   
    ]) {
      const td = document.createElement("td");
      td.innerHTML = col;
      tr.appendChild(td);
    }

    // view button
    const viewBtn = document.createElement("button");
    viewBtn.className = "select-btn";
    viewBtn.textContent = "View";
    const td = document.createElement("td");
    td.append(viewBtn);
    tr.append(td);
    assetTableBody.appendChild(tr);
  }

  highlightSearch();
  sortAssets();
  assetTableBody.dispatchEvent(new CustomEvent("assetsLoaded"));
}

function sortAssets() {
  const rows = [...assetTableBody.querySelectorAll("tr")];
  rows.sort((a, b) => {
    let valA = tableData.get(a.dataset.propNum)[currentSortKey] || "";
    let valB = tableData.get(b.dataset.propNum)[currentSortKey] || "";

    if (currentSortKey === "Price") {
      return sortOrder === "asc" ? Number(valA) - Number(valB) : Number(valB) - Number(valA);
    }

    const dateA = Date.parse(valA);
    const dateB = Date.parse(valB);
    if (!isNaN(dateA) && !isNaN(dateB)) {
      return sortOrder === "asc" ? dateA - dateB : dateB - dateA;
    }

    if (currentSortKey === "AssignedTo") {
      valA = tableData.get(a.dataset.propNum)["Assignee"];
      valB = tableData.get(b.dataset.propNum)["Assignee"];
      if (valA > valB) return sortOrder === "asc" ? -1 : 1;
      if (valA < valB) return sortOrder === "asc" ? 1 : -1;
    }
    if (valA < valB) return sortOrder === "asc" ? -1 : 1;
    if (valA > valB) return sortOrder === "asc" ? 1 : -1;
    return 0;
  });
  for (const tr of rows) assetTableBody.appendChild(tr);
}

function highlightSearch() {
  const search = searchInput.value.trim();
  if (!search) return;
  const regex = new RegExp(`(${search})`, "gi");
  const headerIDs = [...assetTable.querySelectorAll("thead th")].map((th) => th.id);

  const dfs = (node) => {
    if (node.nodeType === Node.TEXT_NODE) {
      const span = document.createElement("span");
      span.innerHTML = node.nodeValue.replace(regex, "<mark>$1</mark>");
      node.replaceWith(...span.childNodes);
      return;
    }
    for (const child of node.childNodes) dfs(child); 
  };

  for (const tr of assetTableBody.querySelectorAll("tr")) {
    for (const td of tr.querySelectorAll("td")) {
      if (!["pnum","prnum","specs"].includes(headerIDs[td.cellIndex])) continue;
      dfs(td);
    }
  }
}

export function setAssetSelectFunc(fn) {assetSelectFunc = fn;}

import { viewAsset } from "./asset-router.js";
import { editUser } from "./user-router.js";

const table = document.getElementById("actlog-table");
const tbody = table.querySelector("tbody");
const paginationDiv = document.getElementById("pagination"); 

let latest = 0; // latest fetch id to avoid race conditions
const rowsPerPage = 10;

export async function fetchLogs(search = "") {
  const fetchID = ++latest;

  const url = new URL(`${window.location.origin}/public/api/index.php`)
  url.search = new URLSearchParams({
    resource: "logs",
    action: "search",
    search: search,
    page: paginationDiv.dataset.curPage,
    limit: rowsPerPage,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
    
    const data = await resp.json();
    
    if (fetchID !== latest) return;
    showLogs(data);
  } catch (err) {
    console.error("Error fetching system logs: ", err);
  }
}

function showLogs(data) {
  tbody.innerHTML = "";

  for (const log of data["logs"]) {
    const tr = document.createElement("tr");
    
    const metadata = JSON.parse(log.Metadata);

    const objID = {
      "asset": metadata["propNum"],
      "user": log.objName,
    }[metadata["object"]];

    // Store data to row
    tr.dataset.time = log.Timestamp;
    tr.dataset.actorid = log.ActorID;
    tr.dataset.objid = objID;
    tr.dataset.object = metadata["object"];

    const action = {
      "modify": "modified",
      "deactivate": "deactivated"
    }[metadata["action"]] || `${metadata["action"]}ed`;
    
    for (const col of [
      log.Timestamp,
      `<a data-type="actor">${log.FName} ${log.LName}</a>`,
      `<a data-type="actor">${log.FName[0].toUpperCase()}. ${log.LName}</a> ${action} ${metadata["object"]} <a data-type="${metadata["object"]}">${objID}</a>`,
    ]) {
      const td = document.createElement("td");
      td.innerHTML = col;
      tr.appendChild(td);
    }

    tbody.appendChild(tr);
  }

  const curPage = Number(paginationDiv.dataset.curPage);
  const totalPage = Math.ceil(data["count"] / rowsPerPage);
  paginationDiv.dataset.totalPage = totalPage;
  document.getElementById("prev").disabled = curPage === 1 || totalPage === 0;
  document.getElementById("next").disabled = curPage === totalPage || totalPage === 0;
  document.getElementById("page-info").textContent = `Page ${curPage} of ${totalPage}`;
}

document.addEventListener("DOMContentLoaded", () => {
  tbody.querySelector("td").colSpan = table.querySelector("thead tr").children.length;

  paginationDiv.dataset.curPage = 1;
  fetchLogs();

  table.addEventListener("click", (e) => {
    const tr = e.target.closest("tr");
    if (!tr) return;

    const a = e.target.closest("a");
    if (!a) return;

    switch (a.dataset.type) {
      case "actor":
        editUser(tr.dataset.actorid);
        break;
      case "asset":
        viewAsset(tr.dataset.objid);
        break;
      case "user":
        editUser(tr.dataset.objid)
        break;
      default:
        console.warn(`Unknown object type: ${a.dataset.type}`);
    }
  });

  paginationDiv.addEventListener("click", (e) => {
    if (e.target.closest("#prev")) {
      if (1 < paginationDiv.dataset.curPage) {
        paginationDiv.dataset.curPage--;
        fetchLogs();
      }
    }
    
    if (e.target.closest("#next")) {
      if (paginationDiv.dataset.curPage<paginationDiv.dataset.totalPage) {
        paginationDiv.dataset.curPage++;
        fetchLogs();
      }
    }
  });
});

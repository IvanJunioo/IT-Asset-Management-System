import { viewAsset } from "./asset-router.js";
import { editUser } from "./user-router.js";

const table = document.getElementById("actlog-table");
const tbody = table.querySelector("tbody");
const paginationDiv = document.getElementById("pagination"); 

export let tableData = new Map();
let totalLogs = 0;
let latest = 0; // latest fetch id to avoid race conditions
const rowsPerPage = 10;

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
      editUser(tableData.get(Number(tr.dataset.logid)).ActorID);
      break;
    case "asset":
      viewAsset(tr.dataset.objid);
      break;
    case "user":
      editUser(Number(tr.dataset.objid))
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

export async function fetchLogs({
  message = "", 
  actorID = null, 
  metadata = "",
} = {}) {
  const fetchID = ++latest;

  const url = new URL(`${window.location.origin}/api/index.php`)
  url.search = new URLSearchParams({
    resource: "logs",
    action: "search",
    actorID: actorID,
    message: message,
    metadata: metadata,
    page: paginationDiv.dataset.curPage,
    limit: rowsPerPage,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
    
    const data = await resp.json();
    tableData = new Map(data["logs"].map(log => [log.LogID, log]));
    totalLogs = Number(data["count"]);
    
    if (fetchID !== latest) return;
    showLogs();
  } catch (err) {
    console.error("Error fetching system logs: ", err);
  }
}

function showLogs() {
  if (tableData.size <= 0) {
    tbody.innerHTML = `
      <tr>
        <td colSpan="${table.querySelector("thead tr").children.length}" style="text-align: center;"> No logs to display. </td>
      </tr>
    `;
    return;
  }

  tbody.innerHTML = "";

  for (const [_, log] of tableData) {
    const tr = document.createElement("tr");
    
    const metadata = JSON.parse(log.Metadata);

    const objID = {
      "asset": metadata["propNum"],
      "user": metadata["empID"],
    }[metadata["object"]];

    const objName = {
      "asset": metadata["propNum"],
      "user": log.objName,
    }[metadata["object"]];

    // Store id
    tr.dataset.logid = log.LogID;
    tr.dataset.objid = objID;

    const action = {
      "modify": "modified",
      "deactivate": "deactivated",
      "activate" : "activated"
    }[metadata["action"]] || `${metadata["action"]}ed`;

    const extra = metadata.hasAssets? " (has assigned assets)" : "";

    const priv = JSON.parse(sessionStorage.getItem("user-info")).Privilege;
    const actorHTML = text => ["SuperAdmin"].includes(priv)? `<a data-type="actor">${text}</a>`: text;
    const objHTML = text => metadata["object"] === "user" && !["SuperAdmin"].includes(priv)? text : `<a data-type="${metadata["object"]}">${text}</a>`;
    
    for (const col of [
      log.Timestamp,
      actorHTML(`${log.FName} ${log.LName}`),
      `${actorHTML(`${log.FName[0].toUpperCase()}. ${log.LName}`)} ${action} ${metadata["object"]} ${objHTML(objName)} ${extra}`,
    ]) {
      const td = document.createElement("td");
      td.innerHTML = col;
      tr.appendChild(td);
    }

    tbody.appendChild(tr);
  }

  const curPage = Number(paginationDiv.dataset.curPage);
  const totalPage = Math.ceil(totalLogs / rowsPerPage);
  paginationDiv.dataset.totalPage = totalPage;
  document.getElementById("prev").disabled = curPage === 1 || totalPage === 0;
  document.getElementById("next").disabled = curPage === totalPage || totalPage === 0;
  document.getElementById("page-info").textContent = `Page ${curPage} of ${totalPage}`;
}

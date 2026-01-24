const table = document.getElementById("actlog-table");
const tbody = table.querySelector("tbody");

let latest = 0 // latest fetch id to avoid race conditions
let allLogs = [];
let curPage = 1;
let totalPage = 0;
const rowsPerPage = 10;

export function fetchLogs(search = "") {
  const fetchID = ++latest;

  fetch("../../src/handlers/act-log.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${search}`,
  })
  .then(res => res.json())
  .then(data => {
    if (fetchID !== latest) return;

    allLogs = data;
    curPage = 1;
    totalPage = Math.ceil(allLogs.length/rowsPerPage)
    showLogs();
  })
  .catch(err => console.error("Error fetching system logs: ", err));
}

function showLogs(){
  renderLogs();
  updatePrevNext();
}

function renderLogs() {
  tbody.innerHTML = "";

  const start = (curPage-1)*rowsPerPage;
  const end = start+rowsPerPage;
  const logs = allLogs.slice(start,end);

  for (const log of logs) {
    const tr = document.createElement("tr");
    
    // Store data to row
    tr.dataset.time = log.Timestamp;
    tr.dataset.empID = log.ActorID;
    tr.dataset.desc = log.Log;
    
    for (const col of [
      log.Timestamp,
      log.ActorID,
      log.Log,
    ]) {
      const td = document.createElement("td");
      td.textContent = col;
      tr.appendChild(td);
    }

    tbody.appendChild(tr);
  }
}

document.addEventListener("DOMContentLoaded", () => {
  fetchLogs();
});

const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");
const pageInfo = document.getElementById("page-info");

function updatePrevNext(){
  pageInfo.textContent = `Page ${curPage} of ${totalPage}`;
  prevBtn.disabled = curPage === 1;
  nextBtn.disabled = curPage === totalPage || totalPage === 0;
}

prevBtn.addEventListener("click", () => {
  if (curPage>1) {
    curPage--;
    showLogs();
  }
});

nextBtn.addEventListener("click", () => {
  if (curPage<totalPage) {
    curPage++;
    showLogs();
  }
});



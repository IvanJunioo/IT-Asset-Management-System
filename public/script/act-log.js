const table = document.getElementById("actlog-table");
const tbody = table.querySelector("tbody");
const paginationDiv = document.getElementById("pagination"); 

let latest = 0; // latest fetch id to avoid race conditions
const rowsPerPage = 10;

export function fetchLogs(search = "") {
  const fetchID = ++latest;

  fetch("../../src/handlers/act-log.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `page=${paginationDiv.dataset.curPage}&limit=${rowsPerPage}&search=${search}`,
  })
  .then(res => res.json())
  .then(data => {
    if (fetchID !== latest) return;

    showLogs(data);
  })
  .catch(err => console.error("Error fetching system logs: ", err));
}

function showLogs(data) {
  tbody.innerHTML = "";

  for (const log of data["logs"]) {
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

  const curPage = paginationDiv.dataset.curPage;
  const totalPage = Math.ceil(data["count"] / rowsPerPage);
  paginationDiv.dataset.totalPage = totalPage;
  paginationDiv.querySelector("#prev").disabled = curPage === 1;
  paginationDiv.querySelector("#next").disabled = curPage === totalPage || totalPage === 0;
  paginationDiv.querySelector("#page-info").textContent = `Page ${curPage} of ${totalPage}`;
}

document.addEventListener("DOMContentLoaded", () => {
  paginationDiv.dataset.curPage = 1;
  fetchLogs();

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

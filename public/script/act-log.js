const table = document.getElementById("actlog-table");
const tbody = table.querySelector("tbody");

let latest = 0 // latest fetch id to avoid race conditions

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
    showLogs(data);
  })
  .catch(err => console.error("Error fetching system logs: ", err));
}

function showLogs(logs) {
  tbody.innerHTML = "";

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

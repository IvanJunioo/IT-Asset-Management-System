document.addEventListener("DOMContentLoaded", () => {
  const tbody = document.querySelector("tbody");

  fetchLogs();
  
  function fetchLogs() {
    fetch("../handlers/act-log.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
    })
    .then(res => res.json())
    .then(data => {
      showLogs(data);
    })
    .catch(err => console.error("Error fetching system logs: ", err));
  }

  function showLogs(logs) {
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
});
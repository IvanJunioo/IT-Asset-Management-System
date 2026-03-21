import { fetchLogs } from "./act-log.js";

const cardDiv = document.querySelector(".user-card");
const headerDiv = cardDiv.querySelector(".log-header");

const userData = JSON.parse(sessionStorage.getItem("userData"));
const logActor = Array.isArray(userData) ? userData[0] : userData;

headerDiv.querySelector(".user-name").textContent = `${logActor.FName} ${logActor.LName}`;

const userBadge = document.createElement("span");
userBadge.className = `badge ${logActor.ActiveStatus.toLowerCase()}`;
userBadge.textContent = logActor.ActiveStatus;
headerDiv.querySelector(".user-info").appendChild(userBadge);

fetchLogs(logActor.EmpID);

cardDiv.addEventListener("click", (e) => {
  if (e.target.closest(".back-link")) {
    window.history.back();
  }
});

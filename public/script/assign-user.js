const userTable = document.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

function addActionsButton() {
  const hr = userTable.querySelector("thead tr");
  if (!hr.querySelector("#actionsth")) {
    const actionsth = document.createElement("th");
    actionsth.id = "actionsth";
    hr.appendChild(actionsth);
  }

  for (const tr of userTableBody.querySelectorAll("tr")) {
    const actionElem = document.createElement("td");
    actionElem.className = "actions";    
    if (tr.dataset.activeStatus == "Active") {
      actionElem.innerHTML = `
        <button class="select-btn">
          select
        </button>
      `;
    }

    tr.appendChild(actionElem);
  }
}

userTableBody.addEventListener("usersLoaded", () => {
  addActionsButton();

  // Handles all table clicks dynamically
  userTable.addEventListener("click", (e) => {
    if (e.target.closest(".select-btn")) {
      const tr = e.target.closest("tr");      
      sessionStorage.setItem("assignToUser", JSON.stringify(tr.dataset.empID));
      window.location.href = "../views/assignment-form.php";
      return;
    }
  });
});
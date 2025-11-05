const userTable = document.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

document.addEventListener("DOMContentLoaded", () => {
  userTable.querySelector("thead tr").appendChild(document.createElement("th"));

});

userTableBody.addEventListener("usersLoaded", () => {
  const rows = userTableBody.querySelectorAll("tr");

  addActionsButton();

	function addActionsButton() {
    for (const row of rows) {
      row.innerHTML += `
      <td class="select">
        <button class="select-btn">
          select
        </button>
      </td>
      `;
    }

    document.querySelectorAll(".select-btn").forEach(btn => {
      btn.addEventListener("click", (e) => {
        const row = e.target.closest("tr");
				const cells = row.querySelectorAll("td");
				const empID = cells[0].textContent.trim();
				sessionStorage.setItem("assignToUser", JSON.stringify(empID));
				window.location.href = "../views/assignment-form.php";
        });
    });

  }
});
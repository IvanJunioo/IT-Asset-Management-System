const userTable = document.querySelector(".user-table");
const userTableBody = userTable.querySelector("tbody");

userTableBody.addEventListener("usersLoaded", () => {
  const rows = userTableBody.querySelectorAll("tr");

  addActionsButton();

	function addActionsButton() {
    userTable.querySelector("thead tr").appendChild(document.createElement("th"));

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
				localStorage.setItem("assignToUser", JSON.stringify(empID));
				window.location.href = "../views/assignment-form.php";
        });
    });

    // document.addEventListener("click", () => {
    //   document.querySelectorAll(".action-menu").forEach(menu => {
    //     menu.style.display = "none";
    //   });
    // });
  }

	// const selectButton = document.getElementById('select-btn');
	// selectButton.addEventListener('click', (e) => {
	// 	const row = e.target.closest("tr");
	// 	const cells = row.querySelectorAll("td");
	// 	const empID = cells[0].textContent.trim();
	// 	localStorage.setItem("assignToUser", JSON.stringify(empID));
	// 	window.location.href = "../views/assignment-form.php";
	// })

});
document.addEventListener("DOMContentLoaded", () => {
	const userTableBody = document.querySelector('.assign-user-table tbody');
  const currentPage = window.location.pathname;

  function fetchUsers() {
    const src = "../handlers/user-table.php";
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent('')}&priv=${encodeURIComponent([])}`,
    })
    .then(res => res.json())
    .then(data => showUsers(data))
    .catch(err => console.error("Error fetching users: ", err));
  }
  
  function showUsers(users) {
    userTableBody.innerHTML = "";
    
    for (const user of users) {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${user.EmpID}</td>
        <td>${user.EmpMail}</td>
        <td>${user.FName}</td>
        <td>${user.MName}</td>
        <td>${user.LName}</td>
        <td>${user.Privilege} </td>
        <td>${user.ActiveStatus} </td>
      `;
			userTableBody.appendChild(tr);
		}
    	if (currentPage.includes("assign-user")) {
        addActionButton();
      }
    }

	function addActionButton() {
    const rows = document.querySelectorAll("tbody tr");

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

  fetchUsers();

	// const selectButton = document.getElementById('select-btn');
	// selectButton.addEventListener('click', (e) => {
	// 	const row = e.target.closest("tr");
	// 	const cells = row.querySelectorAll("td");
	// 	const empID = cells[0].textContent.trim();
	// 	localStorage.setItem("assignToUser", JSON.stringify(empID));
	// 	window.location.href = "../views/assignment-form.php";
	// })

});
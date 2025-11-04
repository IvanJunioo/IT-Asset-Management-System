document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const userTableBody = document.querySelector('.user-table tbody');
  const privilege = document.getElementById("body-filter").querySelectorAll('input[name="privilege"]');
  const resetFilterButton = document.getElementById("filter-box").querySelector("button[id='apply-filter']");
  const multiSelectButton = document.getElementById("multi-select");
  const currentPage = window.location.pathname;

  function fetchUsers() {
    const src = "../handlers/user-table.php";

    const searchFilters = searchInput.value;
    
    const privFilters = [];
    for (const cb of privilege) {
      if (!cb.checked) continue;
      if (cb.id === "faculty") privFilters.push("Faculty");
      if (cb.id === "admin") privFilters.push("Admin");
      if (cb.id === "superadmin") privFilters.push("SuperAdmin");
    }
  
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent(searchFilters)}&priv=${encodeURIComponent(privFilters)}`,
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
        <td data-col="EmpID">${user.EmpID}</td>
        <td data-col="EmpMail">${user.EmpMail}</td>
        <td data-col="FName">${user.FName}</td>
        <td data-col="MName">${user.MName}</td>
        <td data-col="LName">${user.LName}</td>
        <td data-col="Privilege">${user.Privilege} </td>
        <td data-col="ActiveStatus">${user.ActiveStatus} </td>
      `;
			userTableBody.appendChild(tr);
		}
    	if (currentPage.includes("user-manager")) {
				const menuButtons = document.getElementsByClassName("menu-item");

        addActionButton();

				Array.from(menuButtons).forEach(btn => {
					btn.addEventListener("click", (e) => {
						const row = e.target.closest("tr");
						const cells = row.querySelectorAll("td");
						const empid = cells[0].textContent.trim();
						switch (btn.id) {
							case "modify-action":
								editUsers(empid);
								break;
							case "delete-action":
								deleteUsers(empid);
								break;
							default: 
						}
					})
				})
      }
    }
    
    // if (currentPage.includes("user-manager")) {
    //   document.querySelectorAll(".action-btn").forEach(btn => {
    //     btn.addEventListener("click", (e) => {
    //         e.stopPropagation();
    //         const menu = btn.parentElement.querySelector(".action-menu");
    //         if (menu.style.display == "flex") {
    //           menu.style.display = "none";
    //         } else {
    //           menu.style.display = "flex";
    //         }
    //       });
    //   });

    //   document.addEventListener("click", () => {
    //     document.querySelectorAll(".action-menu").forEach(menu => {
    //       menu.style.display = "none";
    //     });
    //   });
    // }

	function editUsers(filter){
    const src = "../handlers/edit-user.php";

    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    })
    .then(res => res.json())
    .then(data => {
      localStorage.setItem("userData", JSON.stringify(data));
      window.location.href = "../views/edit-user-form.php";
    }
    )
    .catch(err => console.error("Error edit users: ", err))
  }

	function deleteUsers(filter){
    const src = "../handlers/delete-user.php";
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${filter}`,
    }).then(_ => {
      window.location.href = "../views/user-manager.php";
    })
  }


	function addActionButton() {
    const rows = document.querySelectorAll("tbody tr");

    for (const row of rows) {
      row.innerHTML += `
      <td class="actions">
        <button class="action-btn">
          <span class="material-icons">more_horiz</span>
        </button>
        
        <div class="action-menu">
          <a class="menu-item" id="modify-action">Modify</a>
          <a class="menu-item" id="delete-action">Delete</a>
          <a class="menu-item" id="assign-action">Assign</a>
        </div>
      </td>
      `;
    }

    document.querySelectorAll(".action-btn").forEach(btn => {
      btn.addEventListener("click", (e) => {
          e.stopPropagation();
          const menu = btn.parentElement.querySelector(".action-menu");
          if (menu.style.display == "flex") {
            menu.style.display = "none";
          } else {
            menu.style.display = "flex";
          }
        });
    });

    document.addEventListener("click", () => {
      document.querySelectorAll(".action-menu").forEach(menu => {
        menu.style.display = "none";
      });
    });
  }

  fetchUsers();
  
  searchInput.addEventListener("input", () => {
    fetchUsers();
  });

  searchButton.addEventListener("click", () => {
    fetchUsers()
    searchInput.value = "";
  });

  privilege.forEach(cb => cb.addEventListener("change", fetchUsers))

  resetFilterButton.addEventListener("click", () => {
    privilege.forEach(cb => cb.checked = false);
    fetchUsers();
  });

  // multiSelectButton.addEventListener("click", () => {
  //   const icon = multiSelectButton.querySelector(".material-icons");
  //   icon.textContent = icon.textContent.includes("check_box_outline_blank")
  //     ? "check_box"
  //     : "check_box_outline_blank";
  // });

	

  // document.querySelectorAll(".action-btn").forEach(btn => {
  //   btn.addEventListener("click", (e) => {
  //       e.stopPropagation();
  //       const actionsCell = btn.closest(".actions");
  //       actionsCell.classList.toggle("show-menu");
  //     });
  // });

  // document.addEventListener("click", () => {
  //     document.querySelectorAll(".actions.show-menu")
  //     .forEach(cell => cell.classList.remove("show-menu"));
  // });
})  

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const userTableBody = document.querySelector('.user-table tbody');
  const multiSelectButton = document.getElementById("multi-select");
  const currentPage = window.location.pathname;

  function fetchUsers(filters = "") {
    const src = "../handlers/user-table.php";
  
    fetch(src, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent(filters)}`,
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
      tr += currentPage.includes("user-manager") ? `
      <td class="actions">
        <button class="action-btn">
          <span class="material-icons">more_horiz</span>
        </button>

        <div class="action-menu">
          <a href="asset-form.php" class="menu-item" id="modify-action">Modify</a>
          <a href="asset-form.php" class="menu-item" id="delete-action">Delete</a>
        </div>
      </td>` : ``
      userTableBody.appendChild(tr);
    }
  }
  
  fetchUsers();
  
  searchInput.addEventListener("input", () => {
    fetchUsers(searchInput.value);
  });

  searchButton.addEventListener("click", () => {
    fetchUsers(searchInput.value)
    searchInput.value = "";
  });

  multiSelectButton.addEventListener("click", () => {
    const icon = multiSelectButton.querySelector(".material-icons");
    icon.textContent = icon.textContent.includes("check_box_outline_blank")
      ? "check_box"
      : "check_box_outline_blank";
  });

  document.querySelectorAll(".action-btn").forEach(btn => {
    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        const actionsCell = btn.closest(".actions");
        actionsCell.classList.toggle("show-menu");
      });
  });

  document.addEventListener("click", () => {
      document.querySelectorAll(".actions.show-menu")
      .forEach(cell => cell.classList.remove("show-menu"));
  });
})  

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const userTableBody = document.querySelector('.user-table tbody');
  const filterBox = document.getElementById("filter-box");

  function fetchUsers() {
    const searchFilters = searchInput.value;
    const privFilters = [...filterBox.querySelectorAll("input[name='privilege']:checked")].map(cb => cb.value);
      
    fetch("../../src/handlers/user-table.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `search=${encodeURIComponent(searchFilters)}&priv=${encodeURIComponent(privFilters)}`,
    })
    .then(res => res.json())
    .then(data => {
      showUsers(data);
      userTableBody.dispatchEvent(new CustomEvent("usersLoaded"));
    })
    .catch(err => console.error("Error fetching users: ", err));
  }
  
  function showUsers(users) {
    userTableBody.innerHTML = "";
    
    for (const user of users) {
      const tr = document.createElement('tr');

      // store user data locally
      tr.dataset.empID = user.EmpID;
      tr.dataset.empMail = user.EmpMail;
      tr.dataset.fName = user.FName;
      tr.dataset.mName = user.MName;
      tr.dataset.lName = user.LName;
      tr.dataset.privilege = user.Privilege;
      tr.dataset.activeStatus = user.ActiveStatus;

      for (const col of [
        user.EmpID,
        user.EmpMail,
        user.FName,
        user.MName,
        user.LName,
        user.Privilege,
        user.ActiveStatus,
      ]) {
        const td = document.createElement("td");
        td.textContent = col;
        tr.appendChild(td);
      }

			userTableBody.appendChild(tr);
		}
  }
    
  fetchUsers();
  
  searchInput.addEventListener("input", () => {
    fetchUsers();
  });

  searchButton.addEventListener("click", () => {
    fetchUsers()
    searchInput.value = "";
  });

  filterBox.querySelector("#body-filter").addEventListener("change", fetchUsers);

  filterBox.querySelector("button[id='apply-filter']").addEventListener("click", () => {
    filterBox.querySelectorAll('input[name="privilege"]').forEach(cb => cb.checked = false);
    fetchUsers();
  });
});

document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const userTableBody = document.querySelector('.user-table tbody');
  const filterBox = document.getElementById("filter-box");

  function fetchUsers() {
    const src = "../handlers/user-table.php";

    const searchFilters = searchInput.value;
    
    const privFilters = [];
    for (const cb of filterBox.querySelectorAll('input[name="privilege"]')) {
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
  }
    
  fetchUsers();
  
  searchInput.addEventListener("input", () => {
    fetchUsers();
  });

  searchButton.addEventListener("click", () => {
    fetchUsers()
    searchInput.value = "";
  });

  filterBox.querySelectorAll('input[name="privilege"]').forEach(cb => cb.addEventListener("change", fetchUsers))

  filterBox.querySelector("button[id='apply-filter']").addEventListener("click", () => {
    filterBox.querySelectorAll('input[name="privilege"]').forEach(cb => cb.checked = false);
    fetchUsers();
  });
});

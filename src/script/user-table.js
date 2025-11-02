document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("search-input");
  const searchButton = document.getElementById("search-button");
  const userTableBody = document.querySelector('.user-table tbody');
  
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
})  

export function editUser(empid){
  fetch(`${window.location.origin}/src/handlers/edit-user.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${empid}`,
  })
  .then(res => res.json())
  .then(data => {
    sessionStorage.setItem("userData", JSON.stringify(data));
    window.location.href = `${window.location.origin}/public/views/edit-user-form.php`;
  })
  .catch(err => console.error("Error editing user: ", err));
}

export function deleteUser(empid){
  fetch(`${window.location.origin}/src/handlers/delete-user.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${empid}`,
  })
  .then(_ => {
    window.location.href = `${window.location.origin}/public/views/user-manager.php`;
  })
  .catch(err => console.error("Error deleting user: ", err));
}

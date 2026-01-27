document.addEventListener("DOMContentLoaded", () => {
  fetch("../../src/handlers/login-url.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
  })
  .then(res => res.json())
  .then(data => {
    const loginA = document.getElementById("login-upmail");
    loginA.href = data;
  })
  .catch(err => console.error("Error fetching: ", err));
});
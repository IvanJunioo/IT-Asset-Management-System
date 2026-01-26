document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  form.action = "../../src/handlers/add-user-form.php";
  form.method = "post";  
});

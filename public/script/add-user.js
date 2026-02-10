document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  form.action = `${window.location.origin}/src/handlers/add-user-form.php`;
  form.method = "post";  
});

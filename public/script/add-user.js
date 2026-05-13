import { searchUsers } from "./api.js";

const form = document.querySelector("form");
form.action = `${window.location.origin}/api/index.php?resource=users&action=add&redirect=${encodeURIComponent("index.php?page=user-manager")}`;
form.method = "post";   

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  form.reset();
})

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const email = document.getElementById("e").value;

  let dataEmail = await searchUsers({search: email});

  if (dataEmail[0]) {
    alert(`The email address you entered is already in use. Please try another one.`);
  }
  else {
    form.submit();
  }
})

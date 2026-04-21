const form = document.querySelector("form");
form.action = `${window.location.origin}/api/index.php?resource=users&action=add&redirect=${encodeURIComponent("index.php?page=user-manager")}`;
form.method = "post";   

const resetBtn = document.getElementById("reset-button");
resetBtn?.addEventListener("click", (_) => {
  form.reset();
})

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  const email = document.getElementById("e");

  let dataEmail = await checkIfExists(email);

  if (dataEmail) {
    alert(`The email address you entered is already in use. Please try another one.`);
  }
  else {
    form.submit();
  }
})

async function checkIfExists(input) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "search",
    search: input.value,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
    const data = await resp.json();
    if (data && data.length > 0) return data[0];
  } catch (err) {
    console.error("Error fetching users: ", err);
  }
  
  return null;
}
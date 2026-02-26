document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  form.action = `${window.location.origin}/public/api/index.php?resource=users&action=add`;
  form.method = "post";   

  const resetBtn = document.getElementById("reset-button");
  resetBtn?.addEventListener("click", (_) => {
    form.reset();
  })

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const email = document.getElementById("e");

    let valid = true;
    let dataEmail = await checkIfExists(email);

    if (dataEmail) {
      valid = false;
      alert(`A user with this credentials already exists`);
    }

    if (valid) {
      form.submit();
    }
  })
});


async function checkIfExists(input) {
  const url = new URL(`${window.location.origin}/public/api/index.php`);
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
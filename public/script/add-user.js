document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  form.action = `${window.location.origin}/public/api/index.php?resource=users&action=add`;
  form.method = "post";   

  const resetBtn = document.getElementById("reset-button");
  resetBtn?.addEventListener("click", (_) => {
    form.reset();
  })
});

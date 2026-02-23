document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  form.action = `${window.location.origin}/public/api/index.php?resource=users&action=add`;
  form.method = "post"; 
  
  const date = new Date();
  const today = `${date.getFullYear().toString()}-${(date.getMonth() + 1).toString().padStart(2,'0')}-${date.getDate().toString().padStart(2,'0')}`;
  const pdate = document.getElementById('pdate');
  pdate.setAttribute('max', today);
  pdate.value = today;
});

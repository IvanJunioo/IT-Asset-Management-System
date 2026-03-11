getUrl();

async function getUrl() {
  const url = new URL(`${window.location.origin}/public/api/index.php`);
  url.search = new URLSearchParams({
    resource: "logs",
    action: "logurl",
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

    const data = await resp.json();
    
    document.getElementById("login-upmail").href = data;
  } catch (err) {
    console.error("Error fetching: ", err);
  }
}

<script type="module">
  sessionStorage.setItem("user-info", JSON.stringify(await getSessionUser()));
  window.location.href = `${window.location.origin}/index.php?page=dashboard`;

  async function getSessionUser() {
    const url = new URL(`${window.location.origin}/api/index.php`);
    url.search = new URLSearchParams({
      resource: "users",
      action: "session",
    }); 

    try {
      const resp = await fetch(url);
      if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

      const data = await resp.json();

      return data;
    } catch (err) {
      console.error("Error fetching: ", err);
    }
  }
</script>
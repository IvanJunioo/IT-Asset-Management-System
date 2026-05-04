const sect = document.getElementById("asset-distribution");

document.getElementById("actlog-table").className = "recent-system-logs";

getDBstats();

async function getDBstats() {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assets",
    action: "stats",
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) {
      window.location.href = `${window.location.origin}/index.php?page=error&code=500&message=${encodeURIComponent("Internal Server Error")}&description=${encodeURIComponent("Failed to fetch database stats.")}`;
      return;
    }

    const data = await resp.json();

    const totalAssetCnt = document.createElement("h2");
    totalAssetCnt.textContent = data.assetsTotal;
    sect.querySelector("#total-assets").prepend(totalAssetCnt);

    const availAssetCnt = document.createElement("h2");
    availAssetCnt.textContent = data.assetsAvail;
    sect.querySelector("#avail-assets").prepend(availAssetCnt);
  } catch (err) {
    window.location.href = `${window.location.origin}/index.php?page=error&code=500&message=${encodeURIComponent("Internal Server Error")}&description=${encodeURIComponent("Failed to fetch database stats.")}`;
    return;
  }

  url.search = new URLSearchParams({
    resource: "users",
    action: "stats",
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) {
      window.location.href = `${window.location.origin}/index.php?page=error&code=500&message=${encodeURIComponent("Internal Server Error")}&description=${encodeURIComponent("Failed to fetch database stats.")}`;
      return;
    }

    const data = await resp.json();

    const totalUserCnt = document.createElement("h2");
    totalUserCnt.textContent = data.usersTotal;
    sect.querySelector("#total-users").prepend(totalUserCnt);

    const activeUserCnt = document.createElement("h2");
    activeUserCnt.textContent = data.usersActive;
    sect.querySelector("#active-users").prepend(activeUserCnt);
  } catch (err) {
    window.location.href = `${window.location.origin}/index.php?page=error&code=500&message=${encodeURIComponent("Internal Server Error")}&description=${encodeURIComponent("Failed to fetch database stats.")}`;
    return;
  }
}

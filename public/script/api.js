async function apiRequest(input, init = null) {
  const resp = await fetch(input, init);
  if (!resp.ok) throw new Error(`HTTP Error: ${resp.status}`);
  if (!init) return await resp.json();   
} 

export async function fetchAsset(propNum) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assets",
    action: "fetch",
    search: propNum,
  });
  return await apiRequest(url);
}

export async function condemnAsset(propNum) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assets",
    action: "condemn",
    redirect: "index.php?page=asset-manager",
  });
  await apiRequest(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({search: propNum}),
  });
}

export async function getAssetStats() {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assets",
    action: "stats",
  });

  return await apiRequest(url);
}

export async function fetchUser(empID) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "fetch",
    search: empID,
  });
  return await apiRequest(url);
}

export async function fetchSessionUser() {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "session",
  });
  return await apiRequest(url);
}

export async function modifyUser(empID, actionType) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: actionType,
    redirect: "index.php?page=user-manager",
  });

  await apiRequest(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      empID: empID,
    }),
  });
}

export async function getUserStats() {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "stats",
  });
  return await apiRequest(url);
}

export async function countAssignments(search) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "search",
    search: search,
  });
  const data = await apiRequest(url);
  return data[0]["assignments"].length;
}

export async function fetchLogs({
  message = "", 
  actorID = null, 
  metadata = "",
  page = 1,
  pageSize = 10,
} = {}) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "logs",
    action: "search",
    actorID: actorID || "",
    message: message,
    metadata: metadata,
    page: page,
    limit: pageSize,
  });
  return await apiRequest(url);
}

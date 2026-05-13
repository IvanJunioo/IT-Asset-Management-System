async function apiRequest(params, init = null) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams(params);

  const resp = await fetch(url, init);
  if (!resp.ok) throw new Error(`HTTP Error: ${resp.status}`);
  
  if (!init) return await resp.json();   
} 

export async function fetchAsset(propNum) {
  return await apiRequest({
    resource: "assets",
    action: "fetch",
    search: propNum.trim(),
  });
}

export async function searchAssets({
  search = "", 
  status = "", 
  base_date = "", 
  end_date = "",
  check_snum = false,
}) {
  return await apiRequest({
    resource: "assets",
    action: "search",
    search: search,
    status: status,
    base_date: base_date,
    end_date: end_date,
    check_snum: check_snum,
  });
}

export async function condemnAsset(propNum) {
  return await apiRequest({
    resource: "assets",
    action: "condemn",
    redirect: "index.php?page=asset-manager",
  }, 
  {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({search: propNum}),
  });
}

export async function getAssetStats() {
  return await apiRequest({
    resource: "assets",
    action: "stats",
  });
}

export async function fetchUser(empID) {
  return await apiRequest({
    resource: "users",
    action: "fetch",
    search: empID.trim(),
  });
}

export async function searchUsers({
  search = "",
  status = "",
  priv = "",
}) {
  return await apiRequest({
    resource: "users",
    action: "search",
    search: search,
    status: status,
    priv: priv,
  });
}

export async function fetchSessionUser() {
  return await apiRequest({
    resource: "users",
    action: "session",
  });
}

export async function modifyUser(empID, actionType) {
  return await apiRequest({
    resource: "users",
    action: actionType,
    redirect: "index.php?page=user-manager",
  }, 
  {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      empID: empID,
    }),
  });
}

export async function getUserStats() {
  return await apiRequest({
    resource: "users",
    action: "stats",
  });
}

export async function fetchUserAssignments(empID) {
  return await apiRequest({
    resource: "assignment",
    action: "fetch",
    user: empID,
  });
}

export async function countAssignments(search) {
  const data = await searchUsers({search: search});
  return data[0]["assignments"].length;
}

export async function fetchLogs({
  message = "", 
  actorID = null, 
  metadata = "",
  page = 1,
  pageSize = 10,
} = {}) {
  return await apiRequest({
    resource: "logs",
    action: "search",
    actorID: actorID || "",
    message: message,
    metadata: metadata,
    page: page,
    limit: pageSize,
  });
}

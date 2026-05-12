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

  const resp = await fetch(url);
  if (!resp.ok) throw new Error(`HTTP Error: ${resp.status}`);
  return await resp.json();
}

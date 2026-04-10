export async function fetchAsset(propNum) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assets",
    action: "fetch",
    search: propNum,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

    const data = await resp.json();
    
    return data;
  } catch (err) {
    console.error("Error fetching asset: ", err);
  }
}

export async function condemnAsset(propNum) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "assets",
    action: "condemn",
  });

  try {
    const resp = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({search: propNum}),
    });
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

    window.location.href = `${window.location.origin}/index.php?page=asset-manager`;
  } catch (err) {
    console.error("Error condemning asset: ", err);
  }
}

export function relayPage(pageName, data = {}) {  // Appends data in URL search parameters before going to next page 
  const params = new URLSearchParams(window.location.search); // Preserves old data
  
  params.set("page", pageName); // Rewrites page value
  
  for (const [key, val] of Object.entries(data)) {
    if (Array.isArray(val)) {
      params.delete(key);
      val.forEach(elem => params.append(key, elem));
    }
    else {
      params.set(key, val);
    }
  }
  
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
}

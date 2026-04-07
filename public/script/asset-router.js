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

export async function viewAsset(propNum) {
  const params = new URLSearchParams({
    page: "asset-view",
    propNum: propNum,
  });
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
}

export async function editAsset(propNum) {
  const params = new URLSearchParams({
    page: "edit-asset-form",
    propNum: propNum,
  });
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
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

export function assignAssets(propNums, nextPage) {  // Allows either asset or user selection first. Resets propNums[]
  const params = new URLSearchParams(window.location.search);
  params.set("page", nextPage);
  params.delete("propNums[]");
  propNums.forEach(propNum => params.append("propNums[]", propNum));
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
}

export function returnAssets(propNums) {
  const params = new URLSearchParams({page: "return-form"});
  propNums.forEach(propNum => params.append("propNums[]", propNum));
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
}

async function fetchAsset(propNum) {
  
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
  const data = await fetchAsset(propNum);
  sessionStorage.setItem("viewAssetData", JSON.stringify(data));
  window.location.href = `${window.location.origin}/index.php?page=asset-view`;
}

export async function editAsset(propNum) {
  const data = await fetchAsset(propNum);
  sessionStorage.setItem("assetData", JSON.stringify(data));
  window.location.href = `${window.location.origin}/index.php?page=edit-asset-form`;
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

export function assignAssets(propNums) {
  sessionStorage.setItem("assetsToAssign", JSON.stringify(propNums));
  window.location.href = `${window.location.origin}/index.php?page=assign-user`;
}

export function returnAssets(propNums) {
  sessionStorage.setItem("assetsToReturn", JSON.stringify(propNums));
  window.location.href = `${window.location.origin}/index.php?page=return-form`;
}

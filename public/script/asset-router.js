export function viewAsset(propNum) {
  fetch(`${window.location.origin}/src/handlers/fetch-asset.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${propNum}`,
  })
  .then(res => res.json())
  .then(data => {
    sessionStorage.setItem("viewAssetData", JSON.stringify(data));
    window.location.href = `${window.location.origin}/public/views/asset-view.php`;
  })
  .catch(err => console.error("Error viewing asset: ", err));
}

export function editAsset(propNum) {
  fetch(`${window.location.origin}/src/handlers/fetch-asset.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${propNum}`,
  })
  .then(res => res.json())
  .then(data => {
    sessionStorage.setItem("assetData", JSON.stringify(data));
    window.location.href = `${window.location.origin}/public/views/edit-asset-form.php`;
  })
  .catch(err => console.error("Error editing assets: ", err));
}

export function returnAsset(propNums) {
  sessionStorage.setItem("assetsToReturn", JSON.stringify(propNums));
  window.location.href = `${window.location.origin}/public/views/return-form.php`;
}

export function deleteAsset(propNum) {
  fetch(`${window.location.origin}/src/handlers/delete-asset.php`, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: `search=${propNum}`,
  }).then(_ => {
    window.location.href = `${window.location.origin}/public/views/asset-manager.php`;
  })
  .catch(err => console.error("Error deleting assets: ", err));
}

export function assignAssets(propNums) {
  sessionStorage.setItem("assetsToAssign", JSON.stringify(propNums));
  window.location.href = `${window.location.origin}/public/views/assign-user.php`;
}

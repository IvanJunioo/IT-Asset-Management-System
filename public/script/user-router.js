export async function fetchUser(empID) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "fetch",
    search: empID,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);

    const data = await resp.json();

    return data;
  } catch (err) {
    console.error("Error fetching user: ", err);
  }
}

export async function editUser(empID) {
  const params = new URLSearchParams({
    page: "edit-user-form",
    empID: empID,
  });
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
}

export async function assignUser(empID, nextPage) { // Allows either asset or user selection first. Resets empID.
  const params = new URLSearchParams(window.location.search);
  params.set("page", nextPage);
  params.set("empID", empID);
  window.location.href = `${window.location.origin}/index.php?${params.toString()}`;
}

export async function modifyUser(empID, actionType) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: actionType,
  });

  try {
    const resp = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        empID: empID,
      }),
    });
    if (!resp.ok) throw new Error(`HTTP error! status: ${resp.status}`);
    
    window.location.href = `${window.location.origin}/index.php?page=user-manager`;
  } catch (err) {
    console.error("Error modifying user: ", err);
  }
}

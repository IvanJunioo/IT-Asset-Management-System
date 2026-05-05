export async function fetchUser(empID) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "fetch",
    search: empID,
  });

  try {
    const resp = await fetch(url);
    if (!resp.ok) {
      return;
    }

    const data = await resp.json();

    return data;
  } catch (err) {
    return;
  }
}

export async function modifyUser(empID, actionType) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: actionType,
    redirect: "index.php?page=user-manager",
  });

  try {
    const resp = await fetch(url, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        empID: empID,
      }),
    });
    if (!resp.ok) {
      return;
    }

    location.reload();
  } catch (err) {
    return;
  }
}

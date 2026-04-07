async function fetchUser(empid) {
  const url = new URL(`${window.location.origin}/api/index.php`);
  url.search = new URLSearchParams({
    resource: "users",
    action: "fetch",
    search: empid,
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

export async function editUser(empid){
  const data = await fetchUser(empid);
  sessionStorage.setItem("userData", JSON.stringify(data));
  window.location.href = `${window.location.origin}/index.php?page=edit-user-form`;
}

export async function assignUser(empid){
  const data = await fetchUser(empid);
  sessionStorage.setItem("assignToUser", JSON.stringify(data));
  window.location.href = `${window.location.origin}/index.php?page=assign-asset`;
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

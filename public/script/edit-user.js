document.addEventListener("DOMContentLoaded", () => {
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  const userForm = document.querySelector(".user-form"); 
  const form = userForm.querySelector("form");

  form.action = "../../src/handlers/edit-user-form.php";
  form.method = "post";

  if (!userData) return;

  fillForm(Array.isArray(userData) ? userData[0] : userData);
  // sessionStorage.removeItem("userData");

  function fillForm(user) {
    const data = {
      'empid': user['EmpID'],
      'e': user['EmpMail'],
      'fn': user['FName'],
      'mn': user['MName'],
      'ln': user['LName'],
    };

    childrenInput = userForm.querySelectorAll('input');
    for (const child of childrenInput) {
      if (child.id in data) {
        child.value = data[child.id];
      } else if (child.value === user['Privilege']) {
        child.checked = true;
      } else if (child.value === user['ActiveStatus']){
				child.checked = true;
			}
    }
  }
});
		
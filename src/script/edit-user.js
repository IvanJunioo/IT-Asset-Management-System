document.addEventListener("DOMContentLoaded", () => {
  const userData = JSON.parse(localStorage.getItem("userData"));
  const userForm = document.querySelector(".user-form"); 

  console.log(userData);
  if (!userData) return;

  fillForm(Array.isArray(userData) ? userData[0] : userData);
  localStorage.removeItem("userData");

  function fillForm(user) {
    const data = {
      'empid': user['EmpID'],
      'e': user['EmpMail'],
      'fn': user['FName'],
      'mn': user['MName'],
      'ln': user['Lname'],
    };

    childrenInput = userForm.querySelectorAll('input');
    for (const child of childrenInput) {
      if (child.id in data) {
        child.value = data[child.id];
      } else if (child.id === user.Privilege) {
        child.checked = true;
      } else if (child.id === user.ActiveStatus){
				child.checked = true;
			}
    }
  }
});
		
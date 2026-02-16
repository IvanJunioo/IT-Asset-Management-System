document.addEventListener("DOMContentLoaded", () => {
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  const userForm = document.querySelector(".user-form"); 
  const form = userForm.querySelector("form");

  form.action = `${window.location.origin}/src/handlers/edit-user-form.php`;
  form.method = "post";
  
  form.querySelector("input#empid").readOnly = true;
  
  if (!userData) return;
  
  const user = Array.isArray(userData) ? userData[0] : userData;
  
  fillForm(user);
  
  if (user['EmpID'] === JSON.parse(sessionStorage.getItem("user-info")).empID) {
    form.querySelector("input#act").disabled = true;
    form.querySelector("input#inact").disabled = true;
  }

  function fillForm(user) {
    const data = {
      'empid': user['EmpID'],
      'e': user['EmpMail'],
      'fn': user['FName'],
      'ln': user['LName'],
    };

    const childrenInput = userForm.querySelectorAll('input');
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
		
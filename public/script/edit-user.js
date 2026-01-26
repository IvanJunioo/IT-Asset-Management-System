document.addEventListener("DOMContentLoaded", () => {
  const userData = JSON.parse(sessionStorage.getItem("userData"));
  const userForm = document.querySelector(".user-form"); 
  const form = userForm.querySelector("form");

  form.action = "../../src/handlers/edit-user-form.php";
  form.method = "post";

  form.querySelector("input#empid").readOnly = true;

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

    const contactNumRows = form.querySelector(".input-rows");
    const contactNumBtn = contactNumRows.querySelector("button");
    for (let i = 1; i < user["ContactNums"].length; i++) {
      contactNumBtn.click();
    }     
    for (const [idx, input] of contactNumRows.querySelectorAll("input").entries()) {
      input.value = user["ContactNums"][idx];
    }
  }
});
		
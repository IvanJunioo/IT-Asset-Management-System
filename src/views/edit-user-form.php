<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/user.css">
    
<body>
    <?php include '../partials/header.php'?>

    <main class="user-form">
        <form action = '../handlers/edit-user-form.php' method="post">
            <label for="employee-id"> Employee ID: </label>
            <input type="text" id ="empid" name="employee-id" placeholder="Enter Employee ID" maxlength="8" minlength="8" size = "8" required>

            <label for="first-name"> First Name: </label>
            <input type="text" id = "fn" name="first-name" placeholder="Enter First Name" maxlength="20" size = "20" required>

            <label for="middle-name"> Middle Name: </label>
            <input type="text" id = "mn" name="middle-name" placeholder="Enter Middle Name" maxlength="20" size = "20">

            <label for="last-name"> Last Name: </label>
            <input type="text" id = "ln" name="last-name" placeholder="Enter Last Name" maxlength="20" size = "20" required>

            <label for="email"> Email: </label>
            <input type="email" id = "e" name="email" placeholder="Enter UP Mail" maxlength="50" size = "30" required> 

            <label for="privilege"> Privilege: </label>
            <label>
              <input type="radio" id = "faculty" name="privilege" value = "Faculty" required checked> Faculty
            </label>
            <label>
              <input type="radio" id = "admin" name="privilege" value = "Admin"> Admin 
            </label>
            <label>
              <input type="radio" id = "superadmin" name="privilege" value = "Super Admin"> Super Admin
            </label>

           
            <label for="active-status"> Status: </label>
            <label>
              <input type="radio" id = "active" name="active-status" value = "Active" required checked> Active 
            </label>
            <label>
              <input type="radio" id = "inactive" name="active-status" value = "Inactive"> Inactive 
            </label>

            <button id="reset-button" type="reset">
              Reset
            </button>

            <button id="submit-button" type="submit" name="action" value="submit">
              Submit
            </button>  
        </form>
				<script src="../script/edit-user.js"></script>
    </main>
    <?php include '../partials/footer.php'?>
</body>
</html>
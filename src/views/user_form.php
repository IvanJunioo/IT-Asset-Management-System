<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
<body>
    <?php include '../partials/header.php'?>

    <main class="add-asset-form">
        <form action = '../includes/assets-inc.php' method="post">
            <label for="emp-mail"> UP Mail: </label>
            <input type="text" id ="pnum" name="emp-mail" placeholder="Enter UP Mail"> </label>

            <label for="name"> Name: </label>
            <input type="text" id = "prnum" name="name" placeholder="Enter Last Name, First Name, Middle Name"></label>

            <label for="role"> System Role: </label>
            <input type="text" id = "snum" name="role" placeholder="Enter System Role"></label>

            <label for="act-log-form"> System Message: </label>
            <input type="text" id = "act-log" name="act-log-form" placeholder="Enter System Message"> </label>

            <button id="reset-button">
                Reset
            </button>

            <button id="submit-button">
                Submit
            </button>  

        </form>
    </main>
    <?php include '../partials/footer.php'?>
</body>
</html>
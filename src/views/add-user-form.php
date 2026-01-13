<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/user.css">
<body>
  <?php include '../partials/header.php'?>

  <main class="user-form">
    <?php include '../views/user-form.php'?>
  </main>

  <?php include '../partials/footer.php'?>
  
  <script defer>
    document.addEventListener("DOMContentLoaded", () => {
      const form = document.querySelector("form");
      form.action = "../handlers/add-user-form.php";
      form.method = "post";  
    });
  </script>
</body>
</html>
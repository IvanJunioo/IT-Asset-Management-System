<?php $REQUIRED_ROLES = ["SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/table-view.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/user-table.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/filters.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/user.css">
<body>
  <?php include '../partials/header.php'?>

  <main class="user-page">
    <?php include '../views/user-page.php'?>
  </main>
  
  <?php include '../partials/footer.php'?>
  
  <script src="../script/assign-user.js" type="module" defer></script>
</body>
</html>
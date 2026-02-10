<?php $REQUIRED_ROLES = ["SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
    <?php include __DIR__ . '/../partials/head.php'?>
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/table-view.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/user-table.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/filters.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/user.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="user-page">
    <?php include __DIR__ . '/user-page.php'?>
  </main>
  
  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="<?= BASE_URL ?>public/script/assign-user.js" type="module" defer></script>
</body>
</html>
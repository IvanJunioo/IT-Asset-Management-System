<?php $REQUIRED_ROLES = ["SuperAdmin"];?>

<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/forms.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/user.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main class="user-form">
    <?php include __DIR__ . '/user-form.php'?>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="<?= BASE_URL ?>public/script/add-user.js" type="module" defer></script>
</body>
</html>
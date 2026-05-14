<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/components/card.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/components/forms.css">
  <style>
    main {
      display: flex;
      justify-content: center;
    }
  </style>
<body>
  <?php include __DIR__ . '/../partials/header.php'?>

  <main>
    <div class="card">
      <h3>Add New User</h3>
      <?php include __DIR__ . '/user-form.php'?>
    </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
  
  <script src="<?= BASE_URL ?>script/add-user.js" type="module" defer></script>
</body>
</html>
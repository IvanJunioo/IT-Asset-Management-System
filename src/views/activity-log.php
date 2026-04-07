<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/act-log.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>css/table.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main>
    <div id="activity-log">
      <?php include __DIR__ . '/act-log.php'?>
    </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
</html>
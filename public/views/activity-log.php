<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/act-log.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main class="activity-log">
    <?php include __DIR__ . '/act-log.php'?>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>

  <script defer>
    document.getElementById("actlog-table").className = "activity-log-table";
  </script>
</body>
</html>
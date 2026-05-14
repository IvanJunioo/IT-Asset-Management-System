<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php'?>
  <?php include __DIR__ . '/../partials/component-styles.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/pages/activity-log.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
  
  <main>
    <div id="activity-log"></div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>
</body>
<script type="module" defer>
  import { LogTable } from "<?= BASE_URL ?>script/components.js";
  new LogTable({container: document.getElementById("activity-log")});
</script>
</html>
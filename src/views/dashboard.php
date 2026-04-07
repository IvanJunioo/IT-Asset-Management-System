<!DOCTYPE html>
<html lang="en">
  <?php include __DIR__ . '/../partials/head.php';?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/dashboard.css">
<body>
  <?php include __DIR__ . '/../partials/header.php'?>
    
  <main class="dashboard">
      <h1 class="dashboard-title">
        <?= htmlspecialchars("Hello, $name!") ?>
      </h1>

      <hr>

      <h2 class="dashboard-text">
        Dashboard
      </h2>

      <section class="dashboard-cards">
        <?php foreach ($dashboardIslands as $label => $data): ?>
          <?php if (in_array($privilege, $data['roles'])): ?>
            <a href="<?=htmlspecialchars($data['url'])?>" class="dashboard-card">
              <h2><?=htmlspecialchars($label)?></h2>
              <p><?=htmlspecialchars($data['body'])?></p>
            </a>
          <?php endif?>
        <?php endforeach?>
      </section>

      <div class="dashboard-bottom">
        <div class="recent-activity">
          <h2>Recent Activity</h2>
          <?php include __DIR__ . '/act-log.php'?>
        </div>

        <section id="asset-distribution">
          <a 
            class = "distr-card" 
            id    = "total-assets" 
            href  = "index.php?page=<?=in_array($_SESSION['privilege'],["Admin", "SuperAdmin"])? "asset-manager" : "assets"?>"
          >
            <p>Assets</p>
          </a>

          <a 
            class = "distr-card" 
            id    = "total-users" 
            href  = "index.php?page=<?=in_array($_SESSION['privilege'],["SuperAdmin"])? "user-manager" : "users"?>"
          >
            <p>Users</p>
          </a>

          <a 
            class = "distr-card" 
            id    = "avail-assets" 
            href  = "index.php?page=<?=in_array($_SESSION['privilege'],["Admin", "SuperAdmin"])? "asset-manager" : "assets"?>"
          >
            <p>Available Assets</p>
          </a>

          <a 
            class = "distr-card" 
            id    = "active-users" 
            href  = "index.php?page=<?=in_array($_SESSION['privilege'],["SuperAdmin"])? "user-manager" : "users"?>"
          >
            <p>Active Users</p>
          </a>

        </section>
      </div>
  </main>

  <?php include __DIR__ . '/../partials/footer.php'?>

  <script src="<?= BASE_URL ?>script/dashboard.js" type="module" defer></script>
</body>
</html>
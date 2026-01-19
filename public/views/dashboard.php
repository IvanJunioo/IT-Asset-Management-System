<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>public/css/dashboard.css">
<body>
  <?php include '../partials/header.php'?>
    
    <main class="dashboard">
        <h1 class="dashboard-title">
          Hello, Super Admin!
        </h1>

        <hr>

        <h2 class="dashboard-text">
          Dashboard
        </h2>

        <section class="dashboard-cards">
          <a href="./assets.php" class="card">
            <h2>View Assets</h2>
            <p>Preview all the system assets.</p>
          </a>

      <a href="./activity-log.php" class="card">
        <h2>View System Activities</h2>
        <p>Track recent system actions and events.</p>
      </a>

      <a href="./asset-manager.php" class="card">
        <h2>Manage Assets</h2>
        <p>Add, edit, assign, or remove assets in your inventory.</p>
      </a>

            <a href="./users.php" class="card">
                <h2>Manage Users</h2>
                <p>Add or update user roles and permissions.</p>
            </a>
        </section>

        <div class="dashboard-bottom">
            <div class="recent-activity">
                <h2>Recent Activity</h2>
                <?php include '../views/act-log.php'?>
            </div>

            <div id="asset-distribution">

                <a href="./assets.php" class="distr-card" id="total-assets">
                    <h2>1240</h2>
                    <p>Assets</p>
                </a>

                <a href="./assets.php" class="distr-card" id="total-users">
                    <h2>49</h2>
                    <p>Users</p>
                </a>

                <a href="./assets.php" class="distr-card" id="avail-assets">
                    <h2>432</h2>
                    <p>Available Assets</p>
                </a>

                <a href="./assets.php" class="distr-card" id="active-users">
                    <h2>48</h2>
                    <p>Active Users</p>
                </a>

            </div>
        </div>
    </main>

    <?php include '../partials/footer.php'?>

    <script src="../script/dashboard.js" defer></script>
</body>
</html>
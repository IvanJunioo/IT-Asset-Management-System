<!-- Header template -->
<div class ="header">
    <section id="logo-caption">
        <div id="menu-caption">
            <div class="upd">University of the Philippines Diliman</div>
            <div class="dcs">Department of Computer Science</div>
            <div class="assIT">Asset Management System</div>
        </div>
        <div class="logo">
            <a href="https://dcs.upd.edu.ph/" target="_blank">
                <img id="dcs" src="https://cms.dcs.upd.edu.ph/assets/9a8e9dd2-3851-4c88-9687-c4aca3aceea5?fit=cover&width=90&height=90">
            </a>
            <a href="https://upd.edu.ph" target="_blank">
                <img id="upd" src="https://cms.dcs.upd.edu.ph/assets/9f12ac0a-ba54-41e5-84ef-1e2b9d076f7d?fit=cover&width=90&height=90">
            </a>
        </div>
    </section>

    <section id="navigation">
        <div id="navigation-bar">
            <a href="<?= BASE_URL ?>public/views/dashboard.php">
                Dashboard
            </a>

            <a href="<?= BASE_URL ?>public/views/assets.php">
                Assets
            </a>

            <a href="<?= BASE_URL ?>public/views/asset-manager.php">
                Manage Assets
            </a>

            <a href="<?= BASE_URL ?>public/views/users.php">
                Users
            </a>

            <a href="<?= BASE_URL ?>public/views/user-manager.php">
                Manage Users
            </a>

            <a href="<?= BASE_URL ?>public/views/activity-log.php">
                System Activities
            </a>
        </div> 

        <div id="user-panel">
            <span id="username"> 
              <?= htmlspecialchars("{$userLName}, {$userFName}, {$userMName[0]}") ?>
            </span>
            <span id="user-role">
              <?= htmlspecialchars($privilege) ?>
            </span>
            <a id="logout" href="<?= BASE_URL ?>src/handlers/logout.php"> Sign Out </a>
        </div>
    </section>
</div>
<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
<body>
    <?php include '../partials/header.php'?>
    
    <main class="dashboard">
        <h1 class="dashboard-title">
            Hello, SuperAdmin!
        </h1>
        <section class="dashboard-cards">
            <a href="./assets.php" class="card">
                <h2>View Assets</h2>
                <p>Preview and manage all system assets.</p>
            </a>

            <a href="./activity_log.php" class="card">
                <h2>View System Activities</h2>
                <p>Track recent system actions and events.</p>
            </a>

            <a href="./asset_manager.php" class="card">
                <h2>Manage Assets</h2>
                <p>Add, edit, or remove assets in your inventory.</p>
            </a>

            <a href="./users.php" class="card">
                <h2>Manage Users</h2>
                <p>View and update user roles and permissions.</p>
            </a>
        </section>
    </main>
    <?php include '../partials/footer.php'?>
</body>
</html>
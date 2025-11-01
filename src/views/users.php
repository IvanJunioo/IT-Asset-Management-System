<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
<body>
    <!-- menu -->
    <?php include '../partials/header.php'?>

    <!-- user-page -->
    <main class="asset-page">
        <div class="left-asset">
            <div id="search-box">
                <input type="text" id="search-input" placeholder="Search Users">
                <button id="search-button"> Search </button>
            </div>
            
            <div class="table-container">
                <table class="asset-table">
                    <thead>
                        <tr>
                            <th> User ID </th>
                            <th> User Email </th>
                            <th> Name </th>
                            <th> Account Status </th>
                            <th> Role </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <a href="user_form.php" id="add-asset"> 
                <span class="material-icons" id="add-asset-button">add</span>
                Add a New User
            </a>
            <script>

                function getRandomItem(arr) {
                    return arr[Math.floor(Math.random() * arr.length)];
                }

                const assetTableBody = document.querySelector('.asset-table tbody');

                const ids = Array.from({ length: 26 }, (_, i) => i);
                const statuses = ["Active", "Deleted", "Active", "Active", "Active"];
                const names = "abcdefghijklmnopqrstuvwxyz"
                const roles = ["Faculty", "Admin", "Super Admin"]

                for (let i = 0; i < 26; i++) {
                    const tr = document.createElement('tr');
                    
                    const name = names[i];
                    const userID = i;
                    const userEmail = names[i] + "@up.edu.ph";
                    const status = getRandomItem(statuses);
                    const role = (status == "Active") ? getRandomItem(roles) : "-";

                    tr.innerHTML = `
                        <td>${userID}</td>
                        <td>${userEmail}</td>
                        <td>${name}</td>
                        <td>${status}</td>
                        <td>${role}</td>
                        <td class="actions">
                            <button class="action-btn">
                                <span class="material-icons">more_horiz</span>
                            </button>

                            <div class="action-menu">
                                <a href="user_form.php" class="menu-item">Modify</a>
                                <a href="user_form.php" class="menu-item">Delete</a>
                            </div>
                        </td>
                    `;

                    assetTableBody.appendChild(tr);
                }

                document.addEventListener("DOMContentLoaded", () => {
                    document.querySelectorAll(".action-btn").forEach(btn => {
                        btn.addEventListener("click", (e) => {
                            e.stopPropagation();
                            const actionsCell = btn.closest(".actions");
                            actionsCell.classList.toggle("show-menu");
                        });
                    });

                    document.addEventListener("click", () => {
                        document.querySelectorAll(".actions.show-menu")
                        .forEach(cell => cell.classList.remove("show-menu"));
                    });
                });


            </script>
    </main>
    <?php include '../partials/footer.php'?>
</body>
</html>
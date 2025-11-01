<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
<body>
    <!-- menu -->
    <?php include '../partials/header.php'?>

    <!-- asset-page -->
    <main class="asset-page">
        <div class="left-asset">
            <div id="search-box">
                <input type="text" id="search-input" placeholder="Search asset">
                <button id="search-button"> Search </button>
            </div>
            
            <div class="table-container">
                <table class="asset-table">
                    <thead>
                        <tr>
                            <th> Property Number </th>
                            <th> Procurement Number </th>
                            <th> Purchase Date </th>
                            <th> Detailed Specification </th>
                            <th> Price </th>
                            <th> Status  </th>
                            <th> Assigned to </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <a href="asset_form.php" id="add-asset"> 
                <span class="material-icons" id="add-asset-button">add</span>
                Add a New Asset 
            </a>
            <script>

                const assetTableBody = document.querySelector('.asset-table tbody');

                const assetNames = [
                    "Lenovo ThinkPad X1 Carbon", "Dell XPS 15", "MacBook Pro 16", "HP Envy 13", 
                    "Acer Predator Helios 300", "Asus ROG Zephyrus G14", "MSI Katana GF66",
                    "Surface Laptop 5", "Huawei MateBook D16", "Gigabyte Aorus 17"
                ];
                const statuses = ["Available", "Assigned", "Condemned", "To Repair"];
                const names = [
                    "a", "b", "c"
                ];

                function getRandomItem(arr) {
                    return arr[Math.floor(Math.random() * arr.length)];
                }

                for (let i = 0; i < 30; i++) {
                    const tr = document.createElement('tr');

                    const assetName = getRandomItem(assetNames) + " #" + (Math.floor(Math.random() * 900) + 100);
                    const status = getRandomItem(statuses);
                    const assignedTo = status === "Available" ? "-" : getRandomItem(names);
                    const infoLink = "https://example.com/assets/" + assetName.replace(/\s+/g, '-').toLowerCase();

                    tr.innerHTML = `
                        <td>${assetName}</td>
                        <td> - </td>
                        <td> - </td>
                        <td> - </td>
                        <td> ${1000} </td>
                        <td> ${status} </td>
                        <td> ${assignedTo} </td>
                        <td class="actions">
                            <button class="action-btn">
                                <span class="material-icons">more_horiz</span>
                            </button>

                            <div class="action-menu">
                                <a href="asset_form.php" class="menu-item">Modify</a>
                                <a href="asset_form.php" class="menu-item">Delete</a>
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
        </div>
        <div class="right-asset">
            <div id="filter-box">
                <div id="head-filter">
                    FILTERS
                </div>

                <div id="body-filter">
                    <label><input type="checkbox" id="available"> Available</label>
                    <label><input type="checkbox" id="assigned"> Assigned</label>
                    <label><input type="checkbox" id="condemned"> Condemned</label>
                    <label><input type="checkbox" id="to-repair"> To Repair</label>
                </div>
                
                <button id="apply-filter"> Apply Filter </button>

            </div>
            <button id="export"> Export assets </button>
        </div>
    </main>
    <?php include '../partials/footer.php'?>
</body>
</html>
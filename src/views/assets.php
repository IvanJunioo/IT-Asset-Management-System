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
                            <th> Asset Name </th>
                            <th> Status </th>
                            <th> Assigned to </th>
                            <th> Detailed Specification </th>
                            <th> Detailed Specification </th>
                            <th> Detailed Specification </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

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
                        <td>${status}</td>
                        <td>${assignedTo}</td>
                        <td><a href="${infoLink}" target="_blank">More Information Here</a></td>
                    `;

                    assetTableBody.appendChild(tr);
                }
            </script>
        </div>
        <div class="right-asset">
            <div id="filter-box">
                <div id="head-filter">
                    FILTERS
                </div>

                <div id="body-filter">
                    <div id="left-filter">
                        <label><input type="checkbox" id="available"> Available</label>
                        <label><input type="checkbox" id="assigned"> Assigned</label>
                    </div>
                    <div id="right-filter">
                        <label><input type="checkbox" id="condemned"> Condemned</label>
                        <label><input type="checkbox" id="to-repair"> To Repair</label>
                    </div>
                </div>
                
                <button id="apply-filter"> Apply Filter </button>

            </div>
            <button id="export"> Export assets </button>
        </div>
    </main>
    <?php include '../partials/footer.php'?>
</body>
</html>
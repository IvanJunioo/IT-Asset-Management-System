<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <!-- menu -->
    <header>
        <section id="logo-caption">
            <div id="menu-caption">
                <h1 class="upd">University of the Philippines Diliman</h1>
                <h1 class="dcs">Department of Computer Science</h1>
                <h1 class="assIT">Asset Management System</h1>
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
                <a> Dashboard </a>
                <a> Assets </a>
                <a> Assigned Assets </a>
            </div> 

            <div id="user-panel">
                <span>JUNIO, IVAN AHRON L.</span>
                <span>SUPER ADMIN</span>
                <a> LOGOUT </a>
            </div>

        </section>
    </header>

    <!-- webpage -->

    <!-- asset-page -->
    <main class="asset-page">
        <div class="left">
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
        <div class="right">
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
            </div>
            <button id="export"> Export assets </button>
        </div>
    </main>

    <!-- dashboard -->
    <main class="dashboard">

    </main>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/user.css">
<body>
  <!-- menu -->
  <?php include '../partials/header.php'?>

  <!-- user-page -->
  <main class="user-page">
    <div class="left-user">
      <div id="search-box">
        <input type="text" id="search-input" placeholder="Search user">
        <button id="search-button"> Search </button>
      </div>
      
      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th> Employee ID </th>
              <th> Email </th> 
              <th> First Name </th>
              <th> Middle Name </th>
              <th> Last Name </th>
              <th> Privilege </th>
              <th> Status  </th>
              <th></th>
            </tr>
          </thead>
            <tbody>
    
            </tbody>
        </table>
      </div>

			<a href="user-form.php" id="addUser"> <button> Add User </button> </a>

      <script>
        fetch("../handlers/user-table.php", {method: "POST"})
          .then(res => res.json())
          .then(data => {
            const userTableBody = document.querySelector('.user-table tbody');
            userTableBody.innerHTML = "";

            for (const user of data) {
              const tr = document.createElement('tr');
              tr.innerHTML = `
                <td>${user.EmpID}</td>
                <td>${user.EmpMail}</td>
                <td>${user.FName}</td>
                <td>${user.MName}</td>
                <td>${user.LName}</td>
                <td>${user.Privilege} </td>
                <td>${user.ActiveStatus} </td>
              `;
              
              userTableBody.appendChild(tr);
            }
          })
      </script>


    </div>
    <div class="right-user">
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
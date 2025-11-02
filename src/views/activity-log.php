<!DOCTYPE html>
<html lang="en">
  <?php include '../partials/head.php'?>
  <link rel="stylesheet" href="<?= BASE_URL ?>css/act-log.css">
<body>
  <?php include '../partials/header.php'?>
  
  <main class="activity-log">
    <table class="activity-log-table">
      <thead>
        <tr>
          <th> Date </th>
          <th> Context </th>
          <th> Procurement Number </th>
          <th> Username </th>
          <th> Activity </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td data-label="Date"> 2025-10-28 </td> 
          <td data-label="Context"> Asset </td> 
          <td data-label="Procurement Number"> 0001 </td> 
          <td data-label="Username"> - </td> 
          <td data-label="Activity"> Purchased Monitors to be assigned in Teaching Laboratory 1 </td> 
        </tr> 

        <tr> 
          <td data-label="Date"> 2025-10-29 </td> 
          <td data-label="Context"> System Users </td> 
          <td data-label="Procurement Number"> - </td> 
          <td data-label="Username"> abc123@up.edu.ph </td> 
          <td data-label="Activity"> Added abc123 user in the system </td> 
        </tr>

        <tr> 
          <td data-label="Date"> 2025-10-30 </td> 
          <td data-label="Context"> System Users </td> 
          <td data-label="Procurement Number"> - </td> 
          <td data-label="Username"> schematicDiagram@up.edu.ph </td> 
          <td data-label="Activity"> Promoted schematicDiagram from Faculty to Admin </td> 
        </tr>
      </tbody>
    </table>
  </main>
  <?php include '../partials/footer.php'?>
</body>
</html>
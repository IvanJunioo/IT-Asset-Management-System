<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
<body>
    <h1>All <?= htmlspecialchars($statusName) ?> Assets</h1>
    <table class="asset-table">
      <thead>
        <tr>
          <th> Property No. </th>
          <th> Serial No. </th>
          <th> Procurement No. </th>
          <th> Acquisition Date </th>
          <th> Unit Cost </th>
          <th> Remarks  </th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($assets as $asset): ?>
          <tr>
            <td><?=  htmlspecialchars($asset->propNum) ?></td>
            <td><?=  htmlspecialchars($asset->serialNum) ?></td>
            <td><?=  htmlspecialchars($asset->procNum) ?></td>
            <td><?=  htmlspecialchars($asset->purchaseDate) ?></td>
            <td><?=  htmlspecialchars($asset->price) ?></td>
            <td><?=  htmlspecialchars($asset->remarks) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  </body>
</html>

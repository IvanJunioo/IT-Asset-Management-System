<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
  <body>
    <?php 
      $title = "";
      if ($statusName == "Condemned") {
        $title = "CONDEMNED";
      } elseif ($statusName == "ToCondemn") {
        $title = "TO-BE-CONDEMNED";
      } else {
        $title = "UNASSIGNED";
      }
    ?>
    <h1> ALL <?= htmlspecialchars($title) ?> ASSETS</h1>
    <div class="asset-container">
      <?php if (!empty($assets)): ?>
      <table>
        <thead>
          <tr>
            <th> Property No. </th>
            <th> Serial No. </th>
            <th> Acquisition Date </th>
            <th> Description </th>
            <?php if ($add_remarks): ?>
              <th> Remarks  </th>
            <?php endif ?>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($assets as $asset): ?>
            <tr>
              <td><?=  htmlspecialchars($asset->propNum) ?></td>
              <td><?=  htmlspecialchars($asset->serialNum) ?></td>
              <td><?=  htmlspecialchars($asset->purchaseDate) ?></td>
              <td><?=  htmlspecialchars($asset->description !== '' ? (strlen($asset->description) > 300 ? substr($asset->description,0,300) . '...' : $asset->description) : 'None') ?></td>
              <?php if ($add_remarks): ?>
                <td class="remarks-cell"><?= htmlspecialchars($asset->remarks !== '' ? (strlen($asset->remarks) > 300 ? substr($asset->remarks,0,300) . '...' : $asset->remarks) : 'None') ?></td>
              <?php endif ?>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <p class="empty"> No <?= htmlspecialchars(strtolower($statusName)) ?> assets</p>
      <?php endif; ?>
    </div>
  </body>
  <footer>
    <p class="page">Page </p>
  </footer>
</html>

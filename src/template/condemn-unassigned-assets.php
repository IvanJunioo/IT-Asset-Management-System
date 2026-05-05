<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
  <body>
    <?php 
      $title = ($statusName == "ToCondemn") ? "TO BE CONDEMNED" : "THAT ARE UNASSIGNED";
    ?>
    <h1>All Assets <?= htmlspecialchars($title) ?></h1>
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
              <td><?=  htmlspecialchars($asset->description !== '' ? $asset->description : 'None') ?></td>
              <?php if ($add_remarks): ?>
                <td class="remarks-cell"><?= htmlspecialchars($asset->remarks !== '' ? $asset->remarks: 'None') ?></td>
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

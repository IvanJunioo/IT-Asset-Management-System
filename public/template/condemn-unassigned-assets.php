<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
  <body>
    <h1>All <?= htmlspecialchars($statusName) ?> Assets</h1>
    <div class="asset-container">
      <?php if (!empty($assets)): ?>
      <table>
        <thead>
          <tr>
            <th> Property No. </th>
            <th> Serial No. </th>
            <th> Acquisition Date </th>
            <th> Description </th>
            <th> Remarks  </th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($assets as $asset): ?>
            <tr>
              <td><?=  htmlspecialchars($asset->propNum) ?></td>
              <td><?=  htmlspecialchars($asset->serialNum) ?></td>
              <td><?=  htmlspecialchars($asset->purchaseDate) ?></td>
              <td><?=  htmlspecialchars($asset->description !== '' ? $asset->description : 'None') ?></td>
              <td class="remarks-cell"><?= htmlspecialchars($asset->remarks !== '' ? $asset->remarks: 'None') ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <span class="empty"> No <?= htmlspecialchars(strtolower($statusName)) ?> assets</span>
      <?php endif; ?>
    </div>
  </body>
</html>

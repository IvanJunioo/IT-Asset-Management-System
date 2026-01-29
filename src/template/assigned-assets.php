<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
<body>
    <h1>Currently Assigned Assets</h1>

    <?php foreach ($assets as $asset): ?>
      <div class="asset-container">
        <div class="asset-header">Asset: <?= htmlspecialchars($asset->propNum) ?></div>
        <table>
          <tr><th>Procurement Number</th><td><?= htmlspecialchars($asset->procNum) ?></td></tr>
          <tr><th>Serial Number</th><td><?= htmlspecialchars($asset->serialNum) ?></td></tr>
          <tr><th>Specs</th><td><?= htmlspecialchars($asset->specs) ?></td></tr>
          <tr><th>Remarks</th><td class="remarks-cell"><?= htmlspecialchars($asset->remarks) ?></td></tr>
        </table>
      </div>
    <?php endforeach; ?>
  </body>
</html>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
<body>
    <h1>Currently Assigned Assets</h1>

    <?php foreach ($data as $d): ?>
      <?php 
        $assetDetails = $d['asset'];
        $asset = $assetDetails[0];
        $assDate = $assetDetails[1];
      ?>
      <div class="asset-container">
        <div class="asset-header">Property Number: <?= htmlspecialchars($asset->propNum) ?></div>
        <table>
          <tr><th>Serial Number</th><td><?= htmlspecialchars($asset->serialNum) ?></td></tr>
          <tr><th>Assignment Date</th><td><?= htmlspecialchars($assDate) ?></td></tr>
          <tr><th>Specs</th><td><?= htmlspecialchars($asset->specs) ?></td></tr>
          <tr><th>Remarks</th><td class="remarks-cell"><?= htmlspecialchars($asset->remarks) ?></td></tr>
        </table>
      </div>
    <?php endforeach; ?>
  </body>
</html>

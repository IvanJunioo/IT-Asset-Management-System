<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
<body>
    <h1>Assets Currently Assigned</h1>
    <div class="asset-container">
      <?php if (!empty($assets)): ?>
        <table>
          <thead>
            <tr>
              <th>Property No.</th>
              <th>Serial No.</th>
              <th>Assignment Date</th>
              <th>Specifications </th>
              <th>Description</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $d): ?>
              <?php 
                $assetDetails = $d['asset'];
                $asset = $assetDetails[0];
                $assDate = $assetDetails[1];
              ?>
              <tr>
                <td><?= htmlspecialchars($asset->propNum) ?></td> 
                <td><?= htmlspecialchars($asset->serialNum) ?></td>
                <td><?= htmlspecialchars($assDate) ?></td>
                <td><?= htmlspecialchars($asset->specs !== ''? $asset->specs: 'None') ?></td>
                <td><?=  htmlspecialchars($asset->description !== '' ? $asset->description : 'None') ?></td>
                <td class="remarks-cell"><?= htmlspecialchars($asset->remarks !== '' ? $asset->remarks: 'None') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
      <?php else: ?>
        <p class="empty"> No assets assigned</p>
      <?php endif; ?>
    </div>
  </body>
</html>

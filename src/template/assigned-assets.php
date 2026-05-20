<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
<body>

    <div class="report-header">
      <h1>Assets Currently Assigned</h1>
      <div class="report-meta">
        As of <?= date("F d, Y") ?>
      </div>
    </div>

    <div class="asset-container">
      <?php if (!empty($assets)): ?>
        <?php 
        $priv = $user->privilege->value;
        $fullName = trim("{$user->name->first} {$user->name->last}");
        ?>
        <div class="asset-header">
          <?= htmlspecialchars($priv) ?>: <?= htmlspecialchars("{$fullName}") ?>
        </div>
        <table>
          <thead>
            <tr>
              <th>Property No.</th>
              <th>Serial No.</th>
              <th>Assignment Date</th>
              <th>Specifications </th>
              <th>Description</th>
              <?php if ($add_remarks): ?>
                <th>Remarks</th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($data as $d): ?>
              <?php 
                $assetDetails = $d['asset'];
                $asset = $assetDetails[0];
                $assDate = $assetDetails[1];
                $assignRemarks = $assetDetails[2];
              ?>
              <tr>
                <td><?= htmlspecialchars($asset->propNum) ?></td> 
                <td><?= htmlspecialchars($asset->serialNum) ?></td>
                <td><?= htmlspecialchars($assDate) ?></td>
                <td><?= htmlspecialchars($asset->specs !== ''? $asset->specs: 'None') ?></td>
                <td><?=  htmlspecialchars($asset->description !== '' ? (strlen($asset->description) > 300 ? substr($asset->description,0,300) . '...' : $asset->description) : 'None') ?></td>
                <?php if ($add_remarks): ?>
                  <td class="remarks-cell"><?= htmlspecialchars($assignRemarks !== '' ? (strlen($assignRemarks) > 300 ? substr($assignRemarks,0,300) . '...' : $assignRemarks) : 'None') ?></td>
                <?php endif ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
      </table>
      <?php else: ?>
        <p class="empty"> No assets assigned</p>
      <?php endif; ?>
    </div>
  </body>
  <footer>
    <p class="page">Page </p>
  </footer>
</html>

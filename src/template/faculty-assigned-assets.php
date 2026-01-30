<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
</head>
<body>
  <h1>All Faculty Assigned Assets</h1>
  <?php foreach ($users as $user): ?>
    <div class="asset-container">
      <?php
        $first  = $user->name->first ?? '';
        $middle = $user->name->middle ?? '';
        $last   = $user->name->last ?? '';
        $empID  = $user->empID ?? '';
        $priv   = $user->getPrivilege()->value ?? '';
        
        $assets = $assetsMapping[$empID] ?? [];
      ?>
      
      <div class="asset-header">
        <?= htmlspecialchars($priv) ?>: <?= htmlspecialchars("$first $middle $last ($empID)") ?>
      </div>
      
      <?php if (!empty($assets)): ?>
        <table>
          <thead>
            <tr>
              <th>Property No.</th>
              <th>Serial No.</th>
              <th>Procurement No.</th>
              <th>Acquisition Date</th>
              <th>Unit Cost</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($assets as $asset): ?>
              <tr>
                <td><?= htmlspecialchars($asset->propNum ?? '') ?></td>
                <td><?= htmlspecialchars($asset->serialNum ?? '') ?></td>
                <td><?= htmlspecialchars($asset->procNum ?? '') ?></td>
                <td><?= htmlspecialchars($asset->purchaseDate ?? '') ?></td>
                <td><?= htmlspecialchars($asset->price ?? '') ?></td>
                <td><?= htmlspecialchars($asset->remarks ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Property No.</th>
              <th>Serial No.</th>
              <th>Procurement No.</th>
              <th>Acquisition Date</th>
              <th>Unit Cost</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <tr><td colspan="6">No assets assigned. </td></tr>
          </tbody>
        </table>
      <?php endif; ?>
      
    </div>
  <?php endforeach; ?>
</body>
</html>

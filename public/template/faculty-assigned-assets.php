<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
  </head>
  <body>
    <h1>All Faculty Assigned Assets</h1>
    <?php foreach ($data as $d): ?>
      <?php 
        $user   = $d['user'];
        $assets = $d['assets'];
        $dates  = $d['assignDates'];

        $fullName = trim("{$user->name->first} {$user->name->last}");
        $priv     = $user->privilege->value;
      ?>
        
      <div class="asset-container">
        <div class="asset-header">
          <?= htmlspecialchars($priv) ?>: <?= htmlspecialchars("{$fullName}") ?>
        </div>
        <table>
          <thead>
            <tr>
              <th>Property No.</th>
              <th>Serial No.</th>
              <th>Assignment Date</th>
              <th>Description</th>
              <?php if ($add_remarks): ?>
                <th>Remarks</th>
              <?php endif ?>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($assets)): ?>
            <?php foreach ($assets as $asset): ?>
              <tr>
                  <td><?= htmlspecialchars($asset->propNum) ?></td>
                  <td><?= htmlspecialchars($asset->serialNum) ?></td>
                  <td><?= htmlspecialchars($dates[$asset->propNum]) ?></td>
                  <td><?=  htmlspecialchars($asset->description !== '' ? $asset->description : 'None') ?></td>
                  <?php if ($add_remarks): ?>
                    <td class="remarks-cell"><?= htmlspecialchars($asset->remarks !== '' ? $asset->remarks: 'None') ?></td>
                  <?php endif ?>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4">No assets assigned.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>
  </body>
  <footer>
    <p class="page">Page </p>
  </footer>
</html>


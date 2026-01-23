<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/asset.php';
require_once '../repos/asset.php';
require_once '../manager/logger.php';

$repo = new AssetRepo($pdo);

$action = $_POST['action'];

if ($action == 'submit') {
  $propNum = $_POST['property-num'];

  $old = $repo->identify($propNum);

  $asset = new Asset(
    propNum: $propNum,
    procNum: $_POST['procurement-num'],
    serialNum: $_POST['serial-num'],
    purchaseDate: $_POST['purchase-date'],
    specs: $_POST['specs'],
    description: $_POST['short-desc'],
    url: $_POST['img-url'],
    remarks: $_POST['remarks'],
    price: $_POST['price'],
    status: AssetStatus::from($_POST['asset-status']),
  );

  $repo->update($asset);

  systemLog(
    "modified asset $propNum",
    array_diff_assoc($asset->jsonSerialize(), $old->jsonSerialize()),
  );
}

header('Location: ../../public/views/asset-manager.php');

exit;

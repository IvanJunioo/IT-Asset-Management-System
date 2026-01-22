<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/asset.php';
require_once '../repos/asset.php';
require_once '../manager/logger.php';

$assetRepo = new AssetRepo($pdo);

$action = $_POST['action'];

if ($action == 'submit') {
  $propNums = $_POST['property-num'];

  foreach ($propNums as $propNum) {
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
  
    $assetRepo->add($asset);
  }

  systemLog(
    "added " . count($propNums) . " new asset(s).", 
    ["assets" => $propNums]
  );
}

header('Location: ../../public/views/asset-manager.php');

exit;

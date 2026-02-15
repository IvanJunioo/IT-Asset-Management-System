<?php

require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../model/asset.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../manager/logger.php';

$assetRepo = new AssetRepo($pdo);

$action = $_POST['action'];

if ($action == 'submit') {
  $propNums = $_POST['property-num'];
  $serialNums = $_POST['serial-num'];
  $urls = $_POST['img-url'];

  foreach ($propNums as $i => $propNum) {
    $asset = new Asset(
      propNum: $propNum,
      procNum: $_POST['procurement-num'],
      serialNum: $serialNums[$i],
      purchaseDate: $_POST['purchase-date'],
      specs: $_POST['specs'],
      description: $_POST['short-desc'],
      url: $urls[$i],
      remarks: $_POST['remarks'],
      price: $_POST['price'],
      status: AssetStatus::from($_POST['asset-status']),
    );
  
    $assetRepo->add($asset);
  }
  
  $propNumList = count($propNums) > 1 ? implode(', ', $propNums) : $propNums[0];
  systemLog(
    "added new asset(s) $propNumList",
    [
      "action" => "add",
      "object" => "asset",
      "propNum" => $propNums,
    ]
  );

}

header('Location: ../../public/views/asset-manager.php');

exit;
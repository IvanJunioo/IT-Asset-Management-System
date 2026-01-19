<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../model/database.php';

$db = new Database($pdo);

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
  
    $db->addAsset($asset);
  }
}

header('Location: ../../public/views/asset-manager.php');

exit;

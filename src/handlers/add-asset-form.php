<?php

require_once '../includes/request-guard.php';
require_once '../includes/dbh-inc.php';
require_once '../model/database.php';

$db = new Database($pdo);

$action = $_POST['action'];

if ($action == 'submit') {
  $asset = new Asset(
    propNum: $_POST['property-num'],
    procNum: $_POST['procurement-num'],
    serialNum: $_POST['serial-num'],
    date: $_POST['purchase-date'],
    specs: $_POST['specs'],
    desc: $_POST['short-desc'],
    url: $_POST['img-url'],
    remarks: $_POST['remarks'],
    price: $_POST['price'],
  );

  $db->addAsset($asset);
}

header('Location: ../views/asset-form.php');

exit;

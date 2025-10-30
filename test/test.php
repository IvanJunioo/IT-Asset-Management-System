<?php

require_once "../src/model/asset.php";
require_once "../src/model/database.php";
require_once "../src/model/user.php";
require_once "../src/model/model.php";
require_once "../src/includes/dbh-inc.php";

$users = [];
$assets = [];

$db = new Database($pdo);
$asset1 = new Asset(
  propNum: "0000",
  procNum: "1001",
  serialNum: "2523",
  date: "2019-07-08",
  specs: "laptop",
  desc: "this is a computer",
  url: "google.com",
  remarks: "new buy",
  price: 470000.89
);

$user1 = new SuperAdmin(
  name: new Username("Dianito", "Kupal", "Ivan.ph"),
  email: "asd123@up.edu.ph",
  empID: "25-66677",
);

$user2 = new Admin(
  name: new Username("Junio","Kupal","Ivan.ph"),
  email: "asd123@up.edu.ph",
  empID: "24-66677",
);

$user3 = new Faculty(
  name: new Username("Alcancia", "Kupal", "Ivan.ph"),
  email: "asd123@up.edu.ph",
  empID: "23-66677",
);

$asset2 = new Asset(
  propNum: "0001",
  procNum: "1001",
  serialNum: "2523",
  date: "2019-07-08",
  specs: "laptop",
  desc: "this is a laptop",
  url: "google.com",
  remarks: "new buy",
  price: 470000.89
);
  
$db->addAsset($asset1);
$db->addUser($user1);
$db->addUser($user2);
$db->addUser($user3);
$db->addAsset($asset2);
// $new = $asset2->copyAsset();
$asset2->setStatus(AssetStatus::InRepair);
$asset2->setPrice(69);
// echo $new->getStatus()->name;
// echo $asset2->getStatus()->name;
$db->updateAsset($asset2);
$db->assignAsset($asset2, $user1, "2020-07-01", "good asset :)");
$db->assignAsset($asset1, $user1, "2019-01-01", "nice asset (y)");

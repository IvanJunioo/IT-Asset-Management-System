<?php

require_once "../src/model/asset.php";
require_once "../src/model/database.php";
require_once "../src/model/user.php";
require_once "../src/includes/dbh-inc.php";

$users = [];
$assets = [];

$db = new Database($pdo);

$user1 = new SuperAdmin(
  name: new Fullname("Dianito", "Kupal", "Ivan.ph"),
  email: "asd123@up.edu.ph",
  empID: "25-66677",
);

$user2 = new Admin(
  name: new Fullname("Junio","Kupal","Ivan.ph"),
  email: "asd123@up.edu.ph",
  empID: "24-66677",
);

$user3 = new Faculty(
  name: new Fullname("Alcancia", "Kupal", "Ivan.ph"),
  email: "asd123@up.edu.ph",
  empID: "23-66677",
);

$asset1 = new Asset(
  propNum: "0000",
  procNum: "1001",
  serialNum: "2000",
  date: "2019-07-08",
  specs: "desktop",
  desc: "this is a computer",
  url: "google.com",
  remarks: "old unit",
  price: 470000
);


$asset2 = new Asset(
  propNum: "0001",
  procNum: "1011",
  serialNum: "5555",
  date: "2020-07-08",
  specs: "arduino",
  desc: "this is an arduino",
  url: "facebook.com",
  remarks: "new buy",
  price: 10000
);
  

$asset3 = new Asset(
  propNum: "0002",
  procNum: "1101",
  serialNum: "4538",
  date: "2019-08-08",
  specs: "cellphone",
  desc: "this is a cellphone",
  url: "upd.google.com",
  remarks: "new buy",
  price: 50500.40
);

$asset4 = new Asset(
  propNum: "0003",
  procNum: "0101",
  serialNum: "2523",
  date: "2021-07-08",
  specs: "laptop",
  desc: "this is a laptop",
  url: "",
  remarks: "need to be fixed",
  price: 15000
);

$db->addUser($user1);
$db->addUser($user2);
$db->addUser($user3);
$db->addAsset($asset1);
$db->addAsset($asset2);
$db->addAsset($asset3);
$db->addAsset($asset4);

$asset2->setStatus(AssetStatus::InRepair);
$asset2->setPrice(69);

$user1->setEmail("hello@upd.edu.ph");
$db->updateUser($user1);

$db->updateAsset($asset2);
$db->assignAsset($asset2, $user1, new DateTimeImmutable("2020-07-01"), "good asset :)");
$db->assignAsset($asset1, $user1, new DateTimeImmutable("2019-01-01"), "nice asset (y)");
// $res = $db->searchAsset(procNum: "11", price_min:20000, price_max:100000);
// foreach ($res as $r){
//   echo $r->getPropNum() . "<br>";
// }
// $resUser = $db->searchUser(isActive: ['Active'], privileges:[UserPrivilege::admin]);
// foreach ($resUser as $r){
//   echo $r->getEmpID() . "<br>";
// }
$a1 = $db->getAssignedAssets($user1);

// $db->unassignAsset(
//   asset: $asset1, 
//   assDate: new DateTimeImmutable("2019-01-01"), 
//   retDate: new DateTimeImmutable("2019-01-20"), 
//   remarks:"returned by Juancho"
// );

$db->assignAsset($asset1, $user2, new DateTimeImmutable('2019-01-20'), "");
$a2 = $db->getAssignedAssets($user2);

# Remove changes to data
$db->deleteUser($user1);
$db->deleteUser($user2);
$db->deleteUser($user3);
$db->deleteAsset($asset1);
$db->deleteAsset($asset2);
$db->deleteAsset($asset3);
$db->deleteAsset($asset4);

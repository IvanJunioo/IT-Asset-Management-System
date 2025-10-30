<?php

declare (strict_types= 1);

include_once '../src/model/user.php';
include_once '../src/model/asset.php';

interface FacultyDatabaseInterface {
  public function searchAsset(string $asset): array;
  public function filterAsset(string $category): array;
  //viewAsset, viewActlog
}

interface AdminDatabaseInterface extends FacultyDatabaseInterface{
  public function addAsset(Asset $asset): bool;
  public function assignAsset(Asset $asset, User $user): bool;
  public function unassignAsset(Asset $asset): bool;
  public function modifyAsset(Asset $old, Asset $new): bool;
}

class Database implements AdminDatabaseInterface {
  public function searchAsset(string $asset) : array {
    return [];
  }
  public function filterAsset(string $category) : array {
    return [];
  }

  public function addAsset(Asset $asset): bool {
    try {
      require_once "src/includes/dbh-inc.php";
      
      $query = "INSERT INTO asset (SerialNum, ProcNum, PurchaseDate, Specs, Remarks, Status, ShortDesc, Price, URL, ActLog) VALUES (?,?,?,?,?,?,?,?,?,?);"; 

      $pdo->prepare($query)->execute([
        $asset->getSerialNum(), 
        $asset->getProcNum(),
        $asset->getPurchaseDate(),
        $asset->getSpecs(),
        $asset->getRemarks(),
        $asset->getStatus()->name,
        $asset->getDescription(),
        $asset->getPrice(),
        $asset->getUrl(),
        $asset->getActlog()
      ]);

      return true;
    } catch (PDOException $e) {
      return false;
    }
  }
  public function assignAsset(Asset $asset, User $user): bool{
    return true;
  }
  public function unassignAsset(Asset $asset): bool {
    return true;
  }
  public function modifyAsset(Asset $old, Asset $new): bool {
    return true;
  }

  public function addUser(User $user) {;}
  public function deleteUser(User $user) {;}
  public function modifyUser(User $old, User $new) {;}
}

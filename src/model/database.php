<?php

declare (strict_types= 1);

include '../src/model/user.php';
include '../src/model/asset.php';

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
    return true;
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

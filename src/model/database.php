<?php

declare (strict_types= 1);

include_once 'user.php';
include_once 'asset.php';

interface FacultyDatabaseInterface {
  public function searchAsset(string $asset): array;
  public function filterAsset(string $category): array;
  //viewAsset, viewActlog
}

interface AdminDatabaseInterface extends FacultyDatabaseInterface{
  public function addAsset(Asset $asset): bool;
  public function assignAsset(Asset $asset, User $user, string $assDate, string $remarks): bool;
  public function unassignAsset(Asset $asset): bool;
  public function updateAsset(Asset $asset): bool;
}

interface DatabaseInterface extends AdminDatabaseInterface {
  public function deleteAsset(Asset $asset): bool;
  public function addUser(User $user) : bool;
  public function deleteUser(User $user) : bool;
  public function updateUser(User $user) : bool;
}

class Database implements DatabaseInterface {
  private PDO $_pdo;

  public function __construct(PDO $pdo){
    $this->_pdo = $pdo;
  }

  public function searchAsset(string $asset) : array {
    return [];
  }

  public function filterAsset(string $filters) : array {
    return [];
  }
  
  public function addAsset(Asset $asset): bool {
    try {
      $query = "INSERT INTO asset (PropNum, SerialNum, ProcNum, PurchaseDate, Specs, Remarks, Status, ShortDesc, Price, URL, ActLog) VALUES (?,?,?,?,?,?,?,?,?,?,?);"; 
      
      $this->_pdo->prepare($query)->execute([
        $asset->getPropNum(),
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
  
  public function assignAsset(
      Asset $asset, 
      User $user,
      string $assDate,
      string $remarks,
    ): bool{

    if ($asset->getStatus() != AssetStatus::Unused){
      return false;
    }

    try {
      $query = "INSERT INTO assignment (PropNum, AssignDate, EmpID, Remarks) VALUES (?,?,?,?);"; 
      
      $this->_pdo->prepare($query)->execute([
        $asset->getPropNum(),
        $assDate,
        $user->getEmpID(),
        $remarks
      ]);
      $asset->setStatus(AssetStatus::Used);
      return $this->updateAsset($asset);
    } catch (PDOException $e) {
      return false;
    }
  }
  
  public function unassignAsset(Asset $asset): bool {
    if ($asset->getStatus() == AssetStatus::Used){
      $asset->setStatus(AssetStatus::Unused);
      return $this->updateAsset($asset);
    }
    return false;
  }
  
  public function updateAsset(Asset $asset): bool {
    try {
      $query = "UPDATE asset SET " . 
        "SerialNum = :snum, " .
        "ProcNum = :pnum, " .
        "PurchaseDate = :pdate, " .
        "Specs = :s, ".
        "Status = :st, " .
        "Remarks = :r, " .
        "ShortDesc = :d,  ".  
        "Price = :p, " . 
        "URL = :u, ".
        "ActLog = :al " .
        "WHERE asset.PropNum = :id;";
      
      $this->_pdo->prepare($query)->execute([
        ":id" => $asset->getPropNum(),
        ":snum" => $asset->getSerialNum(),
        ":pnum" => $asset->getProcNum(),
        ":pdate" => $asset->getPurchaseDate(),
        ":s" => $asset->getSpecs(),
        ":st" => $asset->getStatus()->name,
        ":r" => $asset->getRemarks(),
        ":d" => $asset->getDescription(),
        ":p" => $asset->getPrice(),
        ":u" => $asset->getUrl(),
        ":al" => $asset->getActlog(),
      ]);
      
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }
  
  public function deleteAsset(Asset $asset): bool {
    try {
      $query = "DELETE FROM asset WHERE asset.PropNum = :id;"; 
      # parsed: Last Name,First Name,Middle Name
      $id = $asset->getPropNum();

      $prep = $this->_pdo->prepare($query);
      $prep->bindParam(":id", $id);
      $prep->execute();
      
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }
  
  public function addUser(User $user): bool {
    try {
      $query = "INSERT INTO employee (EmpID, EmpMail, FName, MName, LName, Privilege, ActiveStatus, ActLog) VALUES (?,?,?,?,?,?,?,?);"; 
      
      $this->_pdo->prepare($query)->execute([
        $user->getEmpID(),
        $user->getEmail(),
        $user->getName()->first(),
        $user->getName()->middle(),
        $user->getName()->last(),
        $user->getPrivilege()->name,
        $user->isActive()? "Active" : "Inactive",
        $user->getActlog()
      ]);
  
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }

  public function deleteUser(User $user): bool {
    try {
      $query = "DELETE FROM employee WHERE employee.EmpID = :id;"; 
      # parsed: Last Name,First Name,Middle Name
      $id = $user->getEmpID();

      $prep = $this->_pdo->prepare($query);
      $prep->bindParam(":id", $id);
      $prep->execute();
      
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }
  
  public function updateUser(User $user): bool {
    try {
      $query = "UPDATE employee SET " . 
        "EmpMail = :mail, " .
        "FName = :fn, " .
        "MName = :mn, " .
        "LName = :ln, ".
        "Privilege = :priv, " .
        "ActiveStatus = :as, " .
        "ActLog = :al,  ".  
        "WHERE employee.EmpID = :id;";
            
      $this->_pdo->prepare($query)->execute([
        ":id" => $user->getEmpID(),
        ":mail" => $user->getEmail(),
        ":fn" => $user->getName()->first(),
        ":mn" => $user->getName()->middle(),
        ":ln" => $user->getName()->last(),
        ":priv" => $user->getPrivilege()->name,
        ":as" => $user->isActive()? "Active" : "Inactive",
        ":al" => $user->getActlog(),
      ]);
      
      return true;
    } catch (PDOException $e) {
      return false;
    }
  }
}

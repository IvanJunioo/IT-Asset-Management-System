<?php

declare (strict_types= 1);

include_once 'user.php';
include_once 'asset.php';

interface FacultyDatabaseInterface {
  public function searchAsset(
    float $price_min,
    float $price_max,
    DateTimeImmutable $base_date,
    DateTimeImmutable $end_date,
    array $status,

    string $propNum,
    string $procNum,
    string $serialNum,
    string $specs,
    string $description,
    string $remarks,
  ): array | bool;
  public function getAssignedAssets(User $user): array | bool;
}

interface AdminDatabaseInterface extends FacultyDatabaseInterface{
  public function searchUser(
    string $empID,
    Fullname $fullname,
    string $email,
    array $isActive,
    array $privileges,
  ): array | bool;
  public function addAsset(Asset $asset): bool;
  public function assignAsset(
    Asset $asset, 
    User $user, 
    DateTimeImmutable $assDate, 
    string $remarks
  ): bool;
  public function unassignAsset(
    Asset $asset, 
    DateTimeImmutable $assDate, 
    DateTimeImmutable $retDate,
    string $remarks, 
  ): bool;
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
  
  public function searchAsset(
    float $price_min = 0,
    float $price_max = 10**12 - 0.01,
    DateTimeImmutable $base_date = new DateTimeImmutable("0001-00-00"),
    DateTimeImmutable $end_date = new DateTimeImmutable("now"),
    array $status = [AssetStatus::Used, AssetStatus::Unused, AssetStatus::InRepair, AssetStatus::Broken],
    
    string $propNum = "",
    string $procNum = "",
    string $serialNum = "",
    string $specs = "",
    string $description = "",
    string $remarks = "",
    ): array | bool { 
      $st = implode(',',array_fill(0, count($status), '?'));
      $query = "SELECT * FROM asset WHERE 
        Status IN ($st)
        AND Price >= ? 
        AND Price <= ?
        AND PurchaseDate >= ? 
        AND PurchaseDate <= ?
        AND PropNum LIKE ?
        AND ProcNum LIKE ?
        AND SerialNum LIKE ?
        AND ShortDesc LIKE ?
        AND Specs LIKE ?
        AND Remarks LIKE ?
      ;";
      
      try {
        $stmt = $this->_pdo->prepare($query);
        
        foreach ($status as $s ) {
          $params[] = $s->name;
        }
        
        $params[] = "$price_min";
        $params[] = "$price_max";
        $params[] = $base_date->format("Y-m-d");
        $params[] = $end_date->format("Y-m-d");
        $params[] = "%$propNum%";
        $params[] = "%$procNum%";
        $params[] = "%$serialNum%";
        $params[] = "%$description%";
        $params[] = "%$specs%";
        $params[] = "%$remarks%";
        
        $stmt->execute($params);
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $assets = []; # Initially empty list (not null)
        foreach ($result as $asset) {
          $assets[] = new Asset(
            propNum: $asset["PropNum"],
            procNum: $asset["ProcNum"],
            serialNum: $asset["SerialNum"],
            date: $asset["PurchaseDate"],
            specs: $asset["Specs"],
            desc: $asset["ShortDesc"],
            url: $asset["URL"],
            remarks: $asset["Remarks"],
            price: (float)$asset["Price"]
          );
        }
        
        return $assets;
      } catch(PDOException $e) {
        panic($e);
        return false;
      }
    }
    
    public function searchUser(
      string $empID = "",
      Fullname $fullname = new Fullname("", "", ""),
      string $email = "",
      array $isActive = ["Active", "Inactive"],
      array $privileges = [UserPrivilege::faculty, UserPrivilege::admin, UserPrivilege::superAdmin],
    ): array | bool {
      $act = implode(",", array_fill(0, count($isActive),"?"));
      $priv = implode(",", array_fill(0, count($privileges), "?"));
      $query = "SELECT * FROM employee WHERE 
        ActiveStatus IN ($act)
        AND Privilege IN ($priv)
        AND EmpID LIKE ?
        AND EmpMail LIKE ?
        AND FName LIKE ?
        AND MName LIKE ?
        AND LName LIKE ?
      ";

      try {
        $stmt = $this->_pdo->prepare($query);
        foreach ($isActive as $a) {$params[] = $a;}
        foreach ($privileges as $p) {$params[] = $p->name;}
        $params[] = "%$empID%";
        $params[] = "%$email%";
        $params[] = "%" . $fullname->first . "%";
        $params[] = "%" . $fullname->middle . "%";
        $params[] = "%" . $fullname->last . "%";

        $stmt->execute($params);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $emps = [];
        foreach ($res as $emp) {
          $id = $emp["EmpID"];
          $name = new Fullname($emp["FName"], $emp["MName"], $emp["LName"]);
          $email = $emp["EmpMail"];
          switch ($emp["Privilege"]) {
            case "SuperAdmin":
              $emps[] = new SuperAdmin($id, $name, $email);
              break;
            case "Admin":
              $emps[] = new Admin($id, $name, $email);
              break;
            default:
              $emps[] = new Faculty($id, $name, $email);
          }
        }

        return $emps;
      } catch (PDOException $e) {
        panic($e);
        return false;
      }

    }

    public function getAssignedAssets(User $user): array | bool {
      $query = "SELECT * FROM 
        assignment INNER JOIN asset ON 
        assignment.PropNum = asset.PropNum
        WHERE EmpID = :empid AND RetDate is NULL
      ";

      try {
        $stmt = $this->_pdo->prepare($query);
        $stmt->execute([
          ":empid" => $user->getEmpID(),
        ]);
      
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $assets = [];
        foreach ($res as $asset) {
          $assets[] = new Asset(
            propNum: $asset["PropNum"],
            procNum: $asset["ProcNum"],
            serialNum: $asset["SerialNum"],
            date: $asset["PurchaseDate"],
            specs: $asset["Specs"],
            desc: $asset["ShortDesc"],
            url: $asset["URL"],
            remarks: $asset["Remarks"],
            price: (float)$asset["Price"]
          );
        }
        
        return $assets;
      } catch (PDOException $e) {
        panic($e);
        return false;
      }
    }
    
    public function addAsset(Asset $asset): bool {
    $query = "INSERT INTO asset (PropNum, SerialNum, ProcNum, PurchaseDate, Specs, Remarks, Status, ShortDesc, Price, URL, ActLog) VALUES (?,?,?,?,?,?,?,?,?,?,?);"; 
    
    try {
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
      panic($e);
      return false;
    }
  }
  
  public function assignAsset(
      Asset $asset, 
      User $user,
      DateTimeImmutable $assDate,
      string $remarks,
    ): bool{

    if ($asset->getStatus() != AssetStatus::Unused){
      return false;
    }

    $asset->setStatus(AssetStatus::Used);
    $asset->assignTo($user);

    $query = "INSERT INTO assignment (PropNum, AssignDate, EmpID, Remarks) VALUES (?,?,?,?);"; 
    
    try {
      $this->_pdo->prepare($query)->execute([
        $asset->getPropNum(),
        $assDate->format("Y-m-d"),
        $user->getEmpID(),
        $remarks
      ]);
      
      return $this->updateAsset($asset);
    } catch (PDOException $e) {
      panic($e);
      return false;
    }
  }
  
  public function unassignAsset(
    Asset $asset, 
    DateTimeImmutable $assDate, 
    DateTimeImmutable $retDate,
    string $remarks = "",  
  ): bool {
    if ($asset->getStatus() != AssetStatus::Used){
      return false;
    }
    $asset->setStatus(AssetStatus::Unused);
    $asset->assignTo(null);
    $query = "UPDATE assignment SET 
      RetDate = :rd,
      Remarks = :r 
      WHERE assignment.PropNum = :pn 
      AND assignment.AssignDate = :ad 
    ;";
    
    
    try {
      $this->_pdo->prepare($query)->execute([
        ":rd" => $retDate->format('Y-m-d'),
        ":r" => $remarks,
        ":pn" => $asset->getPropNum(),
        ":ad" => $assDate->format('Y-m-d'),
      ]);
      
      return $this->updateAsset($asset);
    } catch (PDOException $e) {
      panic($e);
      return false;
    }
  }
  
  public function updateAsset(Asset $asset): bool {
    $query = "UPDATE asset SET 
      SerialNum = :snum,
      ProcNum = :pnum,
      PurchaseDate = :pdate,
      Specs = :s,
      Status = :st,
      Remarks = :r,
      ShortDesc = :d,   
      Price = :p, 
      URL = :u,
      ActLog = :al
      WHERE PropNum = :id;";
    
    try {      
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
      panic($e);
      return false;
    }
  }
  
  public function deleteAsset(Asset $asset): bool {
    $query1 = "DELETE FROM assignment WHERE assignment.PropNum = ?;";
    $query2 = "DELETE FROM asset WHERE asset.PropNum = ?;"; 

    try {
      $this->_pdo->prepare($query1)->execute([$asset->getPropNum()]);
      $this->_pdo->prepare($query2)->execute([$asset->getPropNum()]);
    } catch (PDOException $e) {
      panic($e);
      return false;
    }

    return true;
  }
  
  public function addUser(User $user): bool {
    $query = "INSERT INTO employee (EmpID, EmpMail, FName, MName, LName, Privilege, ActiveStatus, ActLog) VALUES (?,?,?,?,?,?,?,?);"; 
    
    try {
      $this->_pdo->prepare($query)->execute([
        $user->getEmpID(),
        $user->getEmail(),
        $user->getName()->first,
        $user->getName()->middle,
        $user->getName()->last,
        $user->getPrivilege()->name,
        $user->isActive()? "Active" : "Inactive",
        $user->getActlog()
      ]);
  
      return true;
    } catch (PDOException $e) {
      panic($e);
      return false;
    }
  }

  public function deleteUser(User $user): bool {
    $query1 = "DELETE FROM assignment WHERE assignment.EmpID = ?;";
    $query2 = "DELETE FROM employee WHERE employee.EmpID = ?;"; 

    try {
      $this->_pdo->prepare($query1)->execute([$user->getEmpID()]);
      $this->_pdo->prepare($query2)->execute([$user->getEmpID()]);
    } catch (PDOException $e) {
      panic($e);
      return false;
    }

    return true;
  }
  
  public function updateUser(User $user): bool {
    $query = "UPDATE employee SET 
      EmpMail = :mail,
      FName = :fn,
      MName = :mn,
      LName = :ln,
      Privilege = :priv,
      ActiveStatus = :as,
      ActLog = :al   
      WHERE employee.EmpID = :id;";
          
    try {
      $this->_pdo->prepare($query)->execute([
        ":id" => $user->getEmpID(),
        ":mail" => $user->getEmail(),
        ":fn" => $user->getName()->first,
        ":mn" => $user->getName()->middle,
        ":ln" => $user->getName()->last,
        ":priv" => $user->getPrivilege()->name,
        ":as" => $user->isActive()? "Active" : "Inactive",
        ":al" => $user->getActlog(),
      ]);
      
      return true;
    } catch (PDOException $e) {
      panic($e);
      return false;
    }
  }

}

function panic(PDOException $err) {
  echo "<br>" . $err->getMessage() . "<br>";
}

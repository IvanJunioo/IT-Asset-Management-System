<?php

declare (strict_types= 1);

include_once 'user.php';
include_once 'asset.php';

interface DatabaseInterface {
  public function searchAsset(
    # Returns array of Asset objects if successful
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
    int $limit,
  ): array;
  public function getAssignedAssets(User $user): array;
	public function getCurrAssignedUser(Asset $asset): ?User;
  public function searchUser(
    string $empID,
    Fullname $fullname,
    string $email,
    array $isActive,
    array $privileges,
    int $limit,
  ): array;
  public function addAsset(Asset $asset): void;
  public function updateAsset(Asset $asset): void;
  public function deleteAsset(Asset $asset): void;
  public function assignAsset(
    Asset $asset, 
    User $user, 
    DateTimeImmutable $assDate, 
    string $remarks
  ): void;
  public function unassignAsset(
    Asset $asset, 
    DateTimeImmutable $assDate, 
    DateTimeImmutable $retDate,
    string $remarks, 
  ): void;
  public function addUser(User $user) : void;
  public function deleteUser(User $user) : void;
  public function updateUser(User $user) : void;
  public function systemLog(
    User $user,
    string $log,
    array $metadata,
  ) : void;
  public function getLogs(int $limit) : array;
}

class Database implements DatabaseInterface {
  private PDO $_pdo;
  
  public function __construct(PDO $pdo){
    $this->_pdo = $pdo;
  }
  
  public function searchAsset(
    float $price_min = 0,
    float $price_max = 10**12 - 0.01,
    ?DateTimeImmutable $base_date = null,
    ?DateTimeImmutable $end_date = null,
    ?array $status = null,  # must be nonempty
    
    string $propNum = "",
    string $procNum = "",
    string $serialNum = "",
    string $specs = "",
    string $description = "",
    string $remarks = "",
    int $limit = 50,
  ): array { 
    $base_date ??= new DateTimeImmutable("0001-01-01");
    $end_date ??= new DateTimeImmutable("9999-12-31");;
    $status ??= AssetStatus::cases();
    
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
      LIMIT $limit
    ;";
    
    $stmt = $this->_pdo->prepare($query);
    
    foreach ($status as $s ) {
      $params[] = $s->name;
    }
    
    $params[] = (string) $price_min;
    $params[] = (string) $price_max;
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
    foreach ($result as $ass) {
      $asset = new Asset(
        propNum: $ass["PropNum"],
        procNum: $ass["ProcNum"],
        serialNum: $ass["SerialNum"],
        date: $ass["PurchaseDate"],
        specs: $ass["Specs"],
        desc: $ass["ShortDesc"],
        url: $ass["URL"],
        remarks: $ass["Remarks"],
        price: (float)$ass["Price"],
      );

      $asset->setStatus(AssetStatus::fromStr($ass["Status"]));
      $user = $this->getCurrAssignedUser($asset);
      if ($user !== null){
        $asset->assignTo($user);
      }
      
      $assets[] = $asset;
    }
    return $assets;
  }

	public function getCurrAssignedUser(Asset $asset): ?User {
		$query = "SELECT * FROM 
      assignment INNER JOIN employee ON 
      assignment.EmpID = employee.EmpID
      WHERE PropNum = :pnum AND RetDate is NULL
    ";

    $stmt = $this->_pdo->prepare($query);
    $stmt->execute([
      ":pnum" => $asset->getPropNum(),
    ]);
  
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $user) {
      $privilege = $user['Privilege'];
      $name = new Fullname($user['FName'], $user['MName'], $user['LName']);

      $ret = match ($privilege) {
        'Super Admin' => new SuperAdmin(
          empID: $user['EmpID'],
          name: $name,
          email: $user['EmpMail']
        ),
        'Admin' => new Admin(
          empID: $user['EmpID'],
          name: $name,
          email: $user['EmpMail']
        ),
        default => new Faculty(
          empID: $user['EmpID'],
          name: $name,
          email: $user['EmpMail']
        ),
      };

      if ($user['ActiveStatus'] == 'Inactive'){
        $user->setActiveStatus(False);
      }

      return $ret;
    }
    return null;
	}

  public function searchUser(
    string $empID = "",
    ?Fullname $fullname = null,
    string $email = "",
    array $isActive = ["Active", "Inactive"],
    ?array $privileges = null, # must be nonempty
    int $limit = 50,
  ): array {
    $fullname ??= new Fullname();
    $privileges ??= UserPrivilege::cases(); 
      
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
      LIMIT $limit
    ";

    $stmt = $this->_pdo->prepare($query);
    foreach ($isActive as $a) {$params[] = $a;}
    foreach ($privileges as $p) {$params[] = $p->name;}
    $params[] = "%$empID%";
    $params[] = "%$email%";
    $params[] = "%$fullname->first%";
    $params[] = "%$fullname->middle%";
    $params[] = "%$fullname->last%";

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
      end($emps)->setActiveStatus($emp["ActiveStatus"] == "Active");
      end($emps)->setActlog($emp["ActLog"]);
    }

    return $emps;
  }

  public function getAssignedAssets(User $user): array {
    $query = "SELECT * FROM 
      assignment INNER JOIN asset ON 
      assignment.PropNum = asset.PropNum
      WHERE EmpID = :empid AND RetDate is NULL
    ";

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
  }
    
  public function addAsset(Asset $asset): void {
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
  }
  
  public function assignAsset(
      Asset $asset, 
      User $user,
      DateTimeImmutable $assDate,
      string $remarks,
    ): void {

    if ($asset->getStatus() != AssetStatus::Unused){
      return;
    }

    $asset->setStatus(AssetStatus::Used);
    $asset->assignTo($user);

    $query = "INSERT INTO assignment (PropNum, AssignDate, EmpID, Remarks) VALUES (?,?,?,?);"; 
    
    $this->_pdo->prepare($query)->execute([
      $asset->getPropNum(),
      $assDate->format("Y-m-d"),
      $user->getEmpID(),
      $remarks
    ]);
    
    $this->updateAsset($asset);
  }
  
  public function unassignAsset(
    Asset $asset, 
    DateTimeImmutable $assDate, 
    DateTimeImmutable $retDate,
    string $remarks = "",  
  ): void {
    if ($asset->getStatus() != AssetStatus::Used){
      return;
    }
    $asset->setStatus(AssetStatus::Unused);
    $asset->assignTo(null);
    $query = "UPDATE assignment SET 
      RetDate = :rd,
      Remarks = :r 
      WHERE assignment.PropNum = :pn 
      AND assignment.AssignDate = :ad 
    ;";
    
    
    $this->_pdo->prepare($query)->execute([
      ":rd" => $retDate->format('Y-m-d'),
      ":r" => $remarks,
      ":pn" => $asset->getPropNum(),
      ":ad" => $assDate->format('Y-m-d'),
    ]);
    
    $this->updateAsset($asset);
  }
  
  public function updateAsset(Asset $asset): void {
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
  }
  
  public function deleteAsset(Asset $asset): void {
    $query1 = "DELETE FROM assignment WHERE assignment.PropNum = ?;";
    $query2 = "DELETE FROM asset WHERE asset.PropNum = ?;"; 

    $this->_pdo->prepare($query1)->execute([$asset->getPropNum()]);
    $this->_pdo->prepare($query2)->execute([$asset->getPropNum()]);
  }
  
  public function addUser(User $user): void {
    $query = "INSERT INTO employee (EmpID, EmpMail, FName, MName, LName, Privilege, ActiveStatus, ActLog) VALUES (?,?,?,?,?,?,?,?);"; 
    
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
  }

  public function deleteUser(User $user): void {
    $query1 = "DELETE FROM assignment WHERE assignment.EmpID = ?;";
    $query2 = "DELETE FROM employee WHERE employee.EmpID = ?;"; 

    $this->_pdo->prepare($query1)->execute([$user->getEmpID()]);
    $this->_pdo->prepare($query2)->execute([$user->getEmpID()]);
  }
  
  public function updateUser(User $user): void {
    $query = "UPDATE employee SET 
      EmpMail = :mail,
      FName = :fn,
      MName = :mn,
      LName = :ln,
      Privilege = :priv,
      ActiveStatus = :astat,
      ActLog = :al   
      WHERE employee.EmpID = :id;";
          
    $this->_pdo->prepare($query)->execute([
      ":id" => $user->getEmpID(),
      ":mail" => $user->getEmail(),
      ":fn" => $user->getName()->first,
      ":mn" => $user->getName()->middle,
      ":ln" => $user->getName()->last,
      ":priv" => $user->getPrivilege()->name,
      ":astat" => $user->isActive()? "Active" : "Inactive",
      ":al" => $user->getActlog(),
    ]);      
  }

  public function systemLog(
    User $user,
    string $log,
    array $metadata,
  ) : void {
    $query = "INSERT INTO log (EmpID, Log, Metadata) VALUES (?,?,?)";

    $this->_pdo->prepare($query)->execute([
      $user->getEmpID(),
      $log,
      json_encode($metadata),
    ]);
  }

  public function getLogs(int $limit = 50) : array {
    $query = "SELECT * FROM log LIMIT $limit";
    $stmt = $this->_pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }
}

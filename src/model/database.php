<?php
// DEPRECATED
// declare (strict_types=1);

// include_once 'user.php';
// include_once 'asset.php';

// interface DatabaseInterface {
//   public function searchAsset(
//     # Returns array of Asset objects if successful
//     float $price_min,
//     float $price_max,
//     DateTimeImmutable $base_date,
//     DateTimeImmutable $end_date,
//     array $status,
//     string $propNum,
//     string $procNum,
//     string $serialNum,
//     string $specs,
//     string $description,
//     string $remarks,
//     int $limit,
//   ): array;
//   public function getAssignedAssets(User $user): array;
// 	public function getCurrAssignedUser(Asset $asset): ?User;
//   public function searchUser(
//     string $empID,
//     Fullname $fullname,
//     string $email,
//     array $isActive,
//     array $privileges,
//     int $limit,
//   ): array;
//   public function addAsset(Asset $asset): void;
//   public function updateAsset(Asset $asset): void;
//   public function deleteAsset(Asset $asset): void;
//   public function assignAsset(
//     Asset $asset, 
//     User $assigner,
//     User $assignee,
//     DateTimeImmutable $assDate, 
//     string $remarks
//   ): void;
//   public function returnAsset(
//     Asset $asset, 
//     DateTimeImmutable $retDate,
//     string $remarks, 
//   ): void;
//   public function addUser(User $user) : void;
//   public function deleteUser(User $user) : void;
//   public function updateUser(User $user) : void;
//   public function systemLog(
//     User $user,
//     string $log,
//     array $metadata,
//   ) : void;
//   public function getLogs(int $limit) : array;
// }

// class Database implements DatabaseInterface {
//   public function __construct(
//     public readonly PDO $pdo,
//   ) {}
  
//   public function searchAsset(
//     float $price_min = 0,
//     float $price_max = 10**12 - 0.01,
//     ?DateTimeImmutable $base_date = null,
//     ?DateTimeImmutable $end_date = null,
//     ?array $status = null,  # must be nonempty
    
//     string $propNum = "",
//     string $procNum = "",
//     string $serialNum = "",
//     string $specs = "",
//     string $description = "",
//     string $remarks = "",
//     int $limit = 50,
//   ): array { 
//     $base_date ??= new DateTimeImmutable("0001-01-01");
//     $end_date ??= new DateTimeImmutable("9999-12-31");
//     $status ??= AssetStatus::cases();
    
//     $st = implode(',',array_fill(0, count($status), '?'));
//     $query = "SELECT * FROM asset WHERE 
//       Status IN ($st)
//       AND Price >= ? 
//       AND Price <= ?
//       AND PurchaseDate >= ? 
//       AND PurchaseDate <= ?
//       AND PropNum LIKE ?
//       AND ProcNum LIKE ?
//       AND SerialNum LIKE ?
//       AND ShortDesc LIKE ?
//       AND Specs LIKE ?
//       AND Remarks LIKE ?
//       LIMIT $limit
//     ;";
    
//     $stmt = $this->pdo->prepare($query);
    
//     foreach ($status as $s ) {
//       $params[] = $s->name;
//     }
    
//     $params[] = (string) $price_min;
//     $params[] = (string) $price_max;
//     $params[] = $base_date->format("Y-m-d");
//     $params[] = $end_date->format("Y-m-d");
//     $params[] = "%$propNum%";
//     $params[] = "%$procNum%";
//     $params[] = "%$serialNum%";
//     $params[] = "%$description%";
//     $params[] = "%$specs%";
//     $params[] = "%$remarks%";
    
//     $stmt->execute($params);
    
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
//     $assets = []; # Initially empty list (not null)
//     foreach ($result as $ass) {
//       $asset = new Asset(
//         propNum: $ass["PropNum"],
//         procNum: $ass["ProcNum"],
//         serialNum: $ass["SerialNum"],
//         purchaseDate: $ass["PurchaseDate"],
//         specs: $ass["Specs"],
//         description: $ass["ShortDesc"],
//         status: AssetStatus::from($ass["Status"]),
//         url: $ass["URL"],
//         remarks: $ass["Remarks"],
//         price: (float)$ass["Price"],
//       );

//       $user = $this->getCurrAssignedUser($asset);
//       if ($user !== null){
//         $asset->assignTo($user);
//       }
      
//       $assets[] = $asset;
//     }
//     return $assets;
//   }

// 	public function getCurrAssignedUser(Asset $asset): ?User {
// 		$query = "SELECT * FROM 
//       assignment INNER JOIN employee ON 
//       assignment.AssigneeID = employee.EmpID
//       WHERE PropNum = :pnum AND ReturnDateTime is NULL
//     ";

//     $stmt = $this->pdo->prepare($query);
//     $stmt->execute([
//       ":pnum" => $asset->propNum,
//     ]);
  
//     $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     foreach ($res as $user) {
//       $privilege = $user['Privilege'];
//       $name = new Fullname($user['FName'], $user['MName'], $user['LName']);
//       $status = $user['ActiveStatus'] === 'Active';

//       $ret = match (UserPrivilege::from($privilege)) {
//         UserPrivilege::SuperAdmin => new SuperAdmin(
//           empID: $user['EmpID'],
//           name: $name,
//           email: $user['EmpMail'],
//           isActive: $status,
//         ),
//         UserPrivilege::Admin => new Admin(
//           empID: $user['EmpID'],
//           name: $name,
//           email: $user['EmpMail'],
//           isActive: $status,
//         ),
//         UserPrivilege::Faculty => new Faculty(
//           empID: $user['EmpID'],
//           name: $name,
//           email: $user['EmpMail'],
//           isActive: $status,
//         ),
//       };

//       return $ret;
//     }
//     return null;
// 	}

//   public function searchUser(
//     string $empID = "",
//     ?Fullname $fullname = null,
//     string $email = "",
//     array $isActive = ["Active", "Inactive"],
//     ?array $privileges = null, # must be nonempty
//     int $limit = 50,
//   ): array {
//     $fullname ??= new Fullname();
//     $privileges ??= UserPrivilege::cases(); 
      
//     $act = implode(",", array_fill(0, count($isActive),"?"));
//     $priv = implode(",", array_fill(0, count($privileges), "?"));
//     $query = "SELECT * FROM employee WHERE 
//       ActiveStatus IN ($act)
//       AND Privilege IN ($priv)
//       AND EmpID LIKE ?
//       AND EmpMail LIKE ?
//       AND FName LIKE ?
//       AND MName LIKE ?
//       AND LName LIKE ?
//       LIMIT $limit
//     ";

//     $stmt = $this->pdo->prepare($query);
//     foreach ($isActive as $a) {$params[] = $a;}
//     foreach ($privileges as $p) {$params[] = $p->value;}
//     $params[] = "%$empID%";
//     $params[] = "%$email%";
//     $params[] = "%$fullname->first%";
//     $params[] = "%$fullname->middle%";
//     $params[] = "%$fullname->last%";

//     $stmt->execute($params);
//     $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     $emps = [];
//     foreach ($res as $emp) {
//       $id = $emp["EmpID"];
//       $name = new Fullname($emp["FName"], $emp["MName"], $emp["LName"]);
//       $email = $emp["EmpMail"];
//       $isActive = $emp["ActiveStatus"] == "Active";

//       $employee = match (UserPrivilege::from($emp["Privilege"])) {
//         UserPrivilege::SuperAdmin => new SuperAdmin($id, $name, $email, $isActive),
//         UserPrivilege::Admin => new Admin($id, $name, $email, $isActive),
//         UserPrivilege::Faculty => new Faculty($id, $name, $email, $isActive),
//       };

//       $emps[] = $employee;
//     }

//     return $emps;
//   }

//   public function getAssignedAssets(User $user): array {
//     $query = "SELECT * FROM 
//       assignment INNER JOIN asset ON 
//       assignment.PropNum = asset.PropNum
//       WHERE EmpID = :empid AND ReturnDateTime is NULL
//     ";

//     $stmt = $this->pdo->prepare($query);
//     $stmt->execute([
//       ":empid" => $user->empID,
//     ]);
  
//     $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     $assets = [];
//     foreach ($res as $asset) {
//       $assets[] = new Asset(
//         propNum: $asset["PropNum"],
//         procNum: $asset["ProcNum"],
//         serialNum: $asset["SerialNum"],
//         purchaseDate: $asset["PurchaseDate"],
//         specs: $asset["Specs"],
//         description: $asset["ShortDesc"],
//         status: $asset["Status"],
//         url: $asset["URL"],
//         remarks: $asset["Remarks"],
//         price: (float)$asset["Price"]
//       );
//     }
    
//     return $assets;
//   }
    
//   public function addAsset(Asset $asset): void {
//     $query = "INSERT INTO asset (PropNum, SerialNum, ProcNum, PurchaseDate, Specs, Remarks, Status, ShortDesc, Price, URL) VALUES (?,?,?,?,?,?,?,?,?,?);"; 
    
//     $this->pdo->prepare($query)->execute([
//       $asset->propNum,
//       $asset->serialNum, 
//       $asset->procNum,
//       $asset->purchaseDate,
//       $asset->specs,
//       $asset->remarks,
//       $asset->status->value,
//       $asset->description,
//       $asset->price,
//       $asset->url,
//     ]);      
//   }
  
//   public function assignAsset(
//       Asset $asset, 
//       User $assigner,
//       User $assignee,
//       DateTimeImmutable $assDate,
//       string $remarks,
//     ): void {

//     if ($asset->status !== AssetStatus::Unassigned){
//       return;
//     }

//     $asset->status = AssetStatus::Assigned;
//     $asset->assignTo($assignee);

//     $query = "INSERT INTO assignment (PropNum, AssignDateTime, AssignerID, AssigneeID, Remarks) VALUES (?,?,?,?,?);"; 
    
//     $this->pdo->prepare($query)->execute([
//       $asset->propNum,
//       $assDate->format("Y-m-d H:i:s"),
//       $assigner->empID,
//       $assignee->empID,
//       $remarks,
//     ]);
    
//     $this->updateAsset($asset);
//   }
  
//   public function returnAsset(
//     Asset $asset, 
//     DateTimeImmutable $retDate,
//     string $remarks = "",  
//   ): void {
//     if ($asset->status !== AssetStatus::Assigned){
//       return;
//     }
//     $asset->status = AssetStatus::Unassigned;
//     $asset->assignTo(null);

//     $query = "UPDATE assignment SET 
//       ReturnDateTime = :rd,
//       Remarks = :r 
//       WHERE assignment.PropNum = :pn 
//       AND assignment.ReturnDateTime = NULL 
//     ;";
    
    
//     $this->pdo->prepare($query)->execute([
//       ":rd" => $retDate->format('Y-m-d H:i:s'),
//       ":r" => $remarks,
//       ":pn" => $asset->propNum,
//     ]);
    
//     $this->updateAsset($asset);
//   }
  
//   public function updateAsset(Asset $asset): void {
//     $query = "UPDATE asset SET 
//       SerialNum = :snum,
//       ProcNum = :pnum,
//       PurchaseDate = :pdate,
//       Specs = :s,
//       Status = :st,
//       Remarks = :r,
//       ShortDesc = :d,   
//       Price = :p, 
//       URL = :u
//       WHERE PropNum = :id;";
    
//     $this->pdo->prepare($query)->execute([
//       ":id" => $asset->propNum,
//       ":snum" => $asset->serialNum,
//       ":pnum" => $asset->procNum,
//       ":pdate" => $asset->purchaseDate,
//       ":s" => $asset->specs,
//       ":st" => $asset->status->value,
//       ":r" => $asset->remarks,
//       ":d" => $asset->description,
//       ":p" => $asset->price,
//       ":u" => $asset->url,
//     ]);      
//   }
  
//   public function deleteAsset(Asset $asset): void {
//     if ($asset->status != AssetStatus::ToCondemn) {
//       return;
//     }
    
//     $query = "UPDATE asset SET Status = :st WHERE PropNum = :id;"; 
//     $this->pdo->prepare($query)->execute([
//       ":id" => $asset->propNum,
//       ":st" => "Condemned",
//     ]);
//   }
  
//   public function addUser(User $user): void {
//     $query = "INSERT INTO employee (EmpID, EmpMail, FName, MName, LName, Privilege, ActiveStatus) VALUES (?,?,?,?,?,?,?);"; 
    
//     $this->pdo->prepare($query)->execute([
//       $user->empID,
//       $user->email,
//       $user->name->first,
//       $user->name->middle,
//       $user->name->last,
//       $user->getPrivilege()->value,
//       $user->isActive? "Active" : "Inactive",
//     ]);  
//   }

//   public function deleteUser(User $user): void {
//     // $assets = $this->getAssignedAssets($user);
//     // foreach ($assets as $asset){
//     //   $now = new DateTimeImmutable("now");
//     //   $this->returnAsset($asset, $now);
//     // }
//     $query = "UPDATE employee SET ActiveStatus = :astat WHERE EmpID = :id;";
//     $this->pdo->prepare($query)->execute([
//       ":id" => $user->empID,
//       ":astat" => "Inactive"
//     ]);
    
//     // $query1 = "DELETE FROM assignment WHERE assignment.EmpID = ?;";
//     // $query2 = "DELETE FROM employee WHERE employee.EmpID = ?;"; 

//     // $this->pdo->prepare($query1)->execute([$user->empID]);
//     // $this->pdo->prepare($query2)->execute([$user->empID]);
//   }
  
//   public function updateUser(User $user): void {
//     $query = "UPDATE employee SET 
//       EmpMail = :mail,
//       FName = :fn,
//       MName = :mn,
//       LName = :ln,
//       Privilege = :priv,
//       ActiveStatus = :astat
//       WHERE employee.EmpID = :id;";
          
//     $this->pdo->prepare($query)->execute([
//       ":id" => $user->empID,
//       ":mail" => $user->email,
//       ":fn" => $user->name->first,
//       ":mn" => $user->name->middle,
//       ":ln" => $user->name->last,
//       ":priv" => $user->getPrivilege()->name,
//       ":astat" => $user->isActive? "Active" : "Inactive",
//     ]);      
//   }

//   public function systemLog(
//     User $user,
//     string $log,
//     array $metadata,
//   ) : void {
//     $query = "INSERT INTO actlog (ActorID, Log, Metadata) VALUES (?,?,?)";

//     $this->pdo->prepare($query)->execute([
//       $user->empID,
//       $log,
//       json_encode($metadata),
//     ]);
//   }

//   public function getLogs(int $limit = 50) : array {
//     $query = "SELECT * FROM actlog LIMIT $limit";
//     $stmt = $this->pdo->prepare($query);
//     $stmt->execute();
//     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
//     return $result;
//   }
// }

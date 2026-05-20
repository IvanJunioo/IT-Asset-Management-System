<?php

declare (strict_types=1);

require_once __DIR__ . '/../model/user.php';
require_once __DIR__ . '/../model/asset.php';

interface AssignmentRepoInterface {
  public function getAssignedAssets(User $user): array;
  public function getCurrAssignedUser(Asset $asset): ?User; 
  public function getAssignmentDate(Asset $asset): string; 
  public function getAssignmentRemarks(Asset $asset): string;
  public function assign(
    Asset $asset, 
    User $assigner,
    User $assignee,
    DateTimeImmutable $assDate, 
    string $remarks
  ): void;
  public function return(
    Asset $asset, 
    DateTimeImmutable $retDate,
    string $remarks, 
  ): void;
}

final class AssignmentRepo implements AssignmentRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}
  
	public function getCurrAssignedUser(Asset $asset): ?User {
		$query = "SELECT * FROM 
      assignment INNER JOIN employee ON 
      assignment.AssigneeID = employee.EmpID
      WHERE PropNum = :pnum AND ReturnDateTime is NULL
      LIMIT 1
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
      ":pnum" => $asset->propNum,
    ]);
  
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {return null;}

    return new User(
      empID: $user['EmpID'],
      name: new Fullname(
        first: $user['FName'], 
        last: $user['LName']
      ),
      email: $user['EmpMail'],
      privilege: UserPrivilege::from($user['Privilege']),
      isActive: $user['ActiveStatus'] === 'Active',
    );
	}

  public function getAssignmentDate(Asset $asset): string {
    $query = "SELECT * FROM 
      assignment WHERE PropNum = :pnum AND ReturnDateTime is NULL";
    
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([":pnum" => $asset->propNum]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $res[0]['AssignDateTime'];
  }

  public function getAssignmentRemarks(Asset $asset): string {
    $query = "SELECT * FROM 
      assignment WHERE PropNum = :pnum AND ReturnDateTime is NULL";
    
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([":pnum" => $asset->propNum]);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $res[0]['Remarks'];
  }

  public function getAssignedAssets(User $user): array {
    $query = "SELECT * FROM 
      assignment INNER JOIN asset ON 
      assignment.PropNum = asset.PropNum
      WHERE assignment.AssigneeID = :empid AND ReturnDateTime is NULL
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
      ":empid" => $user->empID,
    ]);
  
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $assets = [];
    foreach ($res as $asset) {
      $_asset = new Asset(
        propNum: $asset["PropNum"],
        procNum: $asset["ProcNum"],
        serialNum: $asset["SerialNum"],
        purchaseDate: $asset["PurchaseDate"],
        specs: $asset["Specs"],
        description: $asset["ShortDesc"],
        status: AssetStatus::from($asset["Status"]),
        url: $asset["URL"],
        remarks: $asset["Remarks"],
        price: (float)$asset["Price"]
      );
      $_asset->assignTo($user);
      $assets[] = $_asset;
    }
    
    return $assets;
  }

  public function assign(
      Asset $asset, 
      User $assigner,
      User $assignee,
      DateTimeImmutable $assDate,
      string $remarks,
    ): void {
    $query = "INSERT INTO assignment (PropNum, AssignDateTime, AssignerID, AssigneeID, Remarks) VALUES (?,?,?,?,?);"; 

    $this->pdo->prepare($query)->execute([
      $asset->propNum,
      $assDate->format("Y-m-d H:i:s"),
      $assigner->empID,
      $assignee->empID,
      $remarks,
    ]);
  }
  
  public function return(
    Asset $asset, 
    DateTimeImmutable $retDate,
    string $remarks = "",  
  ): void {
    $query = "UPDATE assignment SET 
      ReturnDateTime = :rd,
      Remarks = :r
      WHERE assignment.PropNum = :pn 
      AND assignment.ReturnDateTime IS NULL 
    ;";
    
    $this->pdo->prepare($query)->execute([
      ":rd" => $retDate->format('Y-m-d H:i:s'),
      ":r" => " $remarks",
      ":pn" => $asset->propNum,
    ]);
  }
}

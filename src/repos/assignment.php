<?php

declare (strict_types=1);

include_once 'user.php';
include_once 'asset.php';

interface AssignmentRepoInterface {
  public function getAssignedAssets(User $user): array;
  public function getCurrAssignedUser(Asset $asset): ?User;  
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
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
      ":pnum" => $asset->propNum,
    ]);
  
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($res as $user) {
      $privilege = $user['Privilege'];
      $name = new Fullname($user['FName'], $user['MName'], $user['LName']);
      $status = $user['ActiveStatus'] === 'Active';

      $ret = match (UserPrivilege::from($privilege)) {
        UserPrivilege::SuperAdmin => new SuperAdmin(
          empID: $user['EmpID'],
          name: $name,
          email: $user['EmpMail'],
          isActive: $status,
        ),
        UserPrivilege::Admin => new Admin(
          empID: $user['EmpID'],
          name: $name,
          email: $user['EmpMail'],
          isActive: $status,
        ),
        UserPrivilege::Faculty => new Faculty(
          empID: $user['EmpID'],
          name: $name,
          email: $user['EmpMail'],
          isActive: $status,
        ),
      };

      return $ret;
    }
    return null;
	}

  public function getAssignedAssets(User $user): array {
    $query = "SELECT * FROM 
      assignment INNER JOIN asset ON 
      assignment.PropNum = asset.PropNum
      WHERE EmpID = :empid AND ReturnDateTime is NULL
    ";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
      ":empid" => $user->empID,
    ]);
  
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $assets = [];
    foreach ($res as $asset) {
      $assets[] = new Asset(
        propNum: $asset["PropNum"],
        procNum: $asset["ProcNum"],
        serialNum: $asset["SerialNum"],
        purchaseDate: $asset["PurchaseDate"],
        specs: $asset["Specs"],
        description: $asset["ShortDesc"],
        status: $asset["Status"],
        url: $asset["URL"],
        remarks: $asset["Remarks"],
        price: (float)$asset["Price"]
      );
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
      Remarks = CONCAT(Remarks, :r)
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

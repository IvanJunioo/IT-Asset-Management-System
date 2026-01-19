<?php

declare (strict_types=1);

include_once '../model/user.php';

interface UserRepoInterface {
  public function search(UserSearchCriteria $criteria): array;
  public function count(UserSearchCriteria $criteria): int;

  public function add(User $user) : void;
  public function delete(User $user) : void;
  public function update(User $user) : void;
}

final class UserRepo implements UserRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}

  public function search(UserSearchCriteria $criteria = new UserSearchCriteria()): array {      
    $act = implode(",", array_fill(0, count($criteria->isActive),"?"));
    $priv = implode(",", array_fill(0, count($criteria->privileges), "?"));
    $query = "SELECT * FROM employee WHERE 
      ActiveStatus IN ($act)
      AND Privilege IN ($priv)
      AND EmpID LIKE ?
      AND EmpMail LIKE ?
      AND FName LIKE ?
      AND MName LIKE ?
      AND LName LIKE ?
      LIMIT $criteria->limit
    ";

    $stmt = $this->pdo->prepare($query);
    foreach ($criteria->isActive as $a) {$params[] = $a;}
    foreach ($criteria->privileges as $p) {$params[] = $p->value;}
    $params[] = "%$criteria->empID%";
    $params[] = "%$criteria->email%";
    $params[] = "%" . $criteria->fullname->first . "%";
    $params[] = "%" . $criteria->fullname->middle . "%";
    $params[] = "%" . $criteria->fullname->last . "%";

    $stmt->execute($params);
    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $emps = [];
    foreach ($res as $emp) {
      $id = $emp["EmpID"];
      $name = new Fullname($emp["FName"], $emp["MName"], $emp["LName"]);
      $email = $emp["EmpMail"];
      $isActive = $emp["ActiveStatus"] == "Active";

      $employee = match (UserPrivilege::from($emp["Privilege"])) {
        UserPrivilege::SuperAdmin => new SuperAdmin($id, $name, $email, $isActive),
        UserPrivilege::Admin => new Admin($id, $name, $email, $isActive),
        UserPrivilege::Faculty => new Faculty($id, $name, $email, $isActive),
      };

      $emps[] = $employee;
    }

    return $emps;
  }

  public function count(UserSearchCriteria $criteria = new UserSearchCriteria()): int {
    $act = implode(",", array_fill(0, count($criteria->isActive),"?"));
    $priv = implode(",", array_fill(0, count($criteria->privileges), "?"));
    $query = "SELECT COUNT(*) FROM employee WHERE 
      ActiveStatus IN ($act)
      AND Privilege IN ($priv)
      AND EmpID LIKE ?
      AND EmpMail LIKE ?
      AND FName LIKE ?
      AND MName LIKE ?
      AND LName LIKE ?
      LIMIT $criteria->limit
    ";

    $stmt = $this->pdo->prepare($query);
    foreach ($criteria->isActive as $a) {$params[] = $a;}
    foreach ($criteria->privileges as $p) {$params[] = $p->value;}
    $params[] = "%$criteria->empID%";
    $params[] = "%$criteria->email%";
    $params[] = "%" . $criteria->fullname->first . "%";
    $params[] = "%" . $criteria->fullname->middle . "%";
    $params[] = "%" . $criteria->fullname->last . "%";

    $stmt->execute($params);
    $res = $stmt->fetchColumn();

    return (int)$res;
  }

  public function add(User $user): void {
    $query = "INSERT INTO employee (EmpID, EmpMail, FName, MName, LName, Privilege, ActiveStatus) VALUES (?,?,?,?,?,?,?);"; 
    
    $this->pdo->prepare($query)->execute([
      $user->empID,
      $user->email,
      $user->name->first,
      $user->name->middle,
      $user->name->last,
      $user->getPrivilege()->value,
      $user->isActive? "Active" : "Inactive",
    ]);  
  }

  public function delete(User $user): void {
    // TODO: Unassign all assets of the user
    // $assets = $this->getAssignedAssets($user);
    // foreach ($assets as $asset){
    //   $now = new DateTimeImmutable("now");
    //   $this->returnAsset($asset, $now);
    // }
    $query = "UPDATE employee SET ActiveStatus = :astat WHERE EmpID = :id;";
    $this->pdo->prepare($query)->execute([
      ":id" => $user->empID,
      ":astat" => "Inactive"
    ]);
    // $query1 = "DELETE FROM assignment WHERE assignment.EmpID = ?;";
    // $query2 = "DELETE FROM employee WHERE employee.EmpID = ?;"; 

    // $this->pdo->prepare($query1)->execute([$user->empID]);
    // $this->pdo->prepare($query2)->execute([$user->empID]);
  }
  
  public function update(User $user): void {
    $query = "UPDATE employee SET 
      EmpMail = :mail,
      FName = :fn,
      MName = :mn,
      LName = :ln,
      Privilege = :priv,
      ActiveStatus = :astat
      WHERE employee.EmpID = :id;";
          
    $this->pdo->prepare($query)->execute([
      ":id" => $user->empID,
      ":mail" => $user->email,
      ":fn" => $user->name->first,
      ":mn" => $user->name->middle,
      ":ln" => $user->name->last,
      ":priv" => $user->getPrivilege()->name,
      ":astat" => $user->isActive? "Active" : "Inactive",
    ]);      
  }
}

<?php

declare (strict_types=1);

require_once __DIR__ . '/../model/user.php';

interface UserRepoInterface {
  public function identify(int $empID): User;
  public function search(UserSearchCriteria $criteria): array;
  public function count(UserSearchCriteria $criteria): int;
  public function getContacts(int $empID): array;

  public function add(User $user) : void;
  public function updateContacts(User $user, array $contacts) : void;
  public function update(User $user) : void;
}

final class UserRepo implements UserRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}

  public function identify(int $empID): User {
    $users = $this->search(new UserSearchCriteria(empID: $empID));
    if (count($users) == 0) throw new Exception("User not found!");
    return $users[0];
  }

  public function search(UserSearchCriteria $criteria = new UserSearchCriteria()): array {
    $conds = ["1=1"]; // identity
    $params = [];

    if ($criteria->empID) {
      $conds[] = "EmpID = ?";
      $params[] = $criteria->empID;
    }

    if (!empty($criteria->isActive)) {
      $placeholders = implode(",", array_fill(0, count($criteria->isActive),"?"));
      $conds[] = "ActiveStatus IN ($placeholders)";
      foreach ($criteria->isActive as $a) $params[] = $a;
    }

    if (!empty($criteria->privileges)) {
      $placeholders = implode(",", array_fill(0, count($criteria->privileges),"?"));
      $conds[] = "Privilege IN ($placeholders)";
      foreach ($criteria->privileges as $p) $params[] = $p->value;
    }

    foreach([
      "EmpMail" => $criteria->email,
      "FName" => $criteria->fullname->first,
      "LName" => $criteria->fullname->last,
    ] as $col => $val) {
      if (empty($val)) continue;
      $conds[] = "$col LIKE ?";
      $params[] = "%$val%";
    }

    $query = "SELECT * FROM employee WHERE " . implode(" AND ", $conds) . " LIMIT $criteria->limit";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);

    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $emps = [];
    foreach ($res as $emp) {
      $emps[] = new User(
        empID: $emp["EmpID"], 
        name: new Fullname(first: $emp["FName"], last: $emp["LName"]), 
        email: $emp["EmpMail"], 
        privilege: UserPrivilege::from($emp["Privilege"]),
        isActive: $emp["ActiveStatus"] == "Active",
      );
    }

    return $emps;
  }

  public function count(UserSearchCriteria $criteria = new UserSearchCriteria()): int {
    $conds = ["1=1"];
    $params = [];

    if (!empty($criteria->isActive)) {
      $placeholders = implode(",", array_fill(0, count($criteria->isActive),"?"));
      $conds[] = "ActiveStatus IN ($placeholders)";
      foreach ($criteria->isActive as $a) $params[] = $a;
    }

    if (!empty($criteria->privileges)) {
      $placeholders = implode(",", array_fill(0, count($criteria->privileges),"?"));
      $conds[] = "Privilege IN ($placeholders)";
      foreach ($criteria->privileges as $p) $params[] = $p->value;
    }

    foreach([
      "EmpID" => $criteria->empID,
      "EmpMail" => $criteria->email,
      "FName" => $criteria->fullname->first,
      "LName" => $criteria->fullname->last,
    ] as $col => $val) {
      if (empty($val)) continue;
      $conds[] = "$col LIKE ?";
      $params[] = "%$val%";
    }

    $query = "SELECT COUNT(*) FROM employee WHERE " . implode(" AND ", $conds) . " LIMIT $criteria->limit";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);

    $res = $stmt->fetchColumn();

    return (int)$res;
  }

  public function getContacts(int $empID): array {
    $query = "SELECT ContactNum FROM empcontact WHERE EmpID = ?";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([$empID]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  public function add(User $user): void {
    $assoc = [
      "EmpMail" => $user->email,
      "FName" => $user->name->first,
      "LName" => $user->name->last,
      "Privilege" => $user->privilege->value,
      "ActiveStatus" => $user->isActive? "Active" : "Inactive",
    ];

    $query = "INSERT INTO employee (" . implode(',', array_keys($assoc)) .") VALUES (" . implode(',', array_fill(0, count($assoc), '?')) . ");"; 
    
    $this->pdo->prepare($query)->execute(array_values($assoc));        
  }

  public function updateContacts(User $user, array $contacts) : void {
    $this->pdo->beginTransaction();
    try {
      $stmt = $this->pdo->prepare("DELETE FROM empcontact WHERE EmpID = ?");
      $stmt->execute([$user->empID]);

      $placeholders = implode(',', array_fill(0, count($contacts), "(?, ?)"));
      $query = "INSERT INTO empcontact (EmpID, ContactNum) VALUES $placeholders";

      $vals = [];
      foreach ($contacts as $num) {
        $vals[] = $user->empID;
        $vals[] = $num;
      }

      $this->pdo->prepare($query)->execute($vals);

      $this->pdo->commit();
    } catch (Exception $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }
  
  public function update(User $user): void {
    $conds = [];
    $params = [];
  
    foreach([
      "EmpMail" => $user->email,
      "FName" => $user->name->first,
      "LName" => $user->name->last,
      "Privilege" => $user->privilege->name,
      "ActiveStatus" => $user->isActive? "Active" : "Inactive",
    ] as $col => $val) {
      $conds[] = "$col = ?";
      $params[] = "$val";
    }

    $query = "UPDATE employee SET " . implode(',', $conds) . " WHERE employee.EmpID = ?;";
          
    $this->pdo->prepare($query)->execute([...$params, $user->empID]);      
  }
}

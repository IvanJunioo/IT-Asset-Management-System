<?php

declare (strict_types=1);

require_once __DIR__ . '/../model/user.php';

interface UserRepoInterface {
  public function identify(string $empID): User;
  public function search(UserSearchCriteria $criteria): array;
  public function count(UserSearchCriteria $criteria): int;
  public function getContacts(string $empID): array;

  public function add(User $user) : void;
  public function updateContacts(User $user, array $contacts) : void;
  public function update(User $user) : void;
}

final class UserRepo implements UserRepoInterface {
  public function __construct(
    public readonly PDO $pdo,
  ) {}

  public function identify(string $empID): User {
    $users = $this->search(new UserSearchCriteria(empID: $empID));
    if (count($users) == 0) throw new Exception("User not found!");
    return $users[0];
  }

  public function search(UserSearchCriteria $criteria = new UserSearchCriteria()): array {
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

    $query = "SELECT * FROM employee WHERE " . implode(" AND ", $conds) . " LIMIT $criteria->limit";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);

    $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $emps = [];
    foreach ($res as $emp) {
      $id = $emp["EmpID"];
      $name = new Fullname(first: $emp["FName"], last: $emp["LName"]);
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

  public function getContacts(string $empID): array {
    $query = "SELECT ContactNum FROM empcontact WHERE EmpID = ?";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([$empID]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }

  public function add(User $user): void {
    $query = "INSERT INTO employee (EmpID, EmpMail, FName, LName, Privilege, ActiveStatus) VALUES (?,?,?,?,?,?,?);"; 
    
    $this->pdo->prepare($query)->execute([
      $user->empID,
      $user->email,
      $user->name->first,
      $user->name->last,
      $user->getPrivilege()->value,
      $user->isActive? "Active" : "Inactive",
    ]);  
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
    $query = "UPDATE employee SET 
      EmpMail = :mail,
      FName = :fn,
      LName = :ln,
      Privilege = :priv,
      ActiveStatus = :astat
      WHERE employee.EmpID = :id;
    ";
          
    $this->pdo->prepare($query)->execute([
      ":id" => $user->empID,
      ":mail" => $user->email,
      ":fn" => $user->name->first,
      ":ln" => $user->name->last,
      ":priv" => $user->getPrivilege()->name,
      ":astat" => $user->isActive? "Active" : "Inactive",
    ]);      
  }
}

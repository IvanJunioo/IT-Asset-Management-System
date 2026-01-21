<?php
declare (strict_types= 1);

enum UserPrivilege: string {
  case Faculty = "Faculty";
  case Admin = "Admin";
  case SuperAdmin = "SuperAdmin";
}

final class Fullname {
  public function __construct(
    public readonly string $first = "", 
    public readonly string $middle = "", 
    public readonly string $last = "", 
    public readonly string $suffix = ""
  ) {}
}

abstract class User implements JsonSerializable{
  public function __construct(
    public readonly string $empID, 
    public readonly Fullname $name, 
    public readonly string $email,
    public bool $isActive = True,
  ) {}

  abstract public function getPrivilege(): UserPrivilege;

  public function jsonSerialize(): mixed {
    return [
      'EmpID' => $this->empID,
      'EmpMail' => $this->email,        
      'FName' => $this->name->first,
      'MName' => $this->name->middle,
      'LName' => $this->name->last,	
      'Privilege' => $this->getPrivilege()->value,
      'ActiveStatus' => $this->isActive? "Active" : "Inactive",	
    ];
  }
}

final class SuperAdmin extends User {
  public function getPrivilege(): UserPrivilege {return UserPrivilege::SuperAdmin;}
}

final class Admin extends User {
  public function getPrivilege(): UserPrivilege {return UserPrivilege::Admin;}
}

final class Faculty extends User {
  public function getPrivilege(): UserPrivilege {return UserPrivilege::Faculty;}
}

final class UserSearchCriteria {
  public readonly Fullname $fullname;
  public readonly array $privileges;

  public function __construct(
    public readonly string $empID = "",
    ?Fullname $fullname = null,
    public readonly string $email = "",
    public readonly array $isActive = ["Active", "Inactive"],
    ?array $privileges = null,
    public readonly int $limit = 50,
  ) {
    $this->fullname = $fullname ?? new Fullname();
    $this->privileges = $privileges ?? UserPrivilege::cases();
  }
}

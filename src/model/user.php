<?php
declare (strict_types= 1);

enum UserPrivilege: string {
  case Faculty = "Faculty";
  case Staff = "Staff";
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

  public function FLast(): string {
    return strtoupper($this->first[0]) . ". " . $this->last;
  }

  public function FirstLast(): string {
    return "$this->first $this->last";
  }
}

final class User implements JsonSerializable{
  public function __construct(
    public readonly int $empID, 
    public readonly Fullname $name, 
    public readonly string $email,
    public readonly UserPrivilege $privilege,
    public bool $isActive = True,
  ) {}

  public function jsonSerialize(): mixed {
    return [
      'EmpID' => $this->empID,
      'EmpMail' => $this->email,        
      'FName' => $this->name->first,
      'LName' => $this->name->last,	
      'Privilege' => $this->privilege->value,
      'ActiveStatus' => $this->isActive? "Active" : "Inactive",	
    ];
  }
}

final class UserSearchCriteria {
  public readonly Fullname $fullname;
  public readonly array $isActive;
  public readonly array $privileges;

  public function __construct(
    public readonly ?int $empID = null,
    ?Fullname $fullname = null,
    public readonly string $email = "",
    ?array $isActive = null,
    ?array $privileges = null,
    public readonly int $limit = 50,
  ) {
    $this->fullname = $fullname ?? new Fullname();
    $this->isActive = $isActive ?? ["Active", "Inactive"];
    $this->privileges = $privileges ?? UserPrivilege::cases();
  }
}

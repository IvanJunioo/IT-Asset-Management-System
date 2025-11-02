<?php
declare (strict_types= 1);

include_once 'asset.php';

enum UserPrivilege {
  case faculty;
  case admin;
  case superAdmin;
}

class Fullname {
  public string $first;
  public string $middle;
  public string $last;
  public string $suffix;

  public function __construct(
    string $first = "", 
    string $middle = "", 
    string $last = "", 
    string $suffix = ""
  ) {
    $this->first = $first;
    $this->middle = $middle;
    $this->last = $last;
    $this->suffix = $suffix;
  }
}

abstract class User implements JsonSerializable{
  protected string $_empID;
  protected Fullname $_name;
  protected string $_email;
  protected bool $_isActive = True;
  protected string $_actlog = ""; 

  public function __construct(string $empID, Fullname $name, string $email) {
    $this -> _empID = $empID;
    $this -> _name = $name;
    $this -> _email = $email;
  }

  // getters
  public function getEmpID(): string {return $this -> _empID;}
  public function getName(): Fullname {return $this -> _name;}
  public function getActlog(): string {return $this -> _actlog;}
  public function getEmail(): string {return $this -> _email;}
  public function isActive(): bool {return $this -> _isActive;}
  abstract public function getPrivilege(): UserPrivilege;

  // setters
  public function setName(Fullname $name) {$this -> _name = $name;}
  public function setEmail(string $email) {$this -> _email = $email;}
  public function setActlog(string $actlog) {$this -> _actlog = $actlog;}
  public function setActiveStatus(bool $status) {$this -> _isActive = $status;}

  public function jsonSerialize(): mixed {
    return [
      'EmpID' => $this->getEmpID(),
      'EmpMail' => $this->getEmail(),        
      'FName' => $this->getName()->first,
      'MName' => $this->getName()->middle,
      'LName' => $this->getName()->last,	
      'Privilege' => $this->getPrivilege()->name,
      'ActiveStatus' => $this->isActive()? "Active" : "Inactive",	
      'ActLog' => $this->getActlog(),
    ];
  }
  
  // public function exportAsset() {return;}
}

class SuperAdmin extends User {
  public function __construct(string $empID, Fullname $name, string $email) {
    parent::__construct($empID, $name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::superAdmin;}
}

class Admin extends User {
  public function __construct(string $empID, Fullname $name, string $email) {
    parent::__construct($empID, $name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::admin;}
}

class Faculty extends User {
  public function __construct(string $empID, Fullname $name, string $email) {
    parent::__construct($empID, $name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::faculty; }
}


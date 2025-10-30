<?php
declare (strict_types= 1);

include_once '../src/model/asset.php';

enum UserPrivilege {
  case faculty;
  case admin;
  case superAdmin;
}

class Username {
  private string $first;
  private string $middle;
  private string $last;
  private string $suffix;

  public function __construct(string $first, string $middle, string $last, string $suffix = "") {
    $this->first = $first;
    $this->middle = $middle;
    $this->last = $last;
    $this->suffix = $suffix;
  }
  public function first(): string {return $this->first;}
  public function middle(): string {return $this->middle;}
  public function last(): string {return $this->last;}
  public function suffix(): string {return $this->suffix;}
}

abstract class User {
  protected string $_empID;
  protected Username $_name;
  protected string $_email;
  protected bool $_isActive = True;
  protected string $_actlog = ""; 
  protected array $_assigned;

  public function __construct(string $empID, Username $name, string $email) {
    $this -> _empID = $empID;
    $this -> _name = $name;
    $this -> _email = $email;
  }

  // getters
  public function getEmpID(): string {return $this -> _empID;}
  public function getName(): Username {return $this -> _name;}
  public function getActlog(): string {return $this -> _actlog;}
  public function getEmail(): string {return $this -> _email;}
  public function isActive(): bool {return $this -> _isActive;}
  abstract public function getPrivilege(): UserPrivilege;

  // setters
  public function setName(Username $name) {$this -> _name = $name;}
  public function setEmail(string $email) {$this -> _email = $email;}
  public function setActlog(string $actlog) {$this -> _actlog = $actlog;}
  public function setActiveStatus(bool $status) {$this -> _isActive = $status;}

  public function exportAsset() {return;}
}

class SuperAdmin extends User {
  public function __construct(string $empID, Username $name, string $email) {
    parent::__construct($empID, $name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::superAdmin;}
}

class Admin extends User {
  public function __construct(string $empID, Username $name, string $email) {
    parent::__construct($empID, $name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::admin;}
}

class Faculty extends User {
  public function __construct(string $empID, Username $name, string $email) {
    parent::__construct($empID, $name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::faculty; }
}


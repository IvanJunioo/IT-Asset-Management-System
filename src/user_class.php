<?php
declare (strict_types= 1);

enum UserPrivilege {
  case faculty;
  case admin;
  case superAdmin;
}

abstract class User {
  protected string $_name;
  protected string $_email;
  protected bool $_isActive = False;
  protected string $_actlog = ""; 

  public function __construct(string $name, string $email) {
    $this -> _name = $name;
    $this -> _email = $email;
  }

  // getters
  public function getName(): string {return $this -> _name;}
  public function getActlog(): string {return $this -> _actlog;}
  public function getEmail(): string {return $this -> _email;}
  public function isActive(): bool {return $this -> _isActive;}
  abstract public function getPrivilege(): UserPrivilege;

  // setters
  public function setName(string $name) {$this -> _name = $name;}
  public function setEmail(string $email) {$this -> _email = $email;}
  public function setActlog(string $actlog) {$this -> _actlog = $actlog;}
  public function setActiveStatus(bool $status) {$this -> _isActive = $status;}
}

interface AdminInterface {
  public function modifyAsset(Asset $asset);
}

class SuperAdmin extends User {
  public function __construct(string $name, string $email) {
    parent::__construct($name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::superAdmin;}

  
}

class Admin extends User {
  public function __construct(string $name, string $email) {
    parent::__construct($name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::admin;}
}

class Faculty extends User {
  public function __construct(string $name, string $email) {
    parent::__construct($name, $email);
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::faculty; }
}
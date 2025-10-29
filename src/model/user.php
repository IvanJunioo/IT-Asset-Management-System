<?php
declare (strict_types= 1);

include '../src/model/asset.php';
include '../src/model/database.php';

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
  protected array $_assigned;
  protected Database $_db;

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

  public function exportAsset() {return;}
}

class SuperAdmin extends User {
  public function __construct(string $name, string $email, Database $db) {
    parent::__construct($name, $email);
    $this -> _db = $db;
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::superAdmin;}
}

class Admin extends User {
  public function __construct(string $name, string $email, AdminDatabaseInterface $db) {
    parent::__construct($name, $email);
    $this -> _db = $db;
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::admin;}
}

class Faculty extends User {
  public function __construct(string $name, string $email, FacultyDatabaseInterface $db) {
    parent::__construct($name, $email);
    $this -> _db = $db;
  }

  public function getPrivilege(): UserPrivilege {return UserPrivilege::faculty; }
}


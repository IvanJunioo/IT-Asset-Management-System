<?php
declare (strict_types= 1);

include '../src/model/user.php';

enum AssetStatus {
  case Unused;
  case Used;
  case Broken;
  case InRepair;
}

interface AssetUserInterface {
  public function getProcNum(): string;
  public function getSerialNum(): string;
  public function getPurchaseDate(): string;
  public function getSpecs(): string;
  public function getRemarks(): string;
  public function getDescription(): string;
  public function getStatus(): AssetStatus;
  public function getPrice(): float;
  public function getUrl(): string;
  public function getActlog(): string;
  public function getUser(): User;
}

interface AssetAdminInterface extends AssetUserInterface {
  public function setPrice(float $price);
  public function setRemarks(string $remarks);
  public function setDescription(string $description);
  public function setStatus(AssetStatus $status);
  public function setUrl(string $url);
  public function assignTo(User $user);
}

class Asset implements AssetAdminInterface {
  private string $_procNum;
  private string $_serialNum;
  private string $_purchaseDate;
  private string $_specs;
  private string $_remarks;
  private string $_description;
  private AssetStatus $_status = AssetStatus::Unused;
  private float $_price;
  private string $_url;
  private string $_actlog = "";
  private User $_assigned_To;

  public function __construct(
    string $procNum, 
    string $serialNum,
    string $date,
    string $specs,
    string $desc,
    string $url,
    string $remarks,
    float $price) {
        $this -> _procNum = $procNum;
        $this -> _serialNum = $serialNum;
        $this -> _purchaseDate = $date;
        $this -> _specs = $specs;
        $this -> _description = $desc;
        $this -> _remarks = $remarks;
        $this -> _url = $url;
        $this -> _price = $price;
  }

  public function getProcNum(): string { return $this -> _procNum; }
  public function getSerialNum(): string { return $this -> _serialNum; }
  public function getPurchaseDate(): string { return $this -> _purchaseDate; }
  public function getSpecs(): string { return $this -> _specs; }
  public function getRemarks(): string { return $this -> _remarks; }
  public function getDescription(): string { return $this -> _description; }
  public function getStatus(): AssetStatus { return $this -> _status; }
  public function getPrice(): float { return $this -> _price; }
  public function getUrl(): string { return $this -> _url; }
  public function getActlog(): string { return $this -> _actlog; } 
  public function getUser(): User { return $this -> _assigned_To; }
  
  public function setPrice(float $price) { $this -> _price = $price; }
  public function setRemarks(string $remarks) { $this -> _remarks = $remarks; }
  public function setDescription(string $description){ $this -> _description = $description; }
  public function setStatus(AssetStatus $status) { $this -> _status = $status; }
  public function setUrl(string $url) { $this -> _url = $url; }
  public function assignTo(User $user) { $this -> _assigned_To = $user; }
}

// $user1 = new SuperAdmin("h", "@d");
// $asset1 = new Asset("1","1","1","1","1","1","1",1);
// $asset1->assignTo($user1);
// echo $asset1->getUser()->getName();
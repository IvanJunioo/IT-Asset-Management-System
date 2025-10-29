<?php
declare (strict_types= 1);

enum AssetStatus {
  case Unused;
  case Used;
  case Broken;
  case InRepair;
}

interface AssetUserInterface {
  public string $procNum { get; }
  public string $serialNum { get; }
  // public string $purchaseDate {get;}
  // public string $specs {get;}
  // public string $remarks {get;}
  // public string $description {get;}
  // public AssetStatus $status {get;}
  // public float $price {get;}
  // public string $url {get;}
  // public string $actLog {get;}
}

interface AssetAdminInterface {
  // public float $price {set;}
  // public string $remarks {set;}
  // public string $description {set;}
  // public AssetStatus $status {set;}
  // public string $url {set;}
  // public function assignTo(User $user);
}

class Asset implements AssetUserInterface, AssetAdminInterface {
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

  private User $assigned_to;

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

  public string $procNum {
    get => $this->_procNum;
  }

  public string $serialNum {
    get => $this->_serialNum;
  }
  // public string $purchaseDate {get;}
  // public string $specs {get;}
  // public string $remarks {get;}
  // public string $description {get;}
  // public AssetStatus $status {get;}
  // public float $price {get;}
  // public string $url {get;}
  // public string $actLog {get;}
}

$asset = new Asset("1","2","3","4","5","6","7",12.0);
echo $asset.$procNum;

<?php
declare (strict_types= 1);

include_once 'user.php';

enum AssetStatus {
  case Unused;
  case Used;
  case Broken;
  case InRepair;

  public static function fromStr(string $status): AssetStatus {
    foreach (self::cases() as $case) {
      if ($case->name === $status) return $case;
    }
    throw new Exception('String not a valid AssetStatus');
  }
}

interface AssetUserInterface {
  public function getPropNum(): string;
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
  public function getUser(): string;
}

interface AssetAdminInterface extends AssetUserInterface {
  // Setters return object set
  public function setPrice(float $price): AssetAdminInterface;
  public function setRemarks(string $remarks): AssetAdminInterface;
  public function setDescription(string $description): AssetAdminInterface;
  public function setStatus(AssetStatus $status): AssetAdminInterface;
  public function setUrl(string $url): AssetAdminInterface;
  public function assignTo(User $user): AssetAdminInterface;
}

class Asset implements AssetAdminInterface, JsonSerializable{
  private string $_propNum;
  private string $_procNum;
  private string $_serialNum;
  private string $_purchaseDate;
  private string $_specs;
  private string $_remarks;
  private string $_description;
  private AssetStatus $_status;
  private float $_price;
  private string $_url;
  private string $_actlog = "";
  private ?string $_assigned_To = '-';

  public function __construct(
    string $propNum,
    string $procNum, 
    string $serialNum,
    string $date,
    string $specs,
    string $desc,
    string $url,
    string $remarks,
    float $price,
		// string $actlog
		) {
      $this -> _propNum = $propNum;
      $this -> _procNum = $procNum;
      $this -> _serialNum = $serialNum;
      $this -> _purchaseDate = $date;
      $this -> _specs = $specs;
      $this -> _description = $desc;
      $this -> _remarks = $remarks;
      $this -> _url = $url;
      $this -> _price = $price;
      $this -> _status = AssetStatus::Unused;
			// $this -> _actlog = $actlog;
  }
  public function getPropNum(): string { return $this -> _propNum; }
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
  public function getUser(): string { return $this -> _assigned_To; }
  
  public function setPrice(float $price): Asset { 
    $this -> _price = $price; 
    return $this;
  }
  public function setRemarks(string $remarks): Asset { 
    $this -> _remarks = $remarks; 
    return $this;
  }
  public function setDescription(string $description): Asset { 
    $this -> _description = $description; 
    return $this;
  }
  public function setStatus(AssetStatus $status): Asset { 
    $this -> _status = $status; 
    return $this;
  }
  public function setUrl(string $url): Asset { 
    $this -> _url = $url; 
    return $this;
  }
  public function assignTo(?User $user): Asset { 
    $this -> _assigned_To = $user->getEmpID(); 
    return $this;
  }

  public function jsonSerialize(): mixed {
    return [
      'PropNum' => $this->getPropNum(),
      'ProcNum' => $this->getProcNum(),
      'SerialNum' => $this->getSerialNum(),
      'PurchaseDate' => $this->getPurchaseDate(),
      'Specs' => $this->getSpecs(),
      'Remarks' => $this->getRemarks(),
      'ShortDesc' => $this->getDescription(),
      'Status' => $this->getStatus()->name,
      'Price' => (string) $this->getPrice(),
      'Url' => $this->getUrl(),
      'ActLog' => $this->getActlog(),
			'AssignedTo' => $this->getUser(),
    ];
  }

}

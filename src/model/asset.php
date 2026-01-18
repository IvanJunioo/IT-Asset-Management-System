<?php
declare (strict_types= 1);

include_once 'user.php';

enum AssetStatus: string {
  case Available = "Available";
  case Assigned = "Assigned";
  case Condemned = "Condemned";
  case InRepair = "InRepair";
}

final class Asset implements JsonSerializable{
  public function __construct(
    public readonly string $propNum,
    public readonly string $procNum,
    public readonly string $serialNum,
    public readonly string $purchaseDate,
    public readonly string $specs,
    public readonly string $remarks,
    public readonly string $description,
    public readonly float $price,
    public readonly string $url,
    public AssetStatus $status,
    protected ?string $assigned_To = '-'
  ) {}

  public function assignTo(?User $user): void {
    $this->assigned_To = $user?->empID;
  }

  public function jsonSerialize(): mixed {
    return [
      'PropNum' => $this->propNum,
      'ProcNum' => $this->procNum,
      'SerialNum' => $this->serialNum,
      'PurchaseDate' => $this->purchaseDate,
      'Specs' => $this->specs,
      'Remarks' => $this->remarks,
      'ShortDesc' => $this->description,
      'Status' => $this->status->value,
      'Price' => (string) $this->price,
      'Url' => $this->url,
			'AssignedTo' => $this->assigned_To,
    ];
  }
}

<?php
declare (strict_types= 1);

include_once 'user.php';

enum AssetStatus: string {
  case Unassigned = "Unassigned";
  case Assigned = "Assigned";
  case Condemned = "Condemned";
  case ToCondemn = "ToCondemn";
}

final class Asset implements JsonSerializable {
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

final class AssetSearchCriteria {
  public function __construct(
    public readonly float $price_min = 0,
    public readonly float $price_max = 10**12 - 0.01,
    public readonly DateTimeImmutable $base_date = new DateTimeImmutable("0001-01-01"),
    public readonly DateTimeImmutable $end_date = new DateTimeImmutable("9999-12-31"),
    public readonly array $status = AssetStatus::cases(),
    public readonly string $propNum = "",
    public readonly string $procNum = "",
    public readonly string $serialNum = "",
    public readonly string $specs = "",
    public readonly string $description = "",
    public readonly string $remarks = "",
    public readonly int $limit = 50,
  ) {}
}

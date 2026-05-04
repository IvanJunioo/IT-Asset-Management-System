<?php
declare (strict_types= 1);

final class Assignment implements JsonSerializable {
  public function __construct(
    public readonly int $assignID = 0,  # auto-incremental
    public readonly string $propNum,
    public readonly DateTimeImmutable $assignDate,
    public readonly int $assignerID,
    public readonly int $assigneeID,
    public readonly ?DateTimeImmutable $returnDate,
    public readonly string $remarks,
  ) {}

  public function jsonSerialize(): mixed {
    return [
      'AssignID' => $this->assignID,
      'PropNum' => $this->propNum,        
      'AssignDate' => $this->assignDate,
      'AssignerID' => $this->assignerID,	
      'AssigneeID' => $this->assigneeID,
      'ReturnDate' => $this->returnDate,	
      'Remarks' => $this->remarks,	
    ];
  }
}

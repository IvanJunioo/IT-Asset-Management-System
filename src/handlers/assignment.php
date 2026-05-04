<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../manager/assign.php';

final class AssignmentHandler {
  public function __construct(
    private readonly AssignmentManagerInterface $manager,
    private readonly LogHandler $logHand,
  ) {}

  public function getAssignments(string $assigneeID): array {
    return $this->manager->getAssignments((int)$assigneeID);
  }

  public function assign(
    array $propNums,
    DateTimeImmutable $date,
    string $assigneeID,
    string $remarks,
  ): void {
    foreach ($propNums as $propNum) {
      $this->manager->assignAsset(
        propNum: $propNum,
        assignerID: $_SESSION["user_id"],
        assigneeID: (int)$assigneeID,
        assDate: $date,
        remarks: $remarks,
      );

      $this->logHand->systemLog(
        "assigned asset $propNum to user $assigneeID",
        [
          "action" => "assign",
          "object" => "asset",
          "propNum" => $propNum,
          "assigneeID" => $assigneeID,
        ]
      );
    }
  }

  public function reassign(
    array $propNums,
    DateTimeImmutable $date,
    string $assigneeID,
    string $remarks,
  ): void {
    foreach ($propNums as $propNum) {
      $this->manager->returnAsset($propNum,$date,$remarks);

      $this->manager->assignAsset(
        propNum: $propNum,
        assignerID: $_SESSION["user_id"],
        assigneeID: (int)$assigneeID,
        assDate: $date,
        remarks: $remarks,
      );

      $this->logHand->systemLog(
        "reassigned asset $propNum to user $assigneeID",
        [
          "action" => "reassign",
          "object" => "asset",
          "propNum" => $propNum,
          "assigneeID" => $assigneeID,
        ]
      );
    }
  }

  public function return(
    array $propNums,
    DateTimeImmutable $date,
    string $remarks,
  ): void {
    foreach ($propNums as $propNum) {
      $this->manager->returnAsset($propNum,$date,$remarks);

      $this->logHand->systemLog(
        "returned asset $propNum",
        [
          "action" => "return",
          "object" => "asset",
          "propNum" => $propNum,
        ]
      );
    }
  }
}

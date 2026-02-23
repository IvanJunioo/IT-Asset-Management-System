<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../manager/assign.php';
require_once __DIR__ . '/../manager/logger.php';

final class AssignmentHandler {
  public function __construct(
    private readonly AssignmentManagerInterface $manager,
  ) {}

  public function assign(
    array $propNums,
    DateTimeImmutable $date,
    string $assigneeID,
    string $remarks,
  ): void {
    $empID = $_SESSION["user_id"];

    foreach ($propNums as $propNum) {
      $this->manager->assignAsset(
        propNum: $propNum,
        assignerID: $empID,
        assigneeID: $assigneeID,
        assDate: $date,
        remarks: $remarks,
      );

      systemLog(
        "assigned asset $propNum to user $empID",
        [
          "action" => "assign",
          "object" => "asset",
          "propNum" => $propNum,
          "assigneeID" => $empID,
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

      systemLog(
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

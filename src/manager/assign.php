<?php

declare (strict_types=1);

include_once '../repos/asset.php';
include_once '../repos/assignment.php';
include_once '../repos/user.php';

interface AssignmentManagerInterface {
  public function assignAsset(
    string $propNum, 
    string $assignerID,
    string $assigneeID,
    DateTimeImmutable $assDate, 
    string $remarks,
  ): void;
  public function returnAsset(
    string $propNum, 
    DateTimeImmutable $retDate,
    string $remarks, 
  ): void;  
}

final class AssignmentManager implements AssignmentManagerInterface {
  public function __construct(
    private readonly AssetRepoInterface $assetRepo,
    private readonly AssignmentRepoInterface $assignRepo,
    private readonly UserRepoInterface $userRepo,
  ) {}

  public function assignAsset(
    string $propNum, 
    string $assignerID,
    string $assigneeID,
    DateTimeImmutable $assDate, 
    string $remarks = "",
  ): void {
    $asset = $this->assetRepo->identify($propNum);
    if ($asset->status !== AssetStatus::Unassigned) return;
    $asset->status = AssetStatus::Assigned;

    $assigner = $this->userRepo->identify($assignerID);
    $assignee = $this->userRepo->identify($assigneeID);

    $asset->assignTo($assignee);

    $this->assignRepo->assign($asset, $assigner, $assignee, $assDate, $remarks);
    $this->assetRepo->update($asset);
  }
  
  public function returnAsset(
    string $propNum, 
    DateTimeImmutable $retDate,
    string $remarks = "",  
  ): void {
    $asset = $this->assetRepo->identify($propNum);
    if ($asset->status !== AssetStatus::Assigned) return;
    $asset->status = AssetStatus::Unassigned;
    
    $asset->assignTo(null);

    $this->assignRepo->return($asset, $retDate, $remarks);    
    $this->assetRepo->update($asset);
  }
}

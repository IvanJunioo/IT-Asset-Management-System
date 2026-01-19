<?php

declare (strict_types=1);

include_once '../repos/asset.php';
include_once '../repos/assignment.php';

interface AssignmentManagerInterface {
  public function assignAsset(
    Asset $asset, 
    User $assigner,
    User $assignee,
    DateTimeImmutable $assDate, 
    string $remarks
  ): void;
  public function returnAsset(
    Asset $asset, 
    DateTimeImmutable $retDate,
    string $remarks, 
  ): void;  
}

final class AssignmentManager implements AssignmentManagerInterface {
  public function __construct(
    private readonly AssetRepoInterface $assetRepo,
    private readonly AssignmentRepoInterface $assignRepo,
  ) {}

  public function assignAsset(
      Asset $asset, 
      User $assigner,
      User $assignee,
      DateTimeImmutable $assDate,
      string $remarks,
    ): void {

    if ($asset->status !== AssetStatus::Unassigned){
      return;
    }

    $asset->status = AssetStatus::Assigned;
    $asset->assignTo($assignee);

    $this->assignRepo->assign($asset, $assigner, $assignee, $assDate, $remarks);
    $this->assetRepo->update($asset);
  }
  
  public function returnAsset(
    Asset $asset, 
    DateTimeImmutable $retDate,
    string $remarks = "",  
  ): void {
    if ($asset->status !== AssetStatus::Assigned){
      return;
    }
    $asset->status = AssetStatus::Unassigned;
    $asset->assignTo(null);

    $this->assignRepo->return($asset, $retDate, $remarks);    
    $this->assetRepo->update($asset);
  }
}

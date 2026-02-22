<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/actlog.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/assignment.php';

final class AssetHandler {
  public function __construct(
    private readonly ActLogRepoInterface $logRepo,
    private readonly AssetRepoInterface $assetRepo,
    private readonly AssignmentRepo $assignRepo,
  ) {}

  public function searchAssets(
    string $search,
    string $status,
  ): array {
    $status = $status !== ""? array_map("AssetStatus::from", explode(',', $status)) : null;

    $assets = [];
    foreach (array_values(array_map("unserialize", array_unique(array_map("serialize", [
      ...$this->assetRepo->search(new AssetSearchCriteria(propNum: $search, status: $status)),
      ...$this->assetRepo->search(new AssetSearchCriteria(procNum: $search, status: $status)),
      ...$this->assetRepo->search(new AssetSearchCriteria(serialNum: $search, status: $status)),
      ...$this->assetRepo->search(new AssetSearchCriteria(specs: $search, status: $status)),
    ])))) as $asset) {
      $user = $this->assignRepo->getCurrAssignedUser($asset);
      $asset->assignTo($user);
      $assets[] = [
        ...$asset->jsonSerialize(),
        "Assignee" => $user? $user->name->FirstLast() : "",
      ];
    }
    return $assets;
  }
}

<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../repos/asset.php';
require_once __DIR__ . '/../repos/assignment.php';
require_once __DIR__ . '/../manager/logger.php';

function array_any(array $array, callable $callback): bool {
  foreach ($array as $x) {
    if ($callback($x)) return true;
  }
  return false;
}

final class AssetHandler {
  public function __construct(
    private readonly AssetRepoInterface $assetRepo,
    private readonly AssignmentRepo $assignRepo,
  ) {}

  public function getAsset(string $propNum): Asset {
    return $this->assetRepo->identify($propNum);
  }

  public function getRepoStats(): array {
    return [
      "assetsTotal" => $this->assetRepo->count(new AssetSearchCriteria()),
      "assetsAvail" => $this->assetRepo->count(new AssetSearchCriteria(status: [AssetStatus::Unassigned])),
    ];
  }

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

  public function addAsset(array $assets): void {
    $today = date("Y-m-d");

    if (array_any($assets, fn($asset) => $today < $asset->purchaseDate)) {
      header('Location: ../../public/views/add-asset-form.php');
      exit;
    }

    foreach ($assets as $asset) {
      $this->assetRepo->add($asset);
      
      systemLog(
        "added new asset $asset->propNum",
        [
          "action" => "add",
          "object" => "asset",
          "propNum" => $asset->propNum,
        ]
      );
    }
  }

  public function editAsset(Asset $asset): void {
    $old = $this->assetRepo->identify($asset->propNum);

    $diff = array_diff_assoc($asset->jsonSerialize(), $old->jsonSerialize());
    if (empty($diff)) return;

    $this->assetRepo->update($asset);
  
    systemLog(
      "modified asset $asset->propNum",
      [ 
        "action" => "modify",
        "object" => "asset",
        "propNum" => $asset->propNum,
        "diff" => $diff,
      ],
    );
  }

  public function changeStatus(string $propNum, AssetStatus $status): void {
    $asset = $this->assetRepo->identify($propNum);
    $asset->status = $status;
    $this->assetRepo->update($asset);

    $action = match ($status) {
      AssetStatus::Assigned => "assign",
      AssetStatus::Condemned => "condemn",
      AssetStatus::Unassigned => "return",
      AssetStatus::ToCondemn => "report",
    };

    systemLog(
      "{$action}ed asset $propNum",
      [
        "action" => $action,
        "object" => "asset",
        "propNum" => $propNum,
      ]
    );
  }
}

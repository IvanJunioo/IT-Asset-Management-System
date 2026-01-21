<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/asset.php';

$search = $_POST['search'] ?? "";

$repo = new AssetRepo($pdo);

$assets = $repo->search(new AssetSearchCriteria(propNum: $search));
if (!empty($assets)){
  $asset = $assets[0];
  $asset->status = AssetStatus::Condemned;
	$repo->update($asset);
}

exit;

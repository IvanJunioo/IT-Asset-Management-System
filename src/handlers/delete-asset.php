<?php

require_once '../utilities/request-guard.php';
require_once '../../config/config.php';
require_once '../repos/asset.php';

$search = $_POST['search'] ?? "";

$repo = new AssetRepo($pdo);

$assets = $repo->search(propNum: $search);
if (!empty($assets)){
	$repo->delete($assets[0]);
}

exit;
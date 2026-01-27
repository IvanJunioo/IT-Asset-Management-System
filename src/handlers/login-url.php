<?php
require_once '../utilities/request-guard.php';
require_once '../../config/config.php';

header('Content-Type: application/json');

echo json_encode($url);

exit;
<?php
require_once __DIR__ . '/../utilities/request-guard.php';
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json');

echo json_encode($url);

exit;
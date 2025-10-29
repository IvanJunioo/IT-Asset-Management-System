<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
             || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

$host = $_SERVER['HTTP_HOST'];

$scriptDir = dirname($_SERVER['SCRIPT_NAME']);
$projectRoot = preg_replace('#/(src|public).*#', '/', $scriptDir);

define('BASE_URL', $protocol . $host . $projectRoot);

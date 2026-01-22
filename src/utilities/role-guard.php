<?php
function requireRole(array $allowedRoles): void
{
    if (!isset($_SESSION['privilege'])) {
        http_response_code(403);
        exit('Access denied');
    }

    if (!(count($allowedRoles) == 0) && !in_array($_SESSION['privilege'], $allowedRoles, true)) {
        http_response_code(403);
        exit('Access denied');
    }
}
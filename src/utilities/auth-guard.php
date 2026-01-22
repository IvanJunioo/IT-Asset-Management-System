<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ' . BASE_URL . 'public/views/login.php');
    exit;
}

$userFName = $_SESSION['user_fname'] ?? '';
$userMName = $_SESSION['user_mname'] ?? '';
$userLName = $_SESSION['user_lname'] ?? '';
$privilege = $_SESSION['privilege'] ?? '';
$privilege = $privilege == "SuperAdmin" ? "Super Admin" : $privilege;
$navItems = [
    'Dashboard' => [
        'url' => BASE_URL . 'public/views/dashboard.php',
        'roles' => ['Super Admin', 'Admin', 'Faculty'],
    ],
    'Assets' => [
        'url' => BASE_URL . 'public/views/assets.php',
        'roles' => ['Super Admin', 'Admin', 'Faculty']
    ],
    'Manage Assets' => [
        'url' => BASE_URL . 'public/views/asset-manager.php',
        'roles' => ['Super Admin', 'Admin']
    ],
    'Users' => [
        'url' => BASE_URL . 'public/views/users.php',
        'roles' => ['Super Admin', 'Admin']
    ],
    'Manage Users' => [
        'url' => BASE_URL . 'public/views/user-manager.php',
        'roles' => ['Super Admin']
    ],
    'System Activities' => [
        'url' => BASE_URL . 'public/views/activity-log.php',
        'roles' => ['Super Admin', 'Admin', 'Faculty']
    ]
];
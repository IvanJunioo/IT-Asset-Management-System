<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ' . BASE_URL . 'public/views/login.php');
    exit;
}

$privilege = $_SESSION['privilege'] ?? '';
$privilege = $privilege == "SuperAdmin" ? "Super Admin" : $privilege;
$name = $_SESSION['user_fname'];
if (str_contains($name, " ")) {
    $fsName = explode(" ", $name);
    $name = $fsName[0];
}
$navItems = [
    'Dashboard' => [
        'url' => BASE_URL . 'public/views/dashboard.php',
        'roles' => ['Super Admin', 'Admin', 'Faculty', 'Staff'],
    ],
    'Assets' => [
        'url' => BASE_URL . 'public/views/assets.php',
        'roles' => ['Faculty', 'Staff']
    ],
    'Manage Assets' => [
        'url' => BASE_URL . 'public/views/asset-manager.php',
        'roles' => ['Super Admin', 'Admin']
    ],
    'Users' => [
        'url' => BASE_URL . 'public/views/users.php',
        'roles' => ['Admin']
    ],
    'Manage Users' => [
        'url' => BASE_URL . 'public/views/user-manager.php',
        'roles' => ['Super Admin']
    ],
    'System Activities' => [
        'url' => BASE_URL . 'public/views/activity-log.php',
        'roles' => ['Super Admin', 'Admin', 'Faculty', 'Staff']
    ]
];

$dashboardIslands = [
    'View Assets' => [
        'url' => BASE_URL . 'public/views/assets.php',
        'roles' => ['Faculty', 'Staff'],
        'body' => "Preview all the system assets."
    ],
    'Manage Assets' => [
        'url' => BASE_URL . 'public/views/asset-manager.php',
        'roles' => ['Super Admin', 'Admin'],
        'body' => "Add, edit, assign, or condemn assets in the department's inventory."
    ],
    'View Users' => [
        'url' => BASE_URL . 'public/views/users.php',
        'roles' => ['Admin'],
        'body' => "Preview all the system users."
    ],
    'Manage Users' => [
        'url' => BASE_URL . 'public/views/user-manager.php',
        'roles' => ['Super Admin'],
        'body' => "Add users or update their roles, permissions, other details."
    ],
    'System Activities' => [
        'url' => BASE_URL . 'public/views/activity-log.php',
        'roles' => ['Super Admin', 'Admin', 'Faculty', 'Staff'],
        'body' => "Track all system actions and events."
    ]
];
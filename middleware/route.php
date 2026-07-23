<?php

$currentPage = str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']);
$rootPath    = str_replace('\\', '/', ROOT_PATH);

$currentPage = str_replace($rootPath, '', $currentPage);

/* Skip API */
if (str_starts_with($currentPage, '/api/')) {
    return;
}

/* Guest Pages */
$guestPages = [
    '/pages/auth/login.php',
    '/pages/auth/register.php',
    '/pages/auth/forgot-password.php',
];

/* Public Pages */
$publicPages = [
    '/pages/students/index.php',
    '/pages/students/create.php',
    '/pages/students/edit.php',
];

/* Skip Public Pages */
if (in_array($currentPage, $publicPages, true)) {
    return;
}

/* Handle Guest Pages */
if (in_array($currentPage, $guestPages, true)) {
    require_once MIDDLEWARE_PATH . '/guest.php';
    return;
}

/* Protected Pages */
require_once MIDDLEWARE_PATH . '/auth.php';
<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$uuid = trim($_POST['uuid'] ?? '');
$phone = trim($_POST['phone'] ?? '');

$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if ($uuid == '') {
    errorResponse('Invalid user.');
}

if ($phone == '') {
    errorResponse('Phone is required.');
}

if ($password == '') {
    errorResponse('Password is required.');
}

if (strlen($password) < 8) {
    errorResponse('Password must be at least 8 characters.');
}

if ($password !== $confirm) {
    errorResponse('Passwords do not match.');
}

$stmt = $pdo->prepare("
    UPDATE users
    SET
        phone = ?,
        password = ?
    WHERE uuid = ?
");

$stmt->execute([
    $phone,
    hashPassword($password),
    $uuid
]);

successResponse(
    [],
    'Password updated successfully.'
);
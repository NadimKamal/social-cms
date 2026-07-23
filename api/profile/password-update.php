<?php

require_once __DIR__ . '/../../bootstrap/app.php';

auth();

if (!is_post()) {
    errorResponse('Invalid request method.');
}

/* Current User */
$currentUser = user();

$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$currentUser['id']]);

$dbUser = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dbUser) {
    errorResponse('User not found.');
}

/* Input */
$currentPassword = $_POST['current_password'] ?? '';
$newPassword     = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

/* Validation */
if ($currentPassword === '') {
    errorResponse('Current password is required.');
}

if ($newPassword === '') {
    errorResponse('New password is required.');
}

if (strlen($newPassword) < 6) {
    errorResponse('New password must be at least 6 characters.');
}

if ($confirmPassword === '') {
    errorResponse('Please confirm your new password.');
}

if ($newPassword !== $confirmPassword) {
    errorResponse('Password confirmation does not match.');
}

if (!verifyPassword($currentPassword, $dbUser['password'])) {
    errorResponse('Current password is incorrect.');
}

if (verifyPassword($newPassword, $dbUser['password'])) {
    errorResponse('New password must be different from the current password.');
}

/* Update Password */
$stmt = $pdo->prepare("
    UPDATE users
    SET
        password = ?,
        updated_at = ?
    WHERE id = ?
");

$stmt->execute([
    hashPassword($newPassword),
    now(),
    $currentUser['id']
]);

/* Refresh Session */
$dbUser['password'] = hashPassword($newPassword);
login($currentUser);

/* Response */
successResponse([], 'Password changed successfully.');
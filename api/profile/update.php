<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

if (!isLoggedIn()) {
    errorResponse('Please login first.');
}

$currentUser = user();

$name  = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');

if ($name === '') {
    errorResponse('Name is required.');
}

if ($email === '') {
    errorResponse('Email is required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    errorResponse('Invalid email address.');
}

/* Check if email already used by another user */
$stmt = $pdo->prepare("
    SELECT id
    FROM users
    WHERE email = ?
    AND id != ?
    LIMIT 1
");

$stmt->execute([$email, $currentUser['id']]);

if ($stmt->fetch()) {
    errorResponse('Email already exists.');
}

/* Check if phone already used by another user */
if ($phone !== '') {
    $stmt = $pdo->prepare("
        SELECT id
        FROM users
        WHERE phone = ?
        AND id != ?
        LIMIT 1
    ");

    $stmt->execute([$phone, $currentUser['id']]);

    if ($stmt->fetch()) {
        errorResponse('Phone number already exists.');
    }
}

/* Update Profile */
$stmt = $pdo->prepare("
    UPDATE users
    SET
        name = ?,
        email = ?,
        phone = ?,
        updated_at = ?
    WHERE id = ?
");

$stmt->execute([
    $name,
    $email,
    $phone,
    now(),
    $currentUser['id']
]);

/* Refresh Session */
$stmt = $pdo->prepare("
    SELECT *
    FROM users
    WHERE id=?
    LIMIT 1
");

$stmt->execute([$currentUser['id']]);

login($stmt->fetch(PDO::FETCH_ASSOC));

successResponse([], 'Profile updated successfully.');
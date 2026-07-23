<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

/* Collect Input */
$userType = trim($_POST['user_type'] ?? '');
$name = trim($_POST['name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$passwordConfirmation = $_POST['password_confirmation'] ?? '';

/* Validation */
if (!in_array($userType, ['Person', 'Company'])) {
    errorResponse('Invalid user type.');
}

if ($name === '') {
    errorResponse('Name is required.');
}

if ($email === '') {
    errorResponse('Email is required.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    errorResponse('Invalid email address.');
}

if ($password === '') {
    errorResponse('Password is required.');
}

if (strlen($password) < 6) {
    errorResponse('Password must be at least 6 characters.');
}

if ($password !== $passwordConfirmation) {
    errorResponse('Password confirmation does not match.');
}

/* Check if email already exists */
if (exists($pdo, 'users', ['email' => $email])) {
    errorResponse('Email already exists.');
}

/* Check if phone already exists */
if ($phone !== '') {
    if (exists($pdo, 'users', ['phone' => $phone])) {
        errorResponse('Phone number already exists.');
    }
}

/* Generate Username */
$username = generateUsername($pdo, $userType);

/* Register User */
$result = register($pdo, [
    'uuid'       => generate_uuid(),
    'sys_id'     => generate_uuid(),
    'user_type'  => $userType,
    'name'       => $name,
    'username'   => $username,
    'email'      => $email,
    'phone'      => $phone,
    'password'   => hashPassword($password),
    'picture'    => null,
    'status'     => 'Active',
    'created_at' => now()
]);

if (!$result['success']) {
    errorResponse($result['message']);
}

/* Auto Login */
$user = find($pdo, 'users', ['email' => $email]);

if (!$user) {
    errorResponse('Unable to create account.');
}

login($user);

/* Success */
successResponse([
    'username' => $username,
    'user' => [
        'uuid'      => $user['uuid'],
        'sys_id'    => $user['sys_id'],
        'name'      => $user['name'],
        'username'  => $user['username'],
        'email'     => $user['email'],
        'phone'     => $user['phone'],
        'user_type' => $user['user_type'],
        'picture'   => $user['picture'],
        'status'    => $user['status']
    ]
], 'Registration completed successfully.');
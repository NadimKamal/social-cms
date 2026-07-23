<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request method.');
}

// Input
$login = trim($_POST['login'] ?? '');
$password = $_POST['password'] ?? '';

if ($login === '' || $password === '') {
    errorResponse('Username / Email and password are required.');
}

// Attempt Login
$result = attemptLogin($pdo, $login, $password);

if (!$result['success']) {
    errorResponse($result['message']);
}

// Remember Me (Future)
if (!empty($_POST['remember'])) {
    // TODO: Implement remember me functionality
    // Create remember_token, store in database, set secure cookie
}

// Success Response
apiSuccess([
    'message' => 'Login successful.',
    'user'    => [
        'uuid'      => $result['user']['uuid'],
        'sys_id'    => $result['user']['sys_id'],
        'name'      => $result['user']['name'],
        'username'  => $result['user']['username'],
        'email'     => $result['user']['email'],
        'user_type' => $result['user']['user_type'],
        'picture'   => $result['user']['picture'],
        'status'    => $result['user']['status'],
    ]
]);
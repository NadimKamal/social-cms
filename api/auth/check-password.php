<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$password = $_POST['password'] ?? '';

$response = [
    'success' => false,
    'message' => '',
    'score'   => 0
];

if ($password === '') {
    $response['message'] = 'Password is required.';
    jsonResponse($response);
}

if (strlen($password) < 8) {
    $response['message'] = 'Password must be at least 8 characters.';
    jsonResponse($response);
}

$score = 0;
if (preg_match('/[a-z]/', $password)) $score++;
if (preg_match('/[A-Z]/', $password)) $score++;
if (preg_match('/[0-9]/', $password)) $score++;
if (preg_match('/[\W_]/', $password)) $score++;

$response['score'] = $score;

switch ($score) {
    case 1:
        $response['message'] = 'Weak password.';
        break;
    case 2:
        $response['message'] = 'Fair password.';
        break;
    case 3:
        $response['message'] = 'Good password.';
        break;
    case 4:
        $response['success'] = true;
        $response['message'] = 'Strong password.';
        break;
}

jsonResponse($response);
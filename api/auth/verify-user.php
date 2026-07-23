<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$email = strtolower(trim($_POST['email'] ?? ''));
$phone = trim($_POST['phone'] ?? '');

if ($email == '') {
    errorResponse('Email is required.');
}

$sql = "
    SELECT
        id,
        uuid,
        name,
        email,
        phone
    FROM users
    WHERE email = ?
";

$params = [$email];

if ($phone !== '') {

    $sql .= " AND phone = ?";
    $params[] = $phone;

}

$sql .= " LIMIT 1";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    errorResponse('No account found.');
}

successResponse(
    [
        'uuid'  => $user['uuid'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'phone' => $user['phone']
    ],
    'User verified successfully.'
);
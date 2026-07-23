<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$currentUser = user();

if (!$currentUser) {
    errorResponse('Unauthorized.');
}

/* Delete Existing Picture */
if (!empty($currentUser['picture'])) {
    deleteImage($currentUser['picture']);
}

/* Update Database */
$stmt = $pdo->prepare("
    UPDATE users
    SET picture = NULL
    WHERE id = ?
");

$stmt->execute([$currentUser['id']]);

/* Update Session */
$_SESSION['user']['picture'] = null;

/* Response */
successResponse([
    'picture' => asset('assets/images/default/dp.png')
], 'Profile picture deleted successfully.');
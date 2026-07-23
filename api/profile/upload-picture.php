<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

if (!isLoggedIn()) {
    errorResponse('Please login first.');
}

$currentUser = user();

if (empty($_FILES['picture']['name'])) {
    errorResponse('Please select an image.');
}

/* Upload New Image */
$newPicture = uploadImage($_FILES['picture'], 'users');

if (!$newPicture) {
    errorResponse('Unable to upload image.');
}

/* Delete Old Image */
if (!empty($currentUser['picture'])) {
    deleteImage($currentUser['picture']);
}

/* Update Database */
$stmt = $pdo->prepare("
    UPDATE users
    SET
        picture = ?,
        updated_at = ?
    WHERE id = ?
");

$stmt->execute([
    $newPicture,
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

successResponse(
    ['picture' => asset($newPicture)],
    'Profile picture updated successfully.'
);
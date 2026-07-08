<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    redirect('pages/students/index.php');
}

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$oldPicture = $_POST['old_picture'] ?? '';

if ($id <= 0) {
    die('Invalid Student ID.');
}

if ($title == '') {
    die('Student title is required.');
}

// Get Existing Student

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die('Student not found.');
}

// Upload New Image (If Selected)

$picture = $oldPicture;

if (
    isset($_FILES['picture']) &&
    $_FILES['picture']['error'] === UPLOAD_ERR_OK
) {
    deleteImage($oldPicture);

    $picture = uploadImage($_FILES['picture'], 'students');
}

$stmt = $pdo->prepare("
    UPDATE students
    SET
        title = ?,
        picture = ?,
        updated_at = ?
    WHERE id = ?
");

$stmt->execute([
    $title,
    $picture,
    now(),
    $id
]);

redirect('pages/students/index.php');
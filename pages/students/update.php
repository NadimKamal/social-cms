<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    redirect('pages/students/index.php');
}

$id = (int) ($_POST['id'] ?? 0);
$title = trim($_POST['title'] ?? '');
$oldPicture = $_POST['old_picture'] ?? '';

if ($id <= 0) {
    toast('error', 'Invalid Student ID.');
    redirect('pages/students/edit.php');
}

if ($title == '') {
    toast('warning', 'Student title is required.');
    redirect('pages/students/edit.php');
}

// Get Existing Student
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    toast('error', 'Student not found.');
    redirect('pages/students/index.php');
}

// Upload New Image (If Selected)
$picture = $oldPicture;

if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
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

toast('success', 'Student updated successfully.');

redirect('pages/students/index.php');
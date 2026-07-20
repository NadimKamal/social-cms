<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    redirect('pages/students/index.php');
}

$title = trim($_POST['title'] ?? '');

if ($title == '') {
    toast('warning', 'Title is required.');
    redirect('pages/students/create.php');
}

$picture = uploadImage($_FILES['picture'], 'students');

$sql = "INSERT INTO students (title, picture, created_at) VALUES (?, ?, ?)";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    $title,
    $picture,
    now()
]);

toast('success', 'Student created successfully.');

redirect('pages/students/index.php');
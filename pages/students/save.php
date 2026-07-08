<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    redirect('pages/students/index.php');
}

$title = trim($_POST['title'] ?? '');

if ($title == '') {

    die('Title is required.');

}

$picture = uploadImage($_FILES['picture'], 'students');

$sql = "INSERT INTO students
(
    title,
    picture,
    created_at
)
VALUES
(?,?,?)";

$stmt = $pdo->prepare($sql);

$stmt->execute([

    $title,

    $picture,

    now()

]);

redirect('pages/students/index.php');
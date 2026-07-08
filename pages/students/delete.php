<?php

require_once __DIR__ . '/../../bootstrap/app.php';

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect('pages/students/index.php');
}

// Find Student

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    redirect('pages/students/index.php');
}

// Delete Image

deleteImage($student['picture']);

// Delete Record

$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([$id]);

redirect('pages/students/index.php');
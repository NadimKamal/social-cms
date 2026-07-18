<?php

require_once __DIR__ . '/../../bootstrap/app.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    setFlash('error', 'Invalid category.');
    redirect('pages/content-categories/index.php');
}

try {
    
    // Check Category Exists
    $stmt = $pdo->prepare("
        SELECT id
        FROM content_categories
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        throw new Exception('Category not found.');
    }

    // Prevent Delete if Used
    $stmt = $pdo->prepare("
        SELECT COUNT(*)
        FROM contents
        WHERE content_category_id = ?
    ");

    $stmt->execute([$id]);

    $totalContents = $stmt->fetchColumn();

    if ($totalContents > 0) {
        throw new Exception(
            'This category is already used in contents and cannot be deleted.'
        );
    }

    // Delete
    $stmt = $pdo->prepare("
        DELETE FROM content_categories
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    setFlash('success', 'Category deleted successfully.');

} catch (Exception $e) {
    setFlash('error', $e->getMessage());
}

redirect('pages/content-categories/index.php');
<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {

    redirect('pages/content-categories/index.php');

}

try {

    $id = intval($_POST['id'] ?? 0);

    $title = sanitize($_POST['title'] ?? '');

    $description = sanitize($_POST['description'] ?? '');

    $color = sanitize($_POST['color'] ?? '#2563eb');

    $icon = sanitize($_POST['icon'] ?? '');

    $isActive = intval($_POST['is_active'] ?? 1);

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($id <= 0) {

        throw new Exception('Invalid category.');

    }

    if (empty($title)) {

        throw new Exception('Category title is required.');

    }

    if (strlen($title) > 100) {

        throw new Exception('Category title cannot exceed 100 characters.');

    }

    /*
    |--------------------------------------------------------------------------
    | Check Category Exists
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Duplicate Title Check
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM content_categories
        WHERE LOWER(title)=LOWER(?)
        AND id<>?
        LIMIT 1
    ");

    $stmt->execute([

        $title,

        $id

    ]);

    if ($stmt->fetch()) {

        throw new Exception('Category title already exists.');

    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        UPDATE content_categories
        SET
            title=?,
            description=?,
            color=?,
            icon=?,
            is_active=?,
            updated_at=?
        WHERE id=?
    ");

    $stmt->execute([

        $title,

        $description,

        $color,

        $icon,

        $isActive,

        now(),

        $id

    ]);

    clearOld();

    setFlash(

        'success',

        'Content category updated successfully.'

    );

    redirect('pages/content-categories/index.php');

} catch (Exception $e) {

    withInput($_POST);

    setFlash(

        'error',

        $e->getMessage()

    );

    redirect('pages/content-categories/edit.php?id=' . ($_POST['id'] ?? 0));

}
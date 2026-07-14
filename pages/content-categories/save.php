<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {

    redirect('pages/content-categories/index.php');

}

try {

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

    if (empty($title)) {

        throw new Exception('Category title is required.');

    }

    if (strlen($title) > 100) {

        throw new Exception('Category title cannot exceed 100 characters.');

    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate Check
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        SELECT id
        FROM content_categories
        WHERE LOWER(title)=LOWER(?)
        LIMIT 1
    ");

    $stmt->execute([$title]);

    if ($stmt->fetch()) {

        throw new Exception('Category title already exists.');

    }

    /*
    |--------------------------------------------------------------------------
    | Save
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO content_categories
        (
            uuid,
            title,
            description,
            color,
            icon,
            is_active,
            created_at,
            updated_at
        )
        VALUES
        (
            ?,?,?,?,?,?,?,?
        )
    ");

    $stmt->execute([

        generate_uuid(),

        $title,

        $description,

        $color,

        $icon,

        $isActive,

        now(),

        now()

    ]);

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    clearOld();

    setFlash(

        'success',

        'Content category created successfully.'

    );

    redirect('pages/content-categories/index.php');

} catch (Exception $e) {

    withInput($_POST);

    setFlash(

        'error',

        $e->getMessage()

    );

    redirect('pages/content-categories/create.php');

}
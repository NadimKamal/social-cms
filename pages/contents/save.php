<?php

require_once '../../bootstrap/app.php';

if (!is_post()) {

    redirect('pages/contents/index.php');

}

try {

    /*
    |--------------------------------------------------------------------------
    | Inputs
    |--------------------------------------------------------------------------
    */

    $contentCategoryId = (int) ($_POST['content_category_id'] ?? 0);
    $title = sanitize($_POST['title'] ?? '');
    $originalText = trim($_POST['original_text'] ?? '');
    $aiSummary = trim($_POST['ai_summary'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($contentCategoryId <= 0) {
        throw new Exception('Please select a category.');
    }

    if ($title == '') {
        throw new Exception('Title is required.');
    }

    if (strlen($title) > 255) {
        throw new Exception('Title cannot exceed 255 characters.');
    }

    if ($originalText == '') {
        throw new Exception('Original text is required.');
    }

    if ($aiSummary == '') {
        throw new Exception('Please generate the AI summary before saving.');
    }

    /*
    |--------------------------------------------------------------------------
    | Category Exists
    |--------------------------------------------------------------------------
    */

    if (!exists($pdo, 'content_categories', [
        'id' => $contentCategoryId,
        'is_active' => 1
    ])) {

        throw new Exception('Invalid content category.');

    }

    /*
    |--------------------------------------------------------------------------
    | Upload Image
    |--------------------------------------------------------------------------
    */

    $imagePath = null;

    if (

        isset($_FILES['image']) &&
        $_FILES['image']['error'] === UPLOAD_ERR_OK

    ) {

        $imagePath = uploadImage(
            $_FILES['image'],
            'contents/images'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Insert
    |--------------------------------------------------------------------------
    */

    insert($pdo, 'contents', [

        'uuid' => generate_uuid(),

        'content_category_id' => $contentCategoryId,

        'title' => $title,

        'original_text' => $originalText,

        'image_path' => $imagePath,

        'ai_summary' => $aiSummary,

        'uploaded_by' => null,

        'status' => 'Completed',

        'created_at' => now(),

        'updated_at' => now()

    ]);

    setFlash(

        'success',

        'Content created successfully.'

    );

    redirect(

        'pages/contents/index.php'

    );

} catch (Exception $e) {

    withInput();

    setFlash(

        'error',

        $e->getMessage()

    );

    redirect(

        'pages/contents/create.php'

    );

}
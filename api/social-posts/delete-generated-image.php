<?php

require_once '../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$imagePath = trim($_POST['image_path'] ?? '');

if ($imagePath == '') {
    apiSuccess();
}

// Security

if (!str_starts_with($imagePath, 'uploads/social_posts/images/')) {
    errorResponse('Invalid image path.');
}

deleteImage($imagePath);

apiSuccess();
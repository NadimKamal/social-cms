<?php

require_once '../../bootstrap/app.php';

if (!is_post()) {

    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Invalid request.'
    ];

    redirect('pages/social-posts/index.php');
}

$uuid = trim($_POST['uuid'] ?? '');

$post = findByUuidOrFail(
    $pdo,
    'social_posts',
    $uuid
);

/*
|--------------------------------------------------------------------------
| Image Upload
|--------------------------------------------------------------------------
*/

$imagePath = $post['image_path'];

if (
    isset($_FILES['image']) &&
    $_FILES['image']['error'] === UPLOAD_ERR_OK
) {

    // Delete old image
    if (!empty($post['image_path'])) {
        deleteImage($post['image_path']);
    }

    // Upload new image
    $imagePath = uploadImage(
        $_FILES['image'],
        'social_posts/images'
    );
}

/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

update(
    $pdo,
    'social_posts',
    [
        'caption'    => trim($_POST['caption']),
        'hashtags'   => trim($_POST['hashtags']),
        'keywords'   => trim($_POST['keywords']),
        'status'     => trim($_POST['status']),
        'image_path' => $imagePath,
        'updated_at' => now(),
    ],
    [
        'uuid' => $uuid
    ]
);

/*
|--------------------------------------------------------------------------
| Flash Message
|--------------------------------------------------------------------------
*/

$_SESSION['flash'] = [

    'type'    => 'success',

    'message' => 'Social post updated successfully.'

];

/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

redirect('pages/social-posts/index.php');
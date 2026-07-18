<?php

require_once '../../bootstrap/app.php';

$uuid = $_GET['uuid'] ?? '';

$post = findByUuidOrFail(
    $pdo,
    'social_posts',
    $uuid
);

/*
|--------------------------------------------------------------------------
| Delete Generated Image
|--------------------------------------------------------------------------
*/

if (!empty($post['image_path'])) {

    deleteImage($post['image_path']);

}

/*
|--------------------------------------------------------------------------
| Delete Social Post
|--------------------------------------------------------------------------
*/

delete(
    $pdo,
    'social_posts',
    [
        'uuid' => $uuid
    ]
);

$_SESSION['flash'] = [

    'type' => 'success',

    'message' => 'Social post deleted successfully.'

];

redirect('pages/social-posts/index.php');
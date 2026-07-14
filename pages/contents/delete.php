<?php

require_once '../../bootstrap/app.php';

$uuid = $_GET['uuid'] ?? '';

$content = findByUuidOrFail(
    $pdo,
    'contents',
    $uuid
);

if (!empty($content['image_path'])) {

    deleteImage($content['image_path']);

}

delete(
    $pdo,
    'contents',
    [
        'uuid' => $uuid
    ]
);

$_SESSION['flash'] = [

    'type' => 'success',

    'message' => 'Content deleted successfully.'

];

redirect('pages/contents/index.php');
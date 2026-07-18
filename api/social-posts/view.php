<?php

require_once '../../bootstrap/app.php';

if (!is_get()) {
    errorResponse('Invalid request.');
}

$uuid = trim($_GET['uuid'] ?? '');

if ($uuid == '') {
    errorResponse('Post UUID is required.');
}

$stmt = $pdo->prepare("
SELECT *
FROM social_posts
WHERE uuid = ?
LIMIT 1
");

$stmt->execute([$uuid]);

$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    errorResponse('Social post not found.');
}

/*
|--------------------------------------------------------------------------
| Convert comma separated values into array
|--------------------------------------------------------------------------
*/

$post['hashtags'] = array_values(array_filter(array_map(
    'trim',
    explode(',', $post['hashtags'] ?? '')
)));

$post['keywords'] = array_values(array_filter(array_map(
    'trim',
    explode(',', $post['keywords'] ?? '')
)));

apiSuccess($post);
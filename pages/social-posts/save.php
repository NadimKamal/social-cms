<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {
    errorResponse('Invalid request.');
}

$uuids      = $_POST['uuids'] ?? [];
$caption    = trim($_POST['caption'] ?? '');
$imagePath  = trim($_POST['image_path'] ?? '');
$hashtags   = trim($_POST['hashtags'] ?? '');
$keywords   = trim($_POST['keywords'] ?? '');
$status     = trim($_POST['status'] ?? 'Draft');

if (!is_array($uuids) || empty($uuids)) {
    errorResponse('Please select at least one content.');
}

if ($caption == '') {
    errorResponse('Caption is required.');
}

try {

    $pdo->beginTransaction();

    /*
    |--------------------------------------------------------------------------
    | Get Selected Content IDs
    |--------------------------------------------------------------------------
    */

    $placeholders = implode(',', array_fill(0, count($uuids), '?'));

    $stmt = $pdo->prepare("
        SELECT
            id
        FROM contents
        WHERE uuid IN ($placeholders)
    ");

    $stmt->execute($uuids);

    $contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$contents) {
        throw new Exception('Selected contents not found.');
    }

    /*
    |--------------------------------------------------------------------------
    | Insert Social Post
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO social_posts
        (
            uuid,
            caption,
            image_path,
            video_path,
            hashtags,
            keywords,
            status
        )
        VALUES
        (
            UUID(),
            ?,
            ?,
            NULL,
            ?,
            ?,
            ?
        )
    ");

    $stmt->execute([
        $caption,
        $imagePath,
        $hashtags,
        $keywords,
        $status
    ]);

    $socialPostId = $pdo->lastInsertId();

    /*
    |--------------------------------------------------------------------------
    | Link Selected Contents
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("
        INSERT INTO social_post_contents
        (
            social_post_id,
            content_id
        )
        VALUES
        (
            ?,
            ?
        )
    ");

    foreach ($contents as $content) {

        $stmt->execute([
            $socialPostId,
            $content['id']
        ]);

    }

    $pdo->commit();

    apiSuccess([
        'message' => 'Social post saved successfully.'
    ]);

}
catch (Throwable $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    errorResponse($e->getMessage());

}
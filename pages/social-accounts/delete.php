<?php

require_once __DIR__ . '/../../bootstrap/app.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {

    setFlash(
        'error',
        'Invalid social account.'
    );

    redirect('pages/social-accounts/index.php');

}

try {

    /*
    |--------------------------------------------------------------------------
    | Check Record Exists
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("

        SELECT id

        FROM social_accounts

        WHERE id = ?

        LIMIT 1

    ");

    $stmt->execute([$id]);

    if (!$stmt->fetch()) {

        throw new Exception('Social account not found.');

    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("

        DELETE FROM social_accounts

        WHERE id = ?

        LIMIT 1

    ");

    $stmt->execute([$id]);

    clearFlash();

    setFlash(

        'success',

        'Social account deleted successfully.'

    );

} catch (Exception $e) {

    clearFlash();

    setFlash(

        'error',

        $e->getMessage()

    );

}

redirect('pages/social-accounts/index.php');
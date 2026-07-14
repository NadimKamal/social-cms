<?php

require_once __DIR__ . '/../../bootstrap/app.php';

if (!is_post()) {

    redirect('pages/social-accounts/index.php');

}

try {

    /*
    |--------------------------------------------------------------------------
    | Get Data
    |--------------------------------------------------------------------------
    */

    $id = intval($_POST['id'] ?? 0);

    $platform        = sanitize($_POST['platform'] ?? '');
    $accountName     = sanitize($_POST['account_name'] ?? '');
    $accountUsername = sanitize($_POST['account_username'] ?? '');
    $accountEmail    = sanitize($_POST['account_email'] ?? '');
    $accountUrl      = sanitize($_POST['account_url'] ?? '');
    $pageName        = sanitize($_POST['page_name'] ?? '');
    $pageUrl         = sanitize($_POST['page_url'] ?? '');
    $accessToken     = trim($_POST['access_token'] ?? '');
    $refreshToken    = trim($_POST['refresh_token'] ?? '');
    $isActive        = intval($_POST['is_active'] ?? 1);

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    if ($id <= 0) {

        throw new Exception('Invalid social account.');

    }

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

    if ($platform == '') {

        throw new Exception('Platform is required.');

    }

    $allowedPlatforms = [
        'Facebook',
        'Instagram',
        'X',
        'LinkedIn',
        'YouTube',
        'Threads'
    ];

    if (!in_array($platform, $allowedPlatforms)) {

        throw new Exception('Invalid platform selected.');

    }

    if ($accountName == '') {

        throw new Exception('Account name is required.');

    }

    if (strlen($accountName) > 150) {

        throw new Exception('Account name cannot exceed 150 characters.');

    }

    if (!empty($accountEmail) && !filter_var($accountEmail, FILTER_VALIDATE_EMAIL)) {

        throw new Exception('Invalid email address.');

    }

    if (!empty($accountUrl) && !filter_var($accountUrl, FILTER_VALIDATE_URL)) {

        throw new Exception('Invalid account URL.');

    }

    if (!empty($pageUrl) && !filter_var($pageUrl, FILTER_VALIDATE_URL)) {

        throw new Exception('Invalid page URL.');

    }

    /*
    |--------------------------------------------------------------------------
    | Duplicate Check
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("

        SELECT id

        FROM social_accounts

        WHERE platform = ?

        AND account_name = ?

        AND id <> ?

        LIMIT 1

    ");

    $stmt->execute([

        $platform,

        $accountName,

        $id

    ]);

    if ($stmt->fetch()) {

        throw new Exception('Another social account with the same platform and account name already exists.');

    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->prepare("

        UPDATE social_accounts

        SET

            platform = ?,

            account_name = ?,

            account_username = ?,

            account_email = ?,

            account_url = ?,

            page_name = ?,

            page_url = ?,

            access_token = ?,

            refresh_token = ?,

            is_active = ?,

            updated_at = ?

        WHERE id = ?

    ");

    $stmt->execute([

        $platform,

        $accountName,

        $accountUsername,

        $accountEmail,

        $accountUrl,

        $pageName,

        $pageUrl,

        $accessToken,

        $refreshToken,

        $isActive,

        now(),

        $id

    ]);

    clearFlash();

    setFlash(

        'success',

        'Social account updated successfully.'

    );

    redirect('pages/social-accounts/index.php');

} catch (Exception $e) {

    withInput($_POST);

    clearFlash();

    setFlash(

        'error',

        $e->getMessage()

    );

    redirect('pages/social-accounts/edit.php?id=' . ($_POST['id'] ?? 0));

}
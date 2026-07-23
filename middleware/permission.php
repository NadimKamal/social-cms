<?php

require_once __DIR__ . '/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Permission Middleware
|--------------------------------------------------------------------------
|
| Example:
|
| require_once '../../middleware/permission.php';
| permission('manage-users');
|
*/

if (!function_exists('permission')) {

    function permission(string $permission): void
    {
        auth();

        /*
        |--------------------------------------------------------------------------
        | Future Permission Logic
        |--------------------------------------------------------------------------
        |
        | Example:
        |
        | if (!hasPermission($permission)) {
        |
        |     toast(
        |         'error',
        |         'You do not have permission to access this page.'
        |     );
        |
        |     redirect('pages/dashboard/index.php');
        | }
        |
        */

        return;
    }

}
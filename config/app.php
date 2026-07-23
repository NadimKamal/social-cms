<?php

define('ROOT_PATH', dirname(__DIR__));

$envFile = ROOT_PATH . '/.env';

$GLOBALS['env'] = file_exists($envFile)
    ? parse_ini_file($envFile)
    : [];

define('HELPER_PATH', ROOT_PATH . '/helpers');

require_once HELPER_PATH . '/helper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Dhaka');

define('APP_URL', rtrim(env('APP_URL'), '/') . '/');

define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDE_PATH', ROOT_PATH . '/includes');
define('SERVICE_PATH', ROOT_PATH . '/services');
define('MIDDLEWARE_PATH', ROOT_PATH . '/middleware');
define('ASSET_PATH', ROOT_PATH . '/assets');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('API_PATH', ROOT_PATH . '/api');
define('PAGES_PATH', ROOT_PATH . '/pages');
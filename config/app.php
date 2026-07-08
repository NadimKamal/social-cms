<?php

$env = parse_ini_file(__DIR__ . '/../.env');

define('ROOT_PATH', dirname(__DIR__));

define('APP_URL', rtrim($env['APP_URL'], '/') . '/');

define('CONFIG_PATH', ROOT_PATH . '/config');
define('HELPER_PATH', ROOT_PATH . '/helpers');
define('INCLUDE_PATH', ROOT_PATH . '/includes');
define('ASSET_PATH', ROOT_PATH . '/assets');
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('API_PATH', ROOT_PATH . '/api');
define('PAGES_PATH', ROOT_PATH . '/pages');

date_default_timezone_set('Asia/Dhaka');

require_once HELPER_PATH . '/helper.php';
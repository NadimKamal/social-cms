<?php

require_once '../../config/app.php';
require_once '../../config/database.php';

echo json_encode(getData($pdo, [

    'table' => 'social_accounts',

    'primaryKey' => 'id',

    'defaultOrderBy' => 'id',

    'defaultOrder' => 'DESC'

]));
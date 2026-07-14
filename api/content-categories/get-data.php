<?php

require_once '../../config/app.php';
require_once '../../config/database.php';

echo json_encode(getData($pdo, [

    'table' => 'content_categories',

    'primaryKey' => 'id',

    'defaultOrderBy' => 'id',

    'defaultOrder' => 'DESC'

]));
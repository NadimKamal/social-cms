<?php

require_once '../../config/app.php';
require_once '../../config/database.php';

echo json_encode(getData($pdo, [

    'table' => 'students',

    'primaryKey' => 'id',

    'defaultOrderBy' => 'id',

    'defaultOrder' => 'DESC'

]));
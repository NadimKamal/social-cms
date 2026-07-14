<?php

require_once '../../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Inputs
|--------------------------------------------------------------------------
*/
$page = max(1, intval($_GET['page'] ?? 1));

$rowsPerPage = max(1, intval($_GET['rows_per_page'] ?? 9));

$offset = ($page - 1) * $rowsPerPage;

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

$search = '';
$category = '';
$status = '';
$from = '';
$to = '';

if (!empty($_GET['filterable_columns'])) {

    $filters = explode('|', $_GET['filterable_columns']);

    foreach ($filters as $filter) {

        if (empty($filter)) {

            continue;

        }

        $parts = explode('=>', $filter);

        $key = $parts[0] ?? '';

        $value = trim($parts[1] ?? '');

        switch ($key) {

            case 'search_like':

                $search = $value;

                break;

            case 'content_category_id':

                $category = $value;

                break;

            case 'status':

                $status = $value;

                break;

            case 'created_at_from':

                $from = $value;

                break;

            case 'created_at_to':

                $to = $value;

                break;

        }

    }

}

/*
|--------------------------------------------------------------------------
| Where
|--------------------------------------------------------------------------
*/

$where = [];

$bindings = [];

if ($search != '') {

    $where[] = "(
        c.title LIKE ?
        OR c.original_text LIKE ?
        OR c.ai_summary LIKE ?
    )";

    $bindings[] = "%{$search}%";
    $bindings[] = "%{$search}%";
    $bindings[] = "%{$search}%";

}

if ($category != '') {

    $where[] = "c.content_category_id=?";

    $bindings[] = $category;

}

if ($status != '') {

    $where[] = "c.status=?";

    $bindings[] = $status;

}

if ($from != '') {

    $where[] = "DATE(c.created_at)>=?";

    $bindings[] = $from;

}

if ($to != '') {

    $where[] = "DATE(c.created_at)<=?";

    $bindings[] = $to;

}

$whereSql = '';

if (count($where)) {

    $whereSql = ' WHERE ' . implode(' AND ', $where);

}

/*
|--------------------------------------------------------------------------
| Total
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("

SELECT COUNT(*)

FROM contents c

{$whereSql}

");

$stmt->execute($bindings);

$total = $stmt->fetchColumn();

/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    c.*,

    cc.title AS category_name,

    cc.color AS category_color

FROM contents c

LEFT JOIN content_categories cc

ON cc.id=c.content_category_id

{$whereSql}

ORDER BY c.id DESC

LIMIT {$offset},{$rowsPerPage}

";

$stmt = $pdo->prepare($sql);

$stmt->execute($bindings);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

$statistics = [

    'completed' => 0,

    'pending' => 0,

    'processing' => 0,

    'failed' => 0

];

$stmt = $pdo->query("

SELECT

status,

COUNT(*) total

FROM contents

GROUP BY status

");

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

    $statistics[strtolower($row['status'])] = (int)$row['total'];

}

/*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
*/

$lastPage = ceil($total / $rowsPerPage);

$pages = [];

$start = ($page < 5) ? 1 : $page - 4;

$end = min($lastPage, $start + 8);

if ($start > 1) {

    $pages[] = '...';

}

for ($i = $start; $i <= $end; $i++) {

    $pages[] = $i;

}

if ($end < $lastPage) {

    $pages[] = '...';

}

/*
|--------------------------------------------------------------------------
| Response
|--------------------------------------------------------------------------
*/

echo json_encode([

    'records' => [

        'data' => $data,

        'current_page' => $page,

        'last_page' => $lastPage,

        'total' => $total

    ],

    'statistics' => [

        'total' => $total,

        'completed' => $statistics['completed'],

        'pending' => $statistics['pending'],

        'processing' => $statistics['processing'],

        'failed' => $statistics['failed']

    ],

    'pages' => $pages,

    'sl' => $offset

]);
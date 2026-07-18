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
| WHERE
|--------------------------------------------------------------------------
*/

$where = [];

$bindings = [];

if ($search != '') {

    $where[] = "(
        caption LIKE ?
        OR hashtags LIKE ?
        OR keywords LIKE ?
    )";

    $bindings[] = "%{$search}%";
    $bindings[] = "%{$search}%";
    $bindings[] = "%{$search}%";
}

if ($status != '') {

    $where[] = "status = ?";

    $bindings[] = $status;
}

if ($from != '') {

    $where[] = "DATE(created_at) >= ?";

    $bindings[] = $from;
}

if ($to != '') {

    $where[] = "DATE(created_at) <= ?";

    $bindings[] = $to;
}

$whereSql = '';

if (!empty($where)) {

    $whereSql = ' WHERE ' . implode(' AND ', $where);
}

/*
|--------------------------------------------------------------------------
| Total
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM social_posts
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

SELECT *

FROM social_posts

{$whereSql}

ORDER BY id DESC

LIMIT {$offset}, {$rowsPerPage}

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

    'draft' => 0,
    'ready' => 0,
    'published' => 0

];

$stmt = $pdo->query("

SELECT

    status,

    COUNT(*) total

FROM social_posts

GROUP BY status

");

foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

    $statistics[strtolower($row['status'])] = (int) $row['total'];
}

/*
|--------------------------------------------------------------------------
| Pages
|--------------------------------------------------------------------------
*/

$lastPage = max(1, ceil($total / $rowsPerPage));

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

        'draft' => $statistics['draft'],

        'ready' => $statistics['ready'],

        'published' => $statistics['published']

    ],

    'pages' => $pages,

    'sl' => $offset

]);
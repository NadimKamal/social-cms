<?php

if (!function_exists('now')) {

    function now()
    {
        return date('Y-m-d H:i:s');
    }

}

if (!function_exists('dd')) {

    function dd($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
        die();
    }

}

if (!function_exists('dump')) {

    function dump($data)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";
    }

}

if (!function_exists('e')) {

    function e($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

}

if (!function_exists('redirect')) {

    function redirect($url)
    {
        header("Location: " . APP_URL . ltrim($url, '/'));
        exit;
    }

}

if (!function_exists('asset')) {

    function asset($path)
    {
        return APP_URL . ltrim($path, '/');
    }

}

if (!function_exists('url')) {

    function url($path = '')
    {
        return APP_URL . ltrim($path, '/');
    }

}

if (!function_exists('old')) {

    function old($key, $default = '')
    {
        return $_POST[$key] ?? $default;
    }

}

if (!function_exists('is_post')) {

    function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

}

if (!function_exists('is_get')) {

    function is_get()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

}

if (!function_exists('uploadImage')) {

    function uploadImage(array $file, string $folder = 'general')
    {
        if (
            !isset($file['error']) ||
            $file['error'] !== UPLOAD_ERR_OK
        ) {
            return null;
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed)) {
            throw new Exception('Invalid image type.');
        }

        $uploadDir = ROOT_PATH . "/uploads/{$folder}/";

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid($folder . '_') . '.' . $extension;

        move_uploaded_file(
            $file['tmp_name'],
            $uploadDir . $fileName
        );

        return "uploads/{$folder}/{$fileName}";
    }

}

if (!function_exists('deleteImage')) {

    function deleteImage(?string $path): bool
    {
        if (empty($path)) {
            return false;
        }

        $fullPath = ROOT_PATH . '/' . $path;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

}

if (!function_exists('CUSTOM_DATE')) {

    function CUSTOM_DATE($date)
    {
        if (empty($date)) {
            return '';
        }

        return date('d F, Y', strtotime($date));
    }

}

if (!function_exists('CUSTOM_DATE_TIME')) {

    function CUSTOM_DATE_TIME($date)
    {
        if (empty($date)) {
            return '';
        }

        return date('d F Y, h:i A', strtotime($date));
    }

}

function getData(PDO $pdo, array $config)
{

    $table = $config['table'];

    $primaryKey = $config['primaryKey'] ?? 'id';

    $orderBy = $_GET['order_by'] ?? ($config['defaultOrderBy'] ?? $primaryKey);

    $orderType = $_GET['order_type'] ?? ($config['defaultOrder'] ?? 'DESC');

    $rowsPerPage = intval($_GET['rows_per_page'] ?? 10);

    $page = max(1, intval($_GET['page'] ?? 1));

    $offset = ($page - 1) * $rowsPerPage;

    $where = [];

    $bindings = [];

    if (!empty($_GET['filterable_columns'])) {

        $columns = explode('|', $_GET['filterable_columns']);

        foreach ($columns as $column) {

            if (empty($column)) {

                continue;

            }

            $column = explode('=>', $column);

            $name = $column[0] ?? '';

            $value = trim($column[1] ?? '');

            if ($value == '') {

                continue;

            }

            if (substr($name, -5) == '_like') {

                $field = substr($name,0,-5);

                $where[] = "LOWER($field) LIKE ?";

                $bindings[] = "%".strtolower($value)."%";

                continue;

            }

            if ($name == 'created_at_from') {

                $where[] = "DATE(created_at)>=?";

                $bindings[] = $value;

                continue;

            }

            if ($name == 'created_at_to') {

                $where[] = "DATE(created_at)<=?";

                $bindings[] = $value;

                continue;

            }

            $where[] = "$name=?";

            $bindings[] = $value;

        }

    }

    $whereSql = '';

    if(count($where)>0){

        $whereSql = " WHERE ".implode(" AND ",$where);

    }

    $countSql = "SELECT COUNT(*) FROM {$table}{$whereSql}";

    $stmt = $pdo->prepare($countSql);

    $stmt->execute($bindings);

    $totalRows = $stmt->fetchColumn();

    $sql = "SELECT * FROM {$table}{$whereSql} ORDER BY {$orderBy} {$orderType} LIMIT {$offset},{$rowsPerPage}";

    $stmt = $pdo->prepare($sql);

    $stmt->execute($bindings);

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [

        'records'=>[

            'data'=>$records,

            'current_page'=>$page,

            'last_page'=>ceil($totalRows/$rowsPerPage),

            'total'=>$totalRows

        ],

        'pages'=>getPages(

            $page,

            ceil($totalRows/$rowsPerPage),

            $totalRows

        ),

        'sl'=>$offset

    ];

}
function getPages($currentPage,$lastPage,$totalPages)
{

    $startPage = ($currentPage < 5)?1:$currentPage-4;

    $endPage = $startPage+8;

    if($endPage>$lastPage){

        $endPage=$lastPage;

    }

    $pages=[];

    if($startPage>1){

        $pages[]='...';

    }

    for($i=$startPage;$i<=$endPage;$i++){

        $pages[]=$i;

    }

    if($endPage<$lastPage){

        $pages[]='...';

    }

    return $pages;

}
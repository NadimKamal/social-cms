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

// if (!function_exists('old')) {

//     function old($key, $default = '')
//     {
//         return $_SESSION['_old'][$key] ?? $default;
//     }

// }

function old($key, $default = '')
{
    return $_POST[$key] ?? $default;
}

// if (!function_exists('old')) {

//     function old($key, $default = '')
//     {
//         $value = $_SESSION['_old'][$key] ?? $default;

//         unset($_SESSION['_old'][$key]);

//         if (empty($_SESSION['_old'])) {

//             unset($_SESSION['_old']);

//         }

//         return $value;
//     }

// }

if (!function_exists('setFlash')) {

    function setFlash($type, $message)
    {
        $_SESSION['_flash'] = [

            'type' => $type,

            'message' => $message

        ];
    }

}

if (!function_exists('getFlash')) {

    function getFlash()
    {
        if (!isset($_SESSION['_flash'])) {

            return null;

        }

        $flash = $_SESSION['_flash'];

        unset($_SESSION['_flash']);

        return $flash;
    }
}

if (!function_exists('withInput')) {

    function withInput(array $input)
    {
        $_SESSION['_old'] = $input;
    }

}

if (!function_exists('clearFlash')) {

    function clearFlash()
    {
        unset($_SESSION['_flash']);

        unset($_SESSION['_old']);
    }

}

if (!function_exists('clearOld')) {

    function clearOld()
    {
        unset($_SESSION['_old']);
    }

}

if (!function_exists('is_post')) {

    function is_post()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

}

if (!function_exists('generate_uuid')) {

    function generate_uuid()
    {
        return bin2hex(random_bytes(16));
    }

}

if (!function_exists('jsonResponse')) {

    function jsonResponse($data = [], int $status = 200)
    {
        http_response_code($status);

        header('Content-Type: application/json');

        echo json_encode($data);

        exit;
    }

}

if (!function_exists('successResponse')) {

    function successResponse($data = [], $message = 'Success')
    {
        jsonResponse([
            'success' => true,
            'message' => $message,
            'data'    => $data
        ]);
    }

}

if (!function_exists('errorResponse')) {

    function errorResponse($message = 'Error', $data = [])
    {
        jsonResponse([
            'success' => false,
            'message' => $message,
            'data'    => $data
        ]);
    }

}

if (!function_exists('isAjax')) {

    function isAjax()
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

}

if (!function_exists('sanitize')) {

    function sanitize($value)
    {
        return trim(strip_tags($value));
    }

}

if (!function_exists('env')) {

    function env($key, $default = null)
    {
        return $GLOBALS['env'][$key] ?? $default;
    }
}

if (!function_exists('is_get')) {

    function is_get()
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}

if (!function_exists('exists')) {

    function exists(PDO $pdo, string $table, array $where): bool
    {
        $conditions = [];
        $bindings = [];

        foreach ($where as $column => $value) {

            $conditions[] = "{$column} = ?";
            $bindings[] = $value;

        }

        $sql = "SELECT 1
                FROM {$table}
                WHERE " . implode(' AND ', $conditions) . "
                LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute($bindings);

        return (bool) $stmt->fetchColumn();
    }

}

if (!function_exists('findByUuidOrFail')) {

    function findByUuidOrFail(PDO $pdo, string $table, string $uuid): array
    {
        $stmt = $pdo->prepare("
            SELECT *
            FROM {$table}
            WHERE uuid = ?
            LIMIT 1
        ");

        $stmt->execute([$uuid]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {

            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'Record not found.'
            ];

            redirect('');

            exit;
        }

        return $row;
    }

}

function apiSuccess(array $data = [], string $message = ''): void
{
    header('Content-Type: application/json');

    echo json_encode([
        'success' => true,
        'message' => $message,
        'data'    => $data
    ]);

    exit;
}

if (!function_exists('find')) {

    function find(PDO $pdo, string $table, array $where = [], string $columns = '*')
    {
        $bindings = [];

        $sql = "SELECT {$columns} FROM {$table}";

        if (!empty($where)) {

            $conditions = [];

            foreach ($where as $column => $value) {

                $conditions[] = "{$column} = ?";
                $bindings[] = $value;

            }

            $sql .= " WHERE " . implode(' AND ', $conditions);

        }

        $sql .= " LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute($bindings);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

}

if (!function_exists('findOrFail')) {

    function findOrFail(PDO $pdo, string $table, array $where = [], string $columns = '*')
    {
        $row = find($pdo, $table, $where, $columns);

        if (!$row) {

            throw new Exception('Record not found.');

        }

        return $row;
    }

}

if (!function_exists('insert')) {

    function insert(PDO $pdo, string $table, array $data)
    {
        $columns = array_keys($data);

        $placeholders = array_fill(

            0,

            count($columns),

            '?'

        );

        $sql = "INSERT INTO {$table}
                (" . implode(',', $columns) . ")
                VALUES
                (" . implode(',', $placeholders) . ")";

        $stmt = $pdo->prepare($sql);

        $stmt->execute(array_values($data));

        return $pdo->lastInsertId();
    }

}

if (!function_exists('update')) {

    function update(PDO $pdo, string $table, array $data, array $where)
    {
        $sets = [];

        $bindings = [];

        foreach ($data as $column => $value) {

            $sets[] = "{$column}=?";

            $bindings[] = $value;

        }

        $conditions = [];

        foreach ($where as $column => $value) {

            $conditions[] = "{$column}=?";

            $bindings[] = $value;

        }

        $sql = "UPDATE {$table}
                SET " . implode(',', $sets) . "
                WHERE " . implode(' AND ', $conditions);

        $stmt = $pdo->prepare($sql);

        return $stmt->execute($bindings);

    }

}

if (!function_exists('delete')) {

    function delete(PDO $pdo, string $table, array $where)
    {
        $conditions = [];

        $bindings = [];

        foreach ($where as $column => $value) {

            $conditions[] = "{$column}=?";

            $bindings[] = $value;

        }

        $sql = "DELETE FROM {$table}
                WHERE " . implode(' AND ', $conditions);

        $stmt = $pdo->prepare($sql);

        return $stmt->execute($bindings);

    }

}

if (!function_exists('generate_uuid')) {

    function generate_uuid(): string
    {
        $data = random_bytes(16);

        // Version 4
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);

        // Variant RFC 4122
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf(
            '%s%s-%s-%s-%s-%s%s%s',
            str_split(bin2hex($data), 4)
        );
    }

}

function uploadImage(array $file, string $folder = 'general')
{
    if (
        !isset($file['error']) ||
        $file['error'] !== UPLOAD_ERR_OK
    ) {
        return null;
    }

    $allowed = [
        'jpg',
        'jpeg',
        'png',
        'gif',
        'webp'
    ];

    $extension = strtolower(
        pathinfo($file['name'], PATHINFO_EXTENSION)
    );

    if (!in_array($extension, $allowed)) {
        throw new Exception('Invalid image format.');
    }

    $directory = UPLOAD_PATH . '/' . trim($folder, '/');

    if (!is_dir($directory)) {
        mkdir($directory, 0777, true);
    }

    // filename only
    $fileName = uniqid('', true) . '.' . $extension;

    $destination = $directory . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new Exception('Failed to upload image.');
    }

    return 'uploads/' . trim($folder, '/') . '/' . $fileName;
}

if (!function_exists('uploadVideo')) {

    function uploadVideo(array $file, string $folder = 'videos')
    {

        if (
            !isset($file['error']) ||
            $file['error'] !== UPLOAD_ERR_OK
        ) {
            return null;
        }

        $allowed = [

            'mp4',

            'avi',

            'mov',

            'mkv',

            'webm'

        ];

        $extension = strtolower(
            pathinfo($file['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extension, $allowed)) {

            throw new Exception('Invalid video format.');

        }

        $uploadDir = ROOT_PATH . "/uploads/{$folder}/";

        if (!is_dir($uploadDir)) {

            mkdir($uploadDir, 0777, true);

        }

        $filename = uniqid('video_') . '.' . $extension;

        move_uploaded_file(

            $file['tmp_name'],

            $uploadDir . $filename

        );

        return "uploads/{$folder}/{$filename}";

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

if (!function_exists('deleteFile')) {

    function deleteFile($path)
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
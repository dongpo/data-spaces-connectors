<?php
// participants/index.php - 查詢參與者（需 Token）


// 1. 確保 Authorization Header 能讀到

if (!isset($_SERVER['HTTP_AUTHORIZATION']) || empty($_SERVER['HTTP_AUTHORIZATION'])) {
    if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } else {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (!empty($authHeader)) {
            $_SERVER['HTTP_AUTHORIZATION'] = $authHeader;
        }
    }
}


// 2. 引入設定

session_start();
require_once '../adm/config/config.php';
require_once '../adm/config/getformin.php';
require_once '../adm/config/createimg.php';
require_once '../adm/config/share.php';
require_once '../adm/config/did.php';
require_once '../adm/config/Response.php';
require_once '../adm/config/TokenAuth.php';

$pdo = new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo->exec("SET NAMES ".$GLOBALS["db_encode"]);


// 3. 驗證 Token

$payload = requireToken();


// 4. 取得 participant_id

$participant_id = getPathId();

if ($participant_id <= 0) {
    errorResponse(1035, 400, 'invalid_request', 'Missing participant_id.');
}


// 5. 查詢參與者

$participant = share_getinfo('res_entities', 'thisid', $participant_id);

if (!$participant) {
    errorResponse(1036, 404, 'not_found', 'Participant not found.');
}

if ($participant['isopen'] != 1) {
    errorResponse(1036, 404, 'not_found', 'Participant not found.');
}


// 6. 組裝回傳資料

//$domain = "ttcdb.demosite.com.tw";
$domain = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
$response = [
    'participant_id' => "did:web:{$domain}:participant:{$participant['thisid']}",
    'did' => "did:web:{$domain}:participant:{$participant['thisid']}",
    'name' => $participant['thisname'],
    'status' => 'active'
];


// 7. 回傳

successResponse($response);

$pdo = null;
?>
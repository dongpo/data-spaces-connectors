<?php



// 1. 引入設定

session_start();
require_once '../adm/config/config.php';
require_once '../adm/config/getformin.php';
require_once '../adm/config/createimg.php';
require_once '../adm/config/share.php';
require_once '../adm/config/did.php';
$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);


// 2. 取得 agreement_id

$agreement_id = getPathId();

if ($agreement_id <= 0) {
    outputError(400, 'Missing agreement_id');
}


// 3. 請求頻率限制

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!checkRateLimit($ip)) {
    http_response_code(429);
    outputError(429, 'Too many requests');
}


// 4. 查詢合約

$contract = share_getinfo('gaia_contracts', 'thisid', $agreement_id);

if (!$contract) {
    outputError(404, 'Agreement not found');
}


// 5. 檢查合約是否生效中（只有生效中的合約才公開）

if ($contract['status'] != 1) {
    outputError(404, 'Agreement not found');
}


// 6. 解析 ODRL Agreement

$agreement = json_decode($contract['odrl_agreement_json'] ?? '', true);

if (!$agreement) {
    outputError(404, 'Agreement not found');
}


// 7. 回傳

outputSuccess($agreement);
$pdo = null;
?>
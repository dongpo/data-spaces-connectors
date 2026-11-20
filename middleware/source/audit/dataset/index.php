<?php
// audit/dataset/index.php - 查詢產品紀錄


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
require_once '../../adm/config/config.php';
require_once '../../adm/config/getformin.php';
require_once '../../adm/config/createimg.php';
require_once '../../adm/config/share.php';
require_once '../../adm/config/did.php';
require_once '../../adm/config/Response.php';
require_once '../../adm/config/TokenAuth.php';

$pdo = new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo->exec("SET NAMES ".$GLOBALS["db_encode"]);


// 3. 驗證 Token

$payload = requireToken();


// 4. 取得產品 ID

$productId = $_GET['id'] ?? 0;
if ($productId <= 0) {
    errorResponse(1033, 400, 'invalid_request', 'Missing dataset_id.');
}


// 5. 從 Token 取得組織 ID

$participantId = $payload['participant_id'] ?? '';
$orgId = 0;
if (preg_match('/participant:(\d+)/', $participantId, $matches)) {
    $orgId = (int)$matches[1];
}
if ($orgId <= 0) {
    errorResponse(1012, 403, 'forbidden', 'No permission to view this resource.');
}


// 6. ✅ 檢查產品是否存在且屬於該組織

$product = share_getinfo('gaia_data_products', 'thisid', $productId);
if (!$product) {
    errorResponse(1034, 404, 'not_found', 'Dataset not found.');
}
if ($product['owner_id'] != $orgId) {
    errorResponse(1012, 403, 'forbidden', 'No permission to view this resource.');
}


// 7. 取得該組織所有使用者 ID

$userIds = share_getfree("
    SELECT thisid FROM fun_user 
    WHERE thisentid = '" . addslashes($orgId) . "'
");
$userIdList = [];
foreach ($userIds as $u) {
    $userIdList[] = $u['thisid'];
}

if (empty($userIdList)) {
    successResponse(['data' => [], 'total' => 0]);
    exit;
}
$userIdStr = implode("','", $userIdList);


// 8. 取得該產品的所有版本 ID

$versions = share_getfree("
    SELECT thisid FROM gaia_product_versions 
    WHERE product_id = '" . addslashes($productId) . "'
");
$versionIds = [];
foreach ($versions as $v) {
    $versionIds[] = $v['thisid'];
}


// 9. 組裝查詢條件

$where = "(target_type = 'product' AND target_id = '" . addslashes($productId) . "')";
if (!empty($versionIds)) {
    $versionIdStr = implode("','", $versionIds);
    $where .= " OR (target_type = 'version' AND target_id IN ('" . $versionIdStr . "'))";
}
$where .= " AND changed_by IN ('" . $userIdStr . "')";


// 10. 查詢 Log

$sql = "SELECT 
            thisid,
            target_type,
            target_id,
            action,
            action_description,
            snapshot_data,
            change_summary,
            changed_by,
            changed_at,
            ip_address,
            user_agent,
            owner_id
        FROM gaia_product_log
        WHERE " . $where . "
        ORDER BY changed_at DESC";

$logs = share_getfree($sql);


// 11. 處理 snapshot_data

foreach ($logs as &$log) {
    if (!empty($log['snapshot_data'])) {
        $log['snapshot_data'] = json_decode($log['snapshot_data'], true);
        if (!is_array($log['snapshot_data'])) {
            $log['snapshot_data'] = null;
        }
    } else {
        $log['snapshot_data'] = null;
    }
}
unset($log);


// 12. 回傳結果

successResponse([
    'data' => $logs,
    'total' => count($logs)
]);

$pdo = null;
?>
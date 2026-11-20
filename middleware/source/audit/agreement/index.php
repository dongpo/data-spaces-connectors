<?php
// audit/agreement/index.php - 查詢合約簽署紀錄


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


// 4. 取得合約 ID

$contractId = $_GET['id'] ?? 0;
if ($contractId <= 0) {
    errorResponse(1039, 400, 'invalid_request', 'Missing agreement_id.');
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


// 6. 檢查合約是否存在且屬於該組織

$contract = share_getinfo('gaia_contracts', 'thisid', $contractId);
if (!$contract) {
    errorResponse(1021, 404, 'not_found', 'Contract not found.');
}

// 檢查權限：必須是 provider 或 applicant
if ($contract['provider_org_id'] != $orgId && $contract['applicant_org_id'] != $orgId) {
    errorResponse(1011, 403, 'forbidden', 'No permission to view this agreement.');
}


// 7. 取得申請 ID（application_id = subscription_id）

$subscriptionId = $contract['application_id'];
if ($subscriptionId <= 0) {
    errorResponse(1021, 404, 'not_found', 'Contract not found.');
}


// 8. 取得該組織所有使用者 ID

$userIds = share_getfree("
    SELECT thisid FROM fun_user 
    WHERE thisentid = '" . addslashes($orgId) . "'
");
$userIdList = [];
foreach ($userIds as $u) {
    $userIdList[] = $u['thisid'];
}

$userIdStr = '';
if (!empty($userIdList)) {
    $userIdStr = implode("','", $userIdList);
}


// 9. 查詢 Log

// 組裝查詢條件
$where = "target_type = 'subscription' AND target_id = '" . addslashes($subscriptionId) . "'";

// 權限條件：自己公司的人做的操作 OR 別人對我的產品做的操作
$permissionConditions = [];
if (!empty($userIdStr)) {
    $permissionConditions[] = "changed_by IN ('" . $userIdStr . "')";
}
$permissionConditions[] = "owner_id = '" . addslashes($orgId) . "'";

$where .= " AND (" . implode(" OR ", $permissionConditions) . ")";

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
        FROM gaia_contract_log
        WHERE " . $where . "
        ORDER BY changed_at DESC";

$logs = share_getfree($sql);


// 10. 處理 snapshot_data

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


// 11. 回傳結果

successResponse([
    'data' => $logs,
    'total' => count($logs)
]);

$pdo = null;
?>
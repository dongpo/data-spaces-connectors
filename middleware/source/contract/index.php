<?php
// contract/index.php - 查詢單一合約


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


// 4. 取得合約 ID

$contractId = $_GET['id'] ?? 0;
if ($contractId <= 0) {
    errorResponse(1025, 400, 'invalid_request', 'Missing contract id.');
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


// 6. 查詢合約

$sql = "SELECT 
            thisid,
            application_id,
            product_id,
            version_id,
            provider_org_id,
            applicant_org_id,
            contract_duration_days,
            effective_at,
            expired_at,
            allowed_actions,
            constraints,
            prohibited_actions,
            version_snapshot,
            application_snapshot,
            odrl_agreement_json,
            status,
            terminated_at,
            terminated_by,
            terminated_reason,
            created_at,
            updated_at
        FROM gaia_contracts
        WHERE thisid = '" . addslashes($contractId) . "'";

$contracts = share_getfree($sql);

if (empty($contracts)) {
    errorResponse(1021, 404, 'not_found', 'Contract not found.');
}

$contract = $contracts[0];


// 7. 權限檢查

if ($contract['provider_org_id'] != $orgId && $contract['applicant_org_id'] != $orgId) {
    errorResponse(1011, 403, 'forbidden', 'No permission to view this agreement.');
}


// 8. 處理 JSON 欄位

$jsonFields = ['allowed_actions', 'constraints', 'prohibited_actions', 'version_snapshot', 'application_snapshot', 'odrl_agreement_json'];
foreach ($jsonFields as $field) {
    if (!empty($contract[$field])) {
        $decoded = json_decode($contract[$field], true);
        $contract[$field] = is_array($decoded) ? $decoded : null;
    } else {
        $contract[$field] = null;
    }
}


// 9. 回傳結果

successResponse($contract);

$pdo = null;
?>
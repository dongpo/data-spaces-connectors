<?php
// contract/all.php - 我的合約列表


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


// 4. 從 Token 取得組織 ID

$participantId = $payload['participant_id'] ?? '';
$orgId = 0;
if (preg_match('/participant:(\d+)/', $participantId, $matches)) {
    $orgId = (int)$matches[1];
}
if ($orgId <= 0) {
    errorResponse(1012, 403, 'forbidden', 'No permission to view this resource.');
}


// 5. 查詢合約列表

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
        WHERE provider_org_id = '" . addslashes($orgId) . "'
           OR applicant_org_id = '" . addslashes($orgId) . "'
        ORDER BY provider_org_id, applicant_org_id";

$contracts = share_getfree($sql);


// 6. 處理 JSON 欄位

foreach ($contracts as &$contract) {
    // allowed_actions
    if (!empty($contract['allowed_actions'])) {
        $contract['allowed_actions'] = json_decode($contract['allowed_actions'], true);
        if (!is_array($contract['allowed_actions'])) {
            $contract['allowed_actions'] = null;
        }
    } else {
        $contract['allowed_actions'] = null;
    }
    
    // constraints
    if (!empty($contract['constraints'])) {
        $contract['constraints'] = json_decode($contract['constraints'], true);
        if (!is_array($contract['constraints'])) {
            $contract['constraints'] = null;
        }
    } else {
        $contract['constraints'] = null;
    }
    
    // prohibited_actions
    if (!empty($contract['prohibited_actions'])) {
        $contract['prohibited_actions'] = json_decode($contract['prohibited_actions'], true);
        if (!is_array($contract['prohibited_actions'])) {
            $contract['prohibited_actions'] = null;
        }
    } else {
        $contract['prohibited_actions'] = null;
    }
    
    // version_snapshot
    if (!empty($contract['version_snapshot'])) {
        $contract['version_snapshot'] = json_decode($contract['version_snapshot'], true);
        if (!is_array($contract['version_snapshot'])) {
            $contract['version_snapshot'] = null;
        }
    } else {
        $contract['version_snapshot'] = null;
    }
    
    // application_snapshot
    if (!empty($contract['application_snapshot'])) {
        $contract['application_snapshot'] = json_decode($contract['application_snapshot'], true);
        if (!is_array($contract['application_snapshot'])) {
            $contract['application_snapshot'] = null;
        }
    } else {
        $contract['application_snapshot'] = null;
    }
    
    // odrl_agreement_json
    if (!empty($contract['odrl_agreement_json'])) {
        $contract['odrl_agreement_json'] = json_decode($contract['odrl_agreement_json'], true);
        if (!is_array($contract['odrl_agreement_json'])) {
            $contract['odrl_agreement_json'] = null;
        }
    } else {
        $contract['odrl_agreement_json'] = null;
    }
}
unset($contract);


// 7. 回傳結果

successResponse([
    'data' => $contracts,
    'total' => count($contracts)
]);

$pdo = null;
?>
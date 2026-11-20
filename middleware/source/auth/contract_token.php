<?php
// auth/contract_token.php - 拿 Identity Token 換取 Transaction Token (提貨憑證)
// 搭配 .htaccess 路由: GET /contracts/{id}/transaction-token


// 1. 引入設定與 DB 初始化

session_start();
require_once '../adm/config/config.php';
require_once '../adm/config/getformin.php';
require_once '../adm/config/createimg.php';
require_once '../adm/config/share.php';
require_once '../adm/config/did.php';
require_once '../adm/config/Response.php';

// 設定 CORS Header，處理跨域請求
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

$pdo = new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo->exec("SET NAMES ".$GLOBALS["db_encode"]);


// 2. 檢查請求方法 (配合 RESTful 規範改為 GET)

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    errorResponse(1050, 405, 'method_not_allowed', 'Method not allowed. Use GET.');
}


// 3. 從 Header 提取 auth/token.php 發出的 Identity Token

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    errorResponse(1001, 401, 'unauthorized', 'Missing or invalid Authorization header.');
}

$identityToken = $matches[1];
$jwtParts = explode('.', $identityToken);

if (count($jwtParts) !== 3) {
    errorResponse(1052, 400, 'invalid_grant', 'Malformed Identity Token structure.');
}

list($b64Header, $b64Payload, $b64Signature) = $jwtParts;

$header  = json_decode(base64UrlDecode($b64Header), true);
$payload = json_decode(base64UrlDecode($b64Payload), true);

if (!is_array($header) || !is_array($payload)) {
    errorResponse(1053, 400, 'invalid_grant', 'Invalid JSON in Identity Token.');
}


// 4. 驗證 Identity Token 內容與簽章

if (($payload['type'] ?? '') !== 'identity_token') {
    errorResponse(1058, 401, 'invalid_grant', 'Invalid token type. Identity Token required.');
}

$now = time();
$leeway = 30;
if (!isset($payload['exp']) || ($payload['exp'] + $leeway) < $now) {
    errorResponse(1007, 401, 'invalid_grant', 'Identity Token has expired.');
}

// 載入 Server 私鑰驗證簽章
$serverPrivateKeyPath = __DIR__ . '/../server_private.key';
if (!file_exists($serverPrivateKeyPath)) {
    errorResponse(1062, 500, 'server_error', 'Server private key file not found.');
}

$serverPrivateKey = file_get_contents($serverPrivateKeyPath);
$privateKeyResource = openssl_pkey_get_private($serverPrivateKey);

if (!$privateKeyResource) {
    errorResponse(1062, 500, 'server_error', 'Server private key loading failed.');
}

$keyDetails = openssl_pkey_get_details($privateKeyResource);
$serverPublicKey = $keyDetails['key'];

$dataToVerify = $b64Header . '.' . $b64Payload;
$signature = base64UrlDecode($b64Signature);
$verifyResult = openssl_verify($dataToVerify, $signature, $serverPublicKey, OPENSSL_ALGO_SHA256);

if ($verifyResult !== 1) {
    errorResponse(1004, 401, 'invalid_client', 'Identity Token signature verification failed.');
}

// 從 Identity Token 解析身份
$consumerClientId = $payload['sub'] ?? '';
$participantDid   = $payload['participant_id'] ?? '';

$parts = explode(':', $participantDid);
$ownerId = intval(end($parts));


// 5. 接收 GET 參數 (contractId) - 搭配 .htaccess

$contractId = intval($_GET['id'] ?? 0);

if ($contractId <= 0) {
    errorResponse(1025, 400, 'invalid_request', 'Missing or invalid contract ID (agreement_id).');
}


// 6. 查驗合約 (gaia_contracts) 並且連動 3 層結構的 gaia_schema_version

$stmt = $pdo->prepare("
    SELECT c.*, 
           ma.machine_url AS provider_endpoint,
           pv.odrl_policy_json,
           pv.odrl_policy_simple_json
    FROM gaia_contracts c
    LEFT JOIN gaia_product_versions pv ON c.version_id = pv.thisid
    LEFT JOIN machine_accounts ma ON pv.connector_id = ma.thisid
    WHERE c.thisid = ?  
	AND c.status = 1 
    AND c.expired_at > NOW()	
    LIMIT 1
");
$stmt->execute([$contractId]);
$contract = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contract) {
    errorResponse(1054, 403, 'access_denied', 'Contract not found, inactive, or expired.');
}

// 嚴格驗證：確認當前呼叫者組織 ($ownerId) 是否為此合約的合法申請者 (applicant_org_id)
$applicantOrgId = intval($contract['applicant_org_id']);
$providerOrgId  = intval($contract['provider_org_id']);

if ($ownerId !== $applicantOrgId && $ownerId !== $providerOrgId) {
    errorResponse(1055, 403, 'unauthorized_contract_access', 'Caller organization is neither the applicant nor the provider of this contract.');
}


// 7. Token 狀態管理 (gaia_contract_tokens 作廢舊 Token、寫入新 Token)

$updateStmt = $pdo->prepare("UPDATE gaia_contract_tokens SET status = 0 WHERE contract_id = ? AND status = 1");
$updateStmt->execute([$contractId]);

$jti = 'jwt_' . bin2hex(random_bytes(16));
$exp = $now + 3600; // Transaction Token 效期 1 小時

$insertStmt = $pdo->prepare("
    INSERT INTO gaia_contract_tokens 
    (jti, contract_id, consumer_ent_id, issued_at, effective_at, expires_at, status) 
    VALUES (?, ?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?), FROM_UNIXTIME(?), 1)
");
$insertStmt->execute([$jti, $contractId, $ownerId, $now, $now, $exp]);


// 8. 簽發 Transaction Token JWT (RS256)

//$domain = "ttcdb.demosite.com.tw";
$domain = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
$iss = "https://{$domain}";


$policyContext = !empty($contract['odrl_policy_json']) 
    ? json_decode($contract['odrl_policy_json'], true) 
    : json_decode($contract['odrl_policy_simple_json'] ?? '{}', true);

$header = ['alg' => 'RS256', 'typ' => 'JWT'];
$payload = [
    'iss' => $iss,
    'sub' => $consumerClientId,
    'aud' => $contract['provider_org_id'] ?? '',
    'type' => 'transaction_token',
    'iat' => $now,
    'exp' => $exp,
    'jti' => $jti,
    'agreement_id' => $contractId,
    'dataset_id' => $contract['product_id'] ?? '',
    'dspace:context' => $policyContext
];


$jwtHeader   = base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
$jwtPayload  = base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
$signingInput = $jwtHeader . '.' . $jwtPayload;

$signature = '';
openssl_sign($signingInput, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
$contractToken = $signingInput . '.' . base64UrlEncode($signature);


// 9. 回傳 Transaction Token (全面使用 JSON 序列化安全輸出)

echo json_encode([
    'access_token' => $contractToken,
    'token_type'   => 'Bearer',
    'expires_in'   => 3600,
    'endpoint'     => htmlspecialchars($contract['provider_endpoint'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$pdo = null;
?>

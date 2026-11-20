<?php
// auth/verify.php - 驗證 Contract Token (提貨憑證) 有效性 API


// 1. 引入設定與 DB 初始化 (完全與 auth/token.php 一致)

session_start();
require_once '../adm/config/config.php';
require_once '../adm/config/getformin.php';
require_once '../adm/config/createimg.php';
require_once '../adm/config/share.php';
require_once '../adm/config/did.php';
require_once '../adm/config/Response.php';

$pdo = new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo->exec("SET NAMES ".$GLOBALS["db_encode"]);
header('Content-Type: application/json; charset=utf-8');


// 2. 檢查請求方法 (僅允許 POST)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse(1050, 405, 'method_not_allowed', 'Method not allowed. Use POST.');
}


// 3. 提取 Token (支援 Header 或 POST 參數 token / access_token)

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

$contractToken = '';
if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    $contractToken = $matches[1];
} else {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $contractToken = trim($input['token'] ?? ($input['access_token'] ?? ''));
}

if (empty($contractToken)) {
    errorResponse(1001, 401, 'unauthorized', 'Missing token in Authorization header or POST body.');
}


// 4. 解析 JWT 結構

$jwtParts = explode('.', $contractToken);
if (count($jwtParts) !== 3) {
    errorResponse(1052, 400, 'invalid_grant', 'Malformed Contract Token structure.');
}

list($b64Header, $b64Payload, $b64Signature) = $jwtParts;

$header  = json_decode(base64UrlDecode($b64Header), true);
$payload = json_decode(base64UrlDecode($b64Payload), true);

if (!is_array($header) || !is_array($payload)) {
    errorResponse(1053, 400, 'invalid_grant', 'Invalid JSON in Token payload.');
}


// 5. 驗證 Token 類型與效期

if (($payload['type'] ?? '') !== 'transaction_token') {
    errorResponse(1058, 401, 'invalid_grant', 'Invalid token type. Expected transaction_token.');
}

$now = time();
$leeway = 30; // 30 秒容錯時間
if (!isset($payload['exp']) || ($payload['exp'] + $leeway) < $now) {
    errorResponse(1007, 401, 'invalid_grant', 'Contract Token has expired.');
}


// 6. 驗證平台簽名 (使用 Server 私鑰導出公鑰驗證)

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
    errorResponse(1004, 401, 'invalid_client', 'Contract Token signature verification failed.');
}


// 7. 資料庫查驗 Token 狀態 (gaia_contract_tokens)

$jti = isset($payload['jti']) ? trim($payload['jti']) : '';
$agreementId = isset($payload['agreement_id']) ? intval($payload['agreement_id']) : 0;

if (empty($jti) || empty($agreementId)) {
    errorResponse(1052, 400, 'invalid_grant', 'Missing jti or agreement_id in token payload.');
}

// 針對 jti 進行格式白名單驗證（例如限英數字與連字號），避免特殊字元反射
if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $jti)) {
    errorResponse(1052, 400, 'invalid_grant', 'Invalid jti format.');
}

$tokenStmt = $pdo->prepare("SELECT * FROM gaia_contract_tokens WHERE jti = ? AND status = 1 LIMIT 1");
$tokenStmt->execute([$jti]);
$tokenRecord = $tokenStmt->fetch(PDO::FETCH_ASSOC);

if (!$tokenRecord) {
    // 若錯誤訊息需帶入變數，必須使用 htmlspecialchars 進行情境編碼
    $safeJti = htmlspecialchars($jti, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    errorResponse(1059, 401, 'invalid_grant', 'Contract Token is revoked or inactive. (jti: ' . $safeJti . ')');
}

// Bind the signed token to the stored issuance record.
if ((int)$tokenRecord['contract_id'] !== $agreementId ||
    strtotime($tokenRecord['effective_at']) > $now || strtotime($tokenRecord['expires_at']) <= $now) {
    errorResponse(1059, 401, 'invalid_grant', 'Token record does not match or is outside its valid period.');
}

// 8. 資料庫查驗合約狀態 (gaia_contracts)

$contractStmt = $pdo->prepare("SELECT * FROM gaia_contracts WHERE thisid = ? AND status = 1 AND effective_at <= NOW() AND expired_at > NOW() LIMIT 1");
$contractStmt->execute([$agreementId]);
$contract = $contractStmt->fetch(PDO::FETCH_ASSOC);

if (!$contract) {
    errorResponse(1054, 403, 'access_denied', 'Associated contract is expired or inactive.');
}


if ((int)($payload['dataset_id'] ?? 0) !== (int)$contract['product_id'] ||
    (string)($payload['aud'] ?? '') !== (string)$contract['provider_org_id']) {
    errorResponse(1059, 403, 'access_denied', 'Token resource or provider does not match its contract.');
}

// 9. 驗證成功，回傳 Token 載荷資訊 (Payload)

echo json_encode([
    'active'       => true,
    'code'         => 200,
    'message'      => 'Token is valid.',
    'agreement_id' => $agreementId,
    'consumer_sub' => $payload['sub'] ?? '',
    'dataset_id'   => $payload['dataset_id'] ?? '',
    'expires_at'   => $payload['exp'] ?? 0,
    'payload'      => $payload
], JSON_UNESCAPED_UNICODE);

$pdo = null;
?>
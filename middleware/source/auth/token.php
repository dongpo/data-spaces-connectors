<?php
// auth/token.php - 發放 Identity Token


// 1. 引入設定

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


// 2. 檢查請求方法

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    errorResponse(1050, 405, 'method_not_allowed', 'Method not allowed. Use POST.');
}


// 3. 接收 POST 參數

$grantType       = $_POST['grant_type'] ?? '';
$assertionType   = $_POST['client_assertion_type'] ?? '';
$clientId        = trim($_POST['client_id'] ?? '');
$clientAssertion = $_POST['client_assertion'] ?? '';

if (
    $grantType !== 'client_credentials' ||
    $assertionType !== 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer' ||
    empty($clientId) ||
    empty($clientAssertion)
) {
    errorResponse(1025, 400, 'invalid_request', 'Missing or invalid required OAuth parameters.');
}


// 4. 解析 client_id

$parts = explode(':', $clientId);
$connectorId = end($parts);

if (!is_numeric($connectorId) || $connectorId <= 0) {
    $safeClientId = htmlspecialchars($clientId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    errorResponse(1051, 400, 'invalid_request', 'Invalid client_id format. (client_id: ' . $safeClientId . ')');
}


// 5. 解析 JWT（header.payload.signature）

$jwtParts = explode('.', $clientAssertion);
if (count($jwtParts) !== 3) {
    errorResponse(1052, 400, 'invalid_grant', 'Malformed JWT structure.');
}

list($b64Header, $b64Payload, $b64Signature) = $jwtParts;

$header = json_decode(base64UrlDecode($b64Header), true);
$payload = json_decode(base64UrlDecode($b64Payload), true);

if (!is_array($header) || !is_array($payload)) {
    errorResponse(1053, 400, 'invalid_grant', 'Invalid JSON in JWT header or payload.');
}


// 6. 檢查演算法（必須是 RS256）

if (($header['alg'] ?? '') !== 'RS256') {
    errorResponse(1003, 400, 'invalid_grant', 'Unsupported algorithm. Only RS256 is accepted.');
}


// 7. 查詢 machine_accounts 取得公鑰

$machine = share_getinfo('machine_accounts', 'thisid', $connectorId);

if (!$machine) {
    $safeConnectorId = htmlspecialchars($connectorId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    errorResponse(1054, 401, 'invalid_client', 'Connector not registered. (id: ' . $safeConnectorId . ')');
}

if ($machine['isopen'] != 1) {
    errorResponse(1055, 403, 'access_denied', 'Connector is not active.');
}

$participant = share_getinfo('res_entities', 'thisid', $machine['owner_id']);
if (!$participant || $participant['isopen'] != 1) {
    errorResponse(1056, 403, 'access_denied', 'Participant organization is not active.');
}

$clientPublicKeyPem = trim($machine['thispk'] ?? '');
if (empty($clientPublicKeyPem)) {
    errorResponse(1057, 401, 'invalid_client', 'Public key not registered.');
}


// 8. 驗證 JWT 簽章（用 Client 公鑰）

$dataToVerify = $b64Header . '.' . $b64Payload;
$signature = base64UrlDecode($b64Signature);
$verifyResult = openssl_verify($dataToVerify, $signature, $clientPublicKeyPem, OPENSSL_ALGO_SHA256);

if ($verifyResult !== 1) {
    errorResponse(1004, 401, 'invalid_client', 'Signature verification failed.');
}


// 9. 驗證 Payload

$now = time();
$leeway = 30;
// 動態取得當前主機與完整 token 驗證網址
$domain = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
$platformTokenUrl = "https://{$domain}/auth/token";
//$platformTokenUrl = 'https://ttcdb.demosite.com.tw/auth/token';

if (($payload['aud'] ?? '') !== $platformTokenUrl) {
    errorResponse(1058, 401, 'invalid_grant', 'Audience mismatch.');
}

if (($payload['iss'] ?? '') !== $clientId || ($payload['sub'] ?? '') !== $clientId) {
    errorResponse(1059, 401, 'invalid_grant', 'Issuer or Subject does not match client_id.');
}

if (!isset($payload['exp']) || ($payload['exp'] + $leeway) < $now) {
    errorResponse(1007, 401, 'invalid_grant', 'Token has expired.');
}

if (isset($payload['iat']) && ($payload['iat'] - $leeway) > $now) {
    errorResponse(1060, 401, 'invalid_grant', 'Token issued in the future.');
}

if (empty($payload['jti'] ?? '')) {
    errorResponse(1061, 400, 'invalid_grant', 'Missing jti (JWT ID).');
}


// 10. 取得角色

$orgType = $participant['org_type'] ?? '';
$roleMap = [
    '資料提供者' => 'provider',
    '資料使用者' => 'user',
    '兩者皆有' => 'both'
];
$role = $roleMap[$orgType] ?? 'user';


// 11. 載入 Server 私鑰

//$domain = "ttcdb.demosite.com.tw";

$iss = "https://{$domain}";

$serverPrivateKeyPath = __DIR__ . '/../server_private.key';
if (!file_exists($serverPrivateKeyPath)) {
    errorResponse(1062, 500, 'server_error', 'Server private key file not found.');
}

$serverPrivateKey = file_get_contents($serverPrivateKeyPath);
$privateKeyResource = openssl_pkey_get_private($serverPrivateKey);

if (!$privateKeyResource) {
    errorResponse(1062, 500, 'server_error', 'Server private key loading failed.');
}


// 12. 簽發 Identity Token（RS256）

$header = ['alg' => 'RS256', 'typ' => 'JWT'];
$payload = [
    'iss' => $iss,
    'sub' => $clientId,
    'participant_id' => "did:web:{$domain}:participant:{$machine['owner_id']}",
    'role' => $role,
    'type' => 'identity_token',
    'iat' => $now,
    'exp' => $now + 28800
];

$jwtHeader = base64UrlEncode(json_encode($header));
$jwtPayload = base64UrlEncode(json_encode($payload));
$signingInput = $jwtHeader . '.' . $jwtPayload;

$signature = '';
openssl_sign($signingInput, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
$identityToken = $signingInput . '.' . base64UrlEncode($signature);


// 13. 回傳 Token

echo json_encode([
    'access_token' => $identityToken,
    'token_type'   => 'Bearer',
    'expires_in'   => 28800,
    'scope'        => 'catalog contract token'
], JSON_UNESCAPED_UNICODE);

$pdo = null;
?>
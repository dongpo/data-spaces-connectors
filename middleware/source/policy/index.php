<?php
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

$payload = requireToken();

$policy_id = getPathId();
if ($policy_id <= 0) {
    errorResponse(1031, 400, 'invalid_request', 'Missing policy_id.');
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!checkRateLimit($ip)) {
    http_response_code(429);
    errorResponse(429, 429, 'too_many_requests', 'Too many requests.');
}

$versionData = share_getinfo('gaia_product_versions', 'thisid', $policy_id);
if (!$versionData || $versionData['isopen'] == 0) {
    errorResponse(1032, 404, 'not_found', 'Policy not found.');
}

$product = share_getinfo('gaia_data_products', 'thisid', $versionData['product_id']);
if (!$product || $product['isopen'] != 1) {
    errorResponse(1032, 404, 'not_found', 'Policy not found.');
}

if (empty($versionData['odrl_policy_simple_json'])) {
    errorResponse(1032, 404, 'not_found', 'Policy not found.');
}

$policy = json_decode($versionData['odrl_policy_simple_json'], true);
$newPolicy = ['@context' => 'https://www.w3.org/ns/odrl/2/'];
unset($policy['@context']);
$newPolicy = array_merge($newPolicy, $policy);
successResponse($newPolicy);

$pdo = null;
?>
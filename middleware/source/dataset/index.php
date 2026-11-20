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

$dataset_id = getPathId();
if ($dataset_id <= 0) {
    errorResponse(1033, 400, 'invalid_request', 'Missing dataset_id.');
}

$product = share_getinfo('gaia_data_products', 'thisid', $dataset_id);
if (!$product) {
    errorResponse(1034, 404, 'not_found', 'Dataset not found.');
}
if ($product['isopen'] != 1) {
    errorResponse(1034, 404, 'not_found', 'Dataset not found.');
}

if ($product['current_version_id'] <= 0) {
    errorResponse(1034, 404, 'not_found', 'Dataset not found.');
}

$versionData = share_getinfo('gaia_product_versions', 'thisid', $product['current_version_id']);
if (!$versionData) {
    errorResponse(1034, 404, 'not_found', 'Dataset not found.');
}
if ($versionData['isopen'] == 0) {
    errorResponse(1034, 404, 'not_found', 'Dataset not found.');
}

$fullPolicy = json_decode($versionData['odrl_policy_json'] ?? '', true);
if (!$fullPolicy) {
    errorResponse(1034, 404, 'not_found', 'Dataset not found.');
}

$context = [
    '@context' => [
        'odrl' => 'https://www.w3.org/ns/odrl/2/',
        'dcat' => 'http://www.w3.org/ns/dcat#',
        'dct' => 'http://purl.org/dc/terms/'
    ]
];

$result = array_merge($context, $fullPolicy);

if (isset($result['dataset']) && is_array($result['dataset'])) {
    if (!isset($result['dataset']['@type'])) {
        $result['dataset']['@type'] = 'dcat:Dataset';
    }
}

successResponse($result);

$pdo = null;
?>
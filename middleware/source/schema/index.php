<?php
// 1. 確保 Authorization Header 能正確讀取
if (empty($_SERVER['HTTP_AUTHORIZATION'])) {
    if (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $_SERVER['HTTP_AUTHORIZATION'] =$_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    } else {
        $headers = getallheaders();$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        if (!empty($authHeader)) {
            $_SERVER['HTTP_AUTHORIZATION'] =$authHeader;
        }
    }
}

// 2. 引入設定與工具模組
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
// 3. Rate Limit 檢查 (優先過濾，避免浪費資料庫與系統資源)
$ip =$_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
if (!checkRateLimit($ip)) {
    errorResponse(429, 429, 'too_many_requests', 'Too many requests.');
}

// 4. 驗證 Token (M2M 機器身分驗證)
$payload = requireToken();

// 5. 取得並驗證傳入的 schema_version_id
$schema_version_id = getPathId();
if ($schema_version_id <= 0) {
    errorResponse(1041, 400, 'invalid_request', 'Missing or invalid schema_id.');
}


// 三層式啟用狀態驗證 (階層依賴檢查)


// 6. 查詢第 3 層：Schema 版本 (gaia_schema_version)


$schemaVersionData = share_getinfo('gaia_schema_version', 'thisid', $schema_version_id);


if (!$schemaVersionData || (int)($schemaVersionData['isopen'] ?? 0) !== 1) {
    errorResponse(1042, 404, 'not_found', 'Schema not found.');
}

// 7. 查詢第 2 層：Schema 主表 (gaia_schema)
$schemaId = $schemaVersionData['schema_id'] ?? 0;


$parentSchema = share_getinfo('gaia_schema', 'thisid', $schemaId);
if (!$parentSchema || (int)($parentSchema['isopen'] ?? 0) !== 1) {
    errorResponse(1042, 404, 'not_found', 'Schema not found.');
}

// 8. 查詢第 1 層：Gaia-X 產業分類 (gaia_industry)

$industryId = $parentSchema['industry_id'] ?? 0;

$industry = share_getinfo('gaia_industry', 'thisid', $industryId);
if (!$industry || (int)($industry['isopen'] ?? 0) !== 1) {
    errorResponse(1042, 404, 'not_found', 'Schema not found.');
}


// JSON-LD 資料內容驗證與格式化


// 9. 檢查 Schema 內容是否為空
if (empty($schemaVersionData['schema_json'])) {
    errorResponse(1042, 404, 'not_found', 'Schema content is empty.');
}

// 10. 將資料庫的 JSON 字串解開為陣列（若已是陣列則保持原樣）
$responseSchema = is_string($schemaVersionData['schema_json']) 
    ? json_decode($schemaVersionData['schema_json'], true) 
    : $schemaVersionData['schema_json'];

// 11. 確保 JSON 解析成功
if (!is_array($responseSchema)) {
    errorResponse(1043, 500, 'internal_error', 'Invalid Schema JSON-LD format.');
}




// 12. 動態設定 @id 為 did:web 格式並輸出資料庫內的 JSON-LD
//$host = $_SERVER['HTTP_HOST'] ?? 'ttcdb.demosite.com.tw';
$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
// 動態組合成 did:web 的識別身分格式
$responseSchema['@id'] = "did:web:{$host}:schema:{$schema_version_id}";

header('Content-Type: application/ld+json; charset=utf-8');
successResponse($responseSchema);
$pdo = null;
?>
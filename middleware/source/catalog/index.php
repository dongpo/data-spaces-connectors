<?php
// catalog/index.php - 查詢產品目錄（需 Token）


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


// 4. 取得參數

$id = $_GET['id'] ?? 0;
$all = $_GET['all'] ?? 0;
$theme = $_GET['theme'] ?? '';
$keyword = $_GET['keyword'] ?? '';


// 5. 處理 /catalog/{id}（單一產品）

if ($id > 0) {
    $product = share_getinfo('gaia_data_products', 'thisid', $id);
    if (!$product || $product['isopen'] != 1) {
        errorResponse(1042, 404, 'not_found', 'Catalog not found.');
    }
    
    $versionData = share_getinfo('gaia_product_versions', 'thisid', $product['current_version_id']);
    if (!$versionData || $versionData['isopen'] == 0) {
        errorResponse(1042, 404, 'not_found', 'Catalog not found.');
    }
    
    $fullPolicy = json_decode($versionData['odrl_policy_json'] ?? '', true);
    if (!$fullPolicy) {
        errorResponse(1042, 404, 'not_found', 'Catalog not found.');
    }
    
    successResponse($fullPolicy);
}


// 6. 處理列表搜尋

//$domain = "ttcdb.demosite.com.tw";
$domain = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
$where = " p.isopen = 1 AND v.isopen = 2 AND p.current_version_id > 0 ";

if (!empty($theme)) {
    $where .= " AND v.industry_id = '" . addslashes($theme) . "' ";
}
if (!empty($keyword)) {
    $where .= " AND (v.display_name LIKE '%" . addslashes($keyword) . "%' OR v.keywords LIKE '%" . addslashes($keyword) . "%') ";
}

$catalog = share_getfree("
    SELECT 
        p.thisid AS product_id,
        v.display_name,
        v.version,
        v.description,
        v.industry_id,
        v.keywords,
        v.published_at,
        CONCAT('did:web:{$domain}:dataset:', p.thisid) AS dataset_id
    FROM gaia_data_products p
    INNER JOIN gaia_product_versions v ON p.current_version_id = v.thisid
    WHERE " . $where . "
    ORDER BY v.published_at DESC
");

// 轉換 keywords
foreach ($catalog as &$row) {
    if (!empty($row['keywords'])) {
        $row['keywords'] = json_decode($row['keywords'], true);
        if (!is_array($row['keywords'])) {
            $row['keywords'] = [];
        }
    } else {
        $row['keywords'] = [];
    }
    
    // 取得主題名稱
    if (!empty($row['industry_id'])) {
        $industry = share_getinfo('gaia_industry', 'thisid', $row['industry_id']);
        $row['theme_name'] = $industry['thisname'] ?? '';
        $row['theme_code'] = $industry['thiscode'] ?? '';
    } else {
        $row['theme_name'] = '';
        $row['theme_code'] = '';
    }
}
unset($row);

successResponse($catalog);

$pdo = null;
?>
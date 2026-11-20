<?php
/**
 * CSP 嚴格模式 (無萬用字元版)
 * 用法：$nonce = require __DIR__ . '/csp.php';
 */
if (defined('CSP_LOADED')) {
    return defined('CSP_NONCE') ? CSP_NONCE : '';
}
define('CSP_LOADED', true);

// 必須在任何輸出前載入
if (headers_sent($file, $line)) {
    error_log("CSP 無法送出，已於 {$file}:{$line} 輸出");
    if (!defined('CSP_NONCE')) {
        define('CSP_NONCE', '');
    }
    return CSP_NONCE;
}

try {
    $nonce = base64_encode(random_bytes(16));
} catch (Throwable $e) {
    error_log('Nonce 產生失敗：' . $e->getMessage());
    http_response_code(500);
    exit;
}

$csp = implode('; ', [
    "default-src 'self'",
    "base-uri 'self'",
    "object-src 'none'",
    "frame-ancestors 'none'",
    "form-action 'self'",
    "upgrade-insecure-requests",

    // 已移除所有帶有 * 的萬用字元，改用明確網域
    "script-src 'nonce-{$nonce}' 'strict-dynamic' 'self' "
        . "https://www.googletagmanager.com "
        . "https://maps.googleapis.com "
        . "https://fonts.googleapis.com "
        . "https://ajax.googleapis.com "
        . "https://www.gstatic.com "
        . "https://www.youtube.com "
        . "https://s.ytimg.com "
        . "https://cse.google.com",

    "style-src 'self' 'nonce-{$nonce}' https://fonts.googleapis.com",

    // 圖片來源明確化，移除 *
    "img-src 'self' data: blob: "
        . "https://www.gstatic.com "
        . "https://maps.googleapis.com "
        . "https://i.ytimg.com "
        . "https://s.ytimg.com "
        . "https://www.google-analytics.com "
        . "https://www.googletagmanager.com",

    // 連線來源明確化，移除 *
    "connect-src 'self' "
        . "https://www.google-analytics.com "
        . "https://analytics.google.com "
        . "https://www.googletagmanager.com "
        . "https://maps.googleapis.com",

    "font-src 'self' https://fonts.gstatic.com",
    "frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com https://cse.google.com",
    "worker-src 'self' blob:",
    "manifest-src 'self'",
]);

header('Content-Security-Policy: ' . $csp);

if (!defined('CSP_NONCE')) {
    define('CSP_NONCE', $nonce);
}
return CSP_NONCE;
?>
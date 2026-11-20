<?php
/**
 * CSP 寬鬆模式 (允許 unsafe-inline，適用於舊專案或過渡期)
 * 用法：$nonce = require __DIR__ . '/csp-relaxed.php';
 */

// 1. 避免重複載入
if (defined('CSP_LOADED')) {
    return defined('CSP_NONCE') ? CSP_NONCE : '';
}
define('CSP_LOADED', true);

// 2. 檢查 Header 是否已經送出
if (headers_sent($file, $line)) {
    error_log("CSP 無法送出，已於 {$file}:{$line} 輸出");
    if (!defined('CSP_NONCE')) {
        define('CSP_NONCE', '');
    }
    return CSP_NONCE;
}

// 3. 安全產生 Nonce (備用)
try {
    $nonce = base64_encode(random_bytes(16));
} catch (Throwable $e) {
    error_log('Nonce 產生失敗：' . $e->getMessage());
    http_response_code(500);
    exit;
}

// 4. 寬鬆模式 CSP 規則 (已移除惡意網域、允許 'unsafe-inline')
$csp = implode('; ', [
    "default-src 'self'",
    "base-uri 'self'",
    "object-src 'none'",
    "frame-ancestors 'none'",
    "form-action 'self'",
    "upgrade-insecure-requests",

    // 允許使用 'unsafe-inline' (內聯腳本)
    "script-src 'self' 'unsafe-inline' "
        . "https://www.googletagmanager.com "
        . "https://maps.googleapis.com "
        . "https://*.googleapis.com "
        . "https://*.gstatic.com "
        . "https://*.youtube.com "
        . "https://s.ytimg.com "
        . "https://cse.google.com",

    // 允許使用 'unsafe-inline' (內聯樣式)
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",

    "img-src 'self' data: blob: "
        . "https://*.gstatic.com "
        . "https://*.googleapis.com "
        . "https://*.ytimg.com "
        . "https://*.google-analytics.com "
        . "https://www.googletagmanager.com",

    "connect-src 'self' "
        . "https://*.google-analytics.com "
        . "https://*.analytics.google.com "
        . "https://*.googletagmanager.com "
        . "https://maps.googleapis.com "
        . "https://*.googleapis.com",

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
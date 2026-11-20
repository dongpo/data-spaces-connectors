<?php

function errorResponse($code, $httpCode, $error, $message) {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    
    // 對錯誤訊息進行 HTML 安全編碼，徹底防範反射型與儲存型 XSS
    $safeMessage = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    
    echo json_encode([
        'code' => $code,
        'error' => $error,
        'message' => $safeMessage
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function successResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}


function base64UrlDecode($data) {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/'));
}
?>
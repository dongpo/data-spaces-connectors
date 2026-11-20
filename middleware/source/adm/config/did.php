<?php
function outputError($code, $message) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => $message
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
function outputSuccess($data) {
    header('Content-Type: application/json');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
function getDid($type, $id, $domain = 'ttcdb.demosite.com.tw') {
    return "did:web:{$domain}:{$type}:{$id}";
}
function getPathId() {
    $path = $_SERVER['REQUEST_URI'];
    $path = strtok($path, '?');
    $path = trim($path, '/');
    $segments = explode('/', $path);
    return $segments[count($segments) - 1] ?? 0;
}
function checkRateLimit($ip, $limit = 60, $timeWindow = 60) {
    $tempDir = sys_get_temp_dir();
    $file = $tempDir . '/did_rate_limit_' . md5($ip);
    $now = time();
    
    $data = @json_decode(file_get_contents($file), true);
    if (!$data) {
        $data = ['count' => 0, 'first_request' => $now];
    }
    
    // 超過時間視窗，重置
    if ($now - $data['first_request'] > $timeWindow) {
        $data = ['count' => 0, 'first_request' => $now];
    }
    
    $data['count']++;
    
    @file_put_contents($file, json_encode($data));
    
    if ($data['count'] > $limit) {
        return false;
    }
    return true;
}
?>
<?php
// TokenAuth.php - Token 驗證

require_once __DIR__ . '/Response.php';
require_once __DIR__ . '/JwtValidator.php';
require_once __DIR__ . '/share.php';

function requireToken() {
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    
    if (empty($authHeader) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    
    if (empty($authHeader)) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    }
    
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        errorResponse(1001, 401, 'unauthorized', 'Missing or malformed Authorization Bearer header.');
    }
    
    $identityToken = $matches[1];
    $parts = explode('.', $identityToken);
    if (count($parts) !== 3) {
        errorResponse(1002, 401, 'invalid_token', 'Malformed JWT token structure.');
    }
    
    try {
        return JwtValidator::validateToken($authHeader);
    } catch (Exception $e) {
        errorResponse(1099, 401, 'unauthorized', $e->getMessage());
    }
}

function getOrgIdFromToken($payload) {
    $connectorId = $payload['sub'];
    $parts = explode(':', $connectorId);
    $connectorNum = end($parts);
    
    $machine = share_getinfo('machine_accounts', 'thisid', $connectorNum);
    if (empty($machine)) {
        return 0;
    }
    return $machine['owner_id'];
}

function canViewContract($contractId, $payload) {
    $orgId = getOrgIdFromToken($payload);
    if ($orgId == 0) {
        return false;
    }
    
    $contract = share_getinfo('gaia_contracts', 'thisid', $contractId);
    if (empty($contract)) {
        return false;
    }
    
    return ($contract['provider_org_id'] == $orgId || $contract['applicant_org_id'] == $orgId);
}
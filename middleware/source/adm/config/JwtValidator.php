<?php
// JwtValidator.php - 平台通用 JWT 驗證工具
require_once __DIR__ . '/Response.php';
class JwtValidator {
public static function validateToken($authHeader) {
    //error_log("authHeader: " . substr($authHeader, 0, 100) . "...");
    if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/i', $authHeader, $matches)) {
        //error_log("x Header 格式錯誤");
        errorResponse(1001, 401, 'unauthorized', 'Missing or malformed Authorization header.');
    }
    //error_log("o Header 格式正確");
    $jwt = $matches[1];
    $tokenParts = explode('.', $jwt);
    if (count($tokenParts) !== 3) {
        //error_log("x JWT 結構錯誤");
        errorResponse(1002, 401, 'invalid_token', 'Invalid token structure.');
    }
    //error_log("o JWT 結構正確");

    list($headerB64, $payloadB64, $sigB64) = $tokenParts;

    //檢查演算法
    $header = json_decode(base64UrlDecode($headerB64), true);
    if (!isset($header['alg']) || $header['alg'] !== 'RS256') {
        //error_log("x 演算法不是 RS256，是: " . ($header['alg'] ?? 'NULL'));
        errorResponse(1003, 401, 'invalid_token', 'Unsupported algorithm. Only RS256 is accepted.');
    }
    //error_log("o 演算法是 RS256");

    //用 Server 公鑰驗證簽章
    $publicKey = self::getServerPublicKey();
    $verifyResult = openssl_verify(
        $headerB64 . '.' . $payloadB64,
        base64UrlDecode($sigB64),
        $publicKey,
        OPENSSL_ALGO_SHA256
    );

    if ($verifyResult !== 1) {
        //error_log("x 簽章驗證失敗，結果: " . $verifyResult);
        errorResponse(1004, 401, 'invalid_token', 'Signature verification failed.');
    }
    //error_log("o 簽章驗證成功");

    //解開 Payload
    $payload = json_decode(base64UrlDecode($payloadB64), true);

    if (!$payload) {
        //error_log("x Payload 解析失敗");
        errorResponse(1005, 401, 'invalid_token', 'Invalid token payload.');
    }
    //error_log("o Payload 解析成功");

    //檢查 Token 類型
    if (isset($payload['type']) && $payload['type'] !== 'identity_token') {
        //error_log("x Token 類型錯誤: " . ($payload['type'] ?? 'NULL'));
        errorResponse(1006, 403, 'invalid_token', 'Token type mismatch. Identity token required.');
    }
    //error_log("o Token 類型正確");

    //檢查過期時間
    if (isset($payload['exp']) && time() >= $payload['exp']) {
        //error_log("x Token 已過期");
        errorResponse(1007, 401, 'token_expired', 'Token has expired.');
    }
    //error_log("o Token 未過期");

    //檢查 Issuer
//    if (isset($payload['iss']) && $payload['iss'] !== 'https://ttcdb.demosite.com.tw') {
        //error_log("x Issuer 錯誤: " . ($payload['iss'] ?? 'NULL'));
 //       errorResponse(1009, 401, 'invalid_token', 'Token issuer or token type is invalid.');
 //   }
	
	
	$domain = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
    $expectedIss = "https://{$domain}";

    if (isset($payload['iss']) && $payload['iss'] !== $expectedIss) {
        //error_log("x Issuer 錯誤: " . ($payload['iss'] ?? 'NULL') . "，預期: " . $expectedIss);
        errorResponse(1009, 401, 'invalid_token', 'Token issuer or token type is invalid.');
    }	
	
    //error_log("o Issuer 正確");
    return $payload;}
    private static function getServerPublicKey() {
        $publicKeyPath = __DIR__ . '/../../server_public.pem';
        if (!file_exists($publicKeyPath)) {
            errorResponse(1008, 500, 'server_error', 'Server public key not found.');
        }
        return file_get_contents($publicKeyPath);
    }
}
?>
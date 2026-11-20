<?php
// /audit/transaction/index.php - 交易履約存證寫入 (POST) 與歷來紀錄調閱 (GET)
// 支援路由: 
// GET  /audit/transaction/{id}[?start_date=YYYY-MM-DD[&end_date=YYYY-MM-DD]]
// POST /audit/transaction/{id} 或 POST /audit/transaction

// 1. Authorization Header 相容性處理 (避免 Apache 重寫遺失 Bearer Token)
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

// 2. 載入系統設定檔
$basePath = dirname(__DIR__, 3);
require_once '../../adm/config/config.php';
require_once '../../adm/config/getformin.php';
require_once '../../adm/config/createimg.php';
require_once '../../adm/config/share.php';
require_once '../../adm/config/did.php';
require_once '../../adm/config/Response.php';
require_once '../../adm/config/TokenAuth.php';

// 3. Header 與 CORS 設定
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// 4. 資料庫連線
$pdo = new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo->exec("SET NAMES ".$GLOBALS["db_encode"]);

// 5. 驗證身份憑證 Bearer Token
$payload = requireToken();
$callerDid = $payload['sub'] ?? ($payload['client_id'] ?? '');

$method = $_SERVER['REQUEST_METHOD'];
$transactionId = intval($_GET['id'] ?? 0);


// 🔍 [步驟 5 / 後續調閱] GET /audit/transaction/{id} : 調閱合約歷來履約存證

if ($method === 'GET') {
    if ($transactionId <= 0) {
        errorResponse(1052, 400, 'invalid_request', 'Missing or invalid contract ID.');
    }

    // 1. 檢查 gaia_contracts 合約表：確認該合約是否存在，並取得雙方組織 ID
    $contractStmt = $pdo->prepare("
        SELECT thisid, provider_org_id, applicant_org_id, status 
        FROM gaia_contracts 
        WHERE thisid = ?
    ");
    $contractStmt->execute([$transactionId]);
    $contract = $contractStmt->fetch(PDO::FETCH_ASSOC);

    if (!$contract) {
        errorResponse(1054, 404, 'contract_not_found', 'The specified contract does not exist in gaia_contracts.');
    }

// 2. 從 callerDid 解析出機器帳號 ID (例如 did:web:...:connector:100013 -> 100013)
    $machineId = 0;
    if (preg_match('/:connector:(\d+)$/', $callerDid, $matches)) {
        $machineId = intval($matches[1]);
    }

    $callerOwnerId = 0;
    if ($machineId > 0) {
        // 3. 直接以 machine_accounts 的 thisid 進行查詢
        $machineStmt = $pdo->prepare("
            SELECT owner_id 
            FROM machine_accounts 
            WHERE thisid = ? 
              AND isopen = 1 
            LIMIT 1
        ");
        $machineStmt->execute([$machineId]);
        $machine = $machineStmt->fetch(PDO::FETCH_ASSOC);

        if ($machine) {
            $callerOwnerId = intval($machine['owner_id']);
        }
    }

    // 4. 驗證機器帳號的 owner_id 是否為該合約的 provider_org_id 或 applicant_org_id
    $providerOrgId = intval($contract['provider_org_id']);
    $applicantOrgId = intval($contract['applicant_org_id']);

	if ($callerOwnerId <= 0 || ($callerOwnerId !== $providerOrgId && $callerOwnerId !== $applicantOrgId)) {
        errorResponse(1055, 403, 'unauthorized_access', 'Machine account owner is neither the provider nor the applicant of this contract.');
    }

    // 4. 解析查詢參數
    $startDate = trim($_GET['start_date'] ?? '');
    $endDate = trim($_GET['end_date'] ?? '');

    // 如果完全沒帶日期，預設抓今天
    if (empty($startDate) && empty($endDate)) {
        $startDate = date('Y-m-d');
        $endDate = $startDate;
    }

    
    // 情況 A：無 end_date、或 start_date 與 end_date 相同 -> 回傳單日詳細紀錄
    
    if (empty($endDate) || $startDate === $endDate) {
        if (empty($startDate)) {
            $startDate = $endDate;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
            errorResponse(1053, 400, 'invalid_date', 'Invalid date format. Use YYYY-MM-DD.');
        }

        $stmt = $pdo->prepare("
            SELECT 
                thisid AS clearing_log_id,
                contract_id,
                jti,
                reporter_type,
                reporter_did,
                event_type,
                bytes_transferred,
                payload_hash,
                prev_log_hash,
                current_log_hash,
                http_status_code,
                transfer_time,
                dateadd
            FROM gaia_clearing_logs 
            WHERE contract_id = ?
              AND DATE(dateadd) = ?
            ORDER BY thisid DESC
        ");
        $stmt->execute([$transactionId, $startDate]);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 檢查此 ID 最新紀錄的雙向 Checksum 比對狀態
        $latestJti = $logs[0]['jti'] ?? '';
        $verificationStatus = 'PENDING_COUNTERPARTY';
        $checksumMatched = null;

        if (!empty($latestJti)) {
            $checkStmt = $pdo->prepare("
                SELECT reporter_type, payload_hash 
                FROM gaia_clearing_logs 
                WHERE jti = ? 
                ORDER BY thisid ASC
            ");
            $checkStmt->execute([$latestJti]);
            $jtiLogs = $checkStmt->fetchAll(PDO::FETCH_ASSOC);

            $consumerHash = null;
            $providerHash = null;

            foreach ($jtiLogs as $l) {
                if ($l['reporter_type'] === 'CONSUMER') {
                    $consumerHash = strtolower($l['payload_hash']);
                } elseif ($l['reporter_type'] === 'PROVIDER') {
                    $providerHash = strtolower($l['payload_hash']);
                }
            }

            if ($consumerHash !== null && $providerHash !== null) {
                if (hash_equals($consumerHash, $providerHash)) {
                    $verificationStatus = 'MATCHED';
                    $checksumMatched = true;
                } else {
                    $verificationStatus = 'DISCREPANCY';
                    $checksumMatched = false;
                }
            }
        }

        successResponse([
            'mode'                => 'detailed',
            'date'                => $startDate,
            'contract_id'         => $transactionId,
            'contract_status'     => intval($contract['status']),
            'total_logs'          => count($logs),
            'verification_status' => $verificationStatus,
            'checksum_matched'    => $checksumMatched,
            'logs'                => $logs
        ]);
    } 
    
    // 情況 B：start_date 與 end_date 不同（跨日區間） -> 回傳區間每日統計數
    
else {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            errorResponse(1053, 400, 'invalid_date', 'Invalid date format. Use YYYY-MM-DD.');
        }

        $stmt = $pdo->prepare("
            SELECT 
                DATE(dateadd) AS log_date,
                -- 1. 全局總計
                COUNT(*) AS total_transactions,
                SUM(bytes_transferred) AS total_bytes,
                
                -- 2. 消費者 (CONSUMER) 視角統計
                SUM(CASE WHEN reporter_type = 'CONSUMER' THEN 1 ELSE 0 END) AS consumer_total,
                SUM(CASE WHEN reporter_type = 'CONSUMER' AND http_status_code = 200 THEN 1 ELSE 0 END) AS consumer_success_count,
                SUM(CASE WHEN reporter_type = 'CONSUMER' THEN bytes_transferred ELSE 0 END) AS consumer_bytes,

                -- 3. 提供者 (PROVIDER) 視角統計
                SUM(CASE WHEN reporter_type = 'PROVIDER' THEN 1 ELSE 0 END) AS provider_total,
                SUM(CASE WHEN reporter_type = 'PROVIDER' AND http_status_code = 200 THEN 1 ELSE 0 END) AS provider_success_count,
                SUM(CASE WHEN reporter_type = 'PROVIDER' THEN bytes_transferred ELSE 0 END) AS provider_bytes
            FROM gaia_clearing_logs
            WHERE contract_id = ?
              AND DATE(dateadd) BETWEEN ? AND ?
            GROUP BY DATE(dateadd)
            ORDER BY log_date ASC
        ");
        $stmt->execute([$transactionId, $startDate, $endDate]);
        $rawSummary = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 整理每日資料，並比對兩端數量是否一致
        $summary = [];
        foreach ($rawSummary as $row) {
            $consumerTotal = intval($row['consumer_total']);
            $providerTotal = intval($row['provider_total']);

            // 比對兩端紀錄比數是否吻合
            if ($consumerTotal === $providerTotal) {
                $reconciliationStatus = 'MATCHED';
            } elseif ($consumerTotal === 0 || $providerTotal === 0) {
                $reconciliationStatus = 'PENDING_COUNTERPARTY';
            } else {
                $reconciliationStatus = 'DISCREPANCY';
            }

            $summary[] = [
                'log_date'                 => $row['log_date'],
                'total_transactions'       => intval($row['total_transactions']),
                'total_bytes'              => intval($row['total_bytes']),
                'reconciliation_status'    => $reconciliationStatus, // MATCHED, DISCREPANCY, PENDING_COUNTERPARTY
                'consumer' => [
                    'total_transactions'   => $consumerTotal,
                    'success_count'        => intval($row['consumer_success_count']),
                    'total_bytes'          => intval($row['consumer_bytes'])
                ],
                'provider' => [
                    'total_transactions'   => $providerTotal,
                    'success_count'        => intval($row['provider_success_count']),
                    'total_bytes'          => intval($row['provider_bytes'])
                ]
            ];
        }

        successResponse([
            'mode'            => 'summary',
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'contract_id'     => $transactionId,
            'contract_status' => intval($contract['status']),
            'total_days'      => count($summary),
            'daily_summary'   => $summary
        ]);
    }
}


// 📝 [步驟 4] POST /audit/transaction/{id} : 寫入交易履約存證

if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?? $_POST;

    $contractId       = $transactionId > 0 ? $transactionId : intval($input['contract_id'] ?? ($input['agreement_id'] ?? 0));
    $jti              = trim($input['jti'] ?? '');
    $reporterType     = strtoupper(trim($input['reporter_type'] ?? ''));
    $reporterDid      = trim($input['reporter_did'] ?? ($input['provider_sub'] ?? ($input['consumer_sub'] ?? '')));
    $eventType        = trim($input['event_type'] ?? '');
    $bytesTransferred = intval($input['bytes_transferred'] ?? ($input['bytes_received'] ?? 0));
    $payloadHash      = strtolower(trim($input['payload_hash'] ?? ''));
    $httpStatusCode   = intval($input['http_status_code'] ?? 200);
    $transferTime     = floatval($input['transfer_time'] ?? 0);

    // 1. 基本參數驗證
    if ($contractId <= 0 || empty($jti) || empty($reporterDid) || empty($payloadHash)) {
        errorResponse(1025, 400, 'invalid_request', 'Missing required parameters: contract_id, jti, reporter_did, payload_hash.');
    }

    // 檢查寫入的合約是否存在於 gaia_contracts
    $contractCheck = $pdo->prepare("SELECT thisid, provider_org_id, applicant_org_id FROM gaia_contracts WHERE thisid = ?");
    $contractCheck->execute([$contractId]);
    $auditContract = $contractCheck->fetch(PDO::FETCH_ASSOC);
    if (!$auditContract) {
        errorResponse(1028, 404, 'contract_not_found', 'Cannot log audit for a non-existent contract ID.');
    }

    if (!in_array($reporterType, ['CONSUMER', 'PROVIDER'])) {
        errorResponse(1026, 400, 'invalid_reporter_type', 'reporter_type must be CONSUMER or PROVIDER.');
    }

    if (!preg_match('/^[a-fA-F0-9]{64}$/', $payloadHash)) {
        errorResponse(1027, 400, 'invalid_payload_hash', 'payload_hash must be a valid 64-character SHA-256 string.');
    }

    $callerOrgId = (int)getOrgIdFromToken($payload);
    $expectedOrg = ($reporterType === 'PROVIDER') ? (int)$auditContract['provider_org_id'] : (int)$auditContract['applicant_org_id'];
    if ($callerOrgId <= 0 || $callerOrgId !== $expectedOrg || $reporterDid !== $callerDid) {
        errorResponse(1055, 403, 'unauthorized_audit_report', 'Reporter identity or role does not match the contract.');
    }

    if (empty($eventType)) {
        $eventType = ($reporterType === 'PROVIDER') ? 'DATA_DELIVERY_COMPLETED' : 'DATA_RECEIPT_COMPLETED';
    }

    // 2. 取最新一筆 current_log_hash 作為本筆 prev_log_hash
    $lastStmt = $pdo->query("SELECT current_log_hash FROM gaia_clearing_logs ORDER BY thisid DESC LIMIT 1");
    $lastRecord = $lastStmt->fetch(PDO::FETCH_ASSOC);
    $prevLogHash = ($lastRecord && !empty($lastRecord['current_log_hash'])) ? $lastRecord['current_log_hash'] : str_repeat('0', 64);

    // 3. 壓製 Hash Chain
    $rawLogString   = $jti . $reporterDid . $payloadHash . $prevLogHash;
    $currentLogHash = hash('sha256', $rawLogString);

    // 4. 寫入資料庫
    try {
        $insertStmt = $pdo->prepare("
            INSERT INTO gaia_clearing_logs (
                contract_id, jti, reporter_type, reporter_did, 
                event_type, bytes_transferred, payload_hash, prev_log_hash, 
                current_log_hash, http_status_code, transfer_time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $insertStmt->execute([
            $contractId,
            $jti,
            $reporterType,
            $reporterDid,
            $eventType,
            $bytesTransferred,
            $payloadHash,
            $prevLogHash,
            $currentLogHash,
            $httpStatusCode,
            $transferTime
        ]);

        $insertedId = $pdo->lastInsertId();

        // 5. 雙向 Checksum 即時自動比對
        $counterpartyType = ($reporterType === 'CONSUMER') ? 'PROVIDER' : 'CONSUMER';
        $checkStmt = $pdo->prepare("
            SELECT payload_hash FROM gaia_clearing_logs 
            WHERE jti = ? AND reporter_type = ? 
            ORDER BY thisid DESC LIMIT 1
        ");
        $checkStmt->execute([$jti, $counterpartyType]);
        $counterpartyLog = $checkStmt->fetch(PDO::FETCH_ASSOC);

        $verificationStatus = 'PENDING_COUNTERPARTY';
        $checksumMatched = null;

        if ($counterpartyLog) {
            if (hash_equals($payloadHash, strtolower($counterpartyLog['payload_hash']))) {
                $verificationStatus = 'MATCHED';
                $checksumMatched = true;
            } else {
                $verificationStatus = 'DISCREPANCY';
                $checksumMatched = false;
            }
        }

        // 6. 回傳標準 Response
        successResponse([
            'clearing_log_id'     => intval($insertedId),
            'contract_id'         => $contractId,
            'jti'                 => $jti,
            'reporter_type'       => $reporterType,
            'prev_log_hash'       => $prevLogHash,
            'current_log_hash'    => $currentLogHash,
            'verification_status' => $verificationStatus,
            'checksum_matched'    => $checksumMatched
        ]);

    } catch (PDOException $e) {
        errorResponse(1062, 500, 'server_error', 'Failed to log transaction: ' . $e->getMessage());
    }
}

errorResponse(1050, 405, 'method_not_allowed', 'Method not allowed.');
?>
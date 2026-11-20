<?php
// EDC integration API: existing platform identity authentication and contract ownership apply.
require_once dirname(__DIR__) . '/adm/config/config.php';
require_once dirname(__DIR__) . '/adm/config/share.php';
require_once dirname(__DIR__) . '/adm/config/Response.php';
require_once dirname(__DIR__) . '/adm/config/TokenAuth.php';
$pdo = new PDO('mysql:host='.$dbhost.';dbname='.$dbname.';charset=utf8mb4', $dbuser, $dbpass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$payload = requireToken();
$org = (int)getOrgIdFromToken($payload);
$input = json_decode(file_get_contents('php://input'), true) ?: [];
$id = (int)($_GET['contract_id'] ?? $input['contract_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM gaia_contracts WHERE thisid = ?');
$stmt->execute([$id]); $contract = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$contract || $org <= 0 || !in_array($org, [(int)$contract['provider_org_id'],(int)$contract['applicant_org_id']], true)) {
    errorResponse(1055,403,'forbidden','Caller is not a party to the contract.');
}
$action = $_GET['action'] ?? 'eligibility';
if ($action === 'eligibility' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $start = strtotime($contract['effective_at']); $end = strtotime($contract['expired_at']);
    successResponse([
      'active'=>(int)$contract['status']===1 && $start <= time() && $end > time(),
      'contract_id'=>(int)$contract['thisid'], 'product_id'=>(int)$contract['product_id'],
      'version_id'=>(int)$contract['version_id'], 'provider_org_id'=>(int)$contract['provider_org_id'],
      'applicant_org_id'=>(int)$contract['applicant_org_id'], 'effective_at_epoch'=>$start, 'expired_at_epoch'=>$end,
      'allowed_actions'=>json_decode($contract['allowed_actions'] ?: '[]',true),
      'constraints'=>json_decode($contract['constraints'] ?: '{}',true),
      'policy_hash'=>hash('sha256',$contract['odrl_agreement_json'])
    ]);
}
if ($action === 'exchange_reports' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $exchange=(string)($_GET['exchange_id'] ?? '');
    if (!preg_match('/^[a-f0-9-]{36}$/',$exchange)) errorResponse(1025,400,'invalid_request','Invalid exchange identifier.');
    $s=$pdo->prepare('SELECT exchange_id,reporter_type,edc_transfer_process_id,edc_agreement_id,payload_hash,bytes_transferred,created_at FROM gaia_edc_exchange_reports WHERE contract_id=? AND exchange_id=? ORDER BY reporter_type');
    $s->execute([$id,$exchange]);
    successResponse(['contract_id'=>$id,'reports'=>$s->fetchAll(PDO::FETCH_ASSOC)]);
}
if ($action === 'audit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $exchange = (string)($input['exchange_id'] ?? ''); $role = (string)($input['reporter_type'] ?? '');
    $hash = strtolower((string)($input['payload_hash'] ?? '')); $did = (string)($payload['sub'] ?? '');
    $transfer = (string)($input['edc_transfer_process_id'] ?? '');
    $agreement = (string)($input['edc_agreement_id'] ?? '');
    $size = $input['bytes_transferred'] ?? null;
    $expected = $role === 'PROVIDER' ? (int)$contract['provider_org_id'] : ($role === 'CONSUMER' ? (int)$contract['applicant_org_id'] : 0);
    if (!$expected || $org !== $expected || !preg_match('/^[a-f0-9-]{36}$/',$exchange) || !preg_match('/^[a-f0-9]{64}$/',$hash) || !is_int($size) || $size < 0 || strlen($transfer)<1 || strlen($transfer)>128 || strlen($agreement)<1 || strlen($agreement)>128) {
      errorResponse(1025,400,'invalid_report','Invalid or unauthorized exchange report.');
    }
    $pdo->beginTransaction();
    try {
      $s=$pdo->prepare('INSERT INTO gaia_edc_exchange_reports (contract_id,exchange_id,reporter_type,reporter_did,edc_transfer_process_id,edc_agreement_id,payload_hash,bytes_transferred) VALUES (?,?,?,?,?,?,?,?)');
      $s->execute([$id,$exchange,$role,$did,$transfer,$agreement,$hash,$size]);
      $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();
      if ($e->getCode() !== '23000') errorResponse(1062,500,'server_error','Could not record exchange.');
      $s=$pdo->prepare('SELECT * FROM gaia_edc_exchange_reports WHERE contract_id=? AND exchange_id=? AND reporter_type=?');
      $s->execute([$id,$exchange,$role]);$old=$s->fetch(PDO::FETCH_ASSOC);
      if (!$old || !hash_equals($old['payload_hash'],$hash) || (int)$old['bytes_transferred']!==$size || $old['reporter_did']!==$did || $old['edc_transfer_process_id']!==$transfer || $old['edc_agreement_id']!==$agreement) {
        errorResponse(1063,409,'conflicting_report','Exchange report conflicts with the stored report.');
      }
    }
    $s=$pdo->prepare('SELECT reporter_type,payload_hash,bytes_transferred,edc_transfer_process_id,edc_agreement_id FROM gaia_edc_exchange_reports WHERE contract_id=? AND exchange_id=?');
    $s->execute([$id,$exchange]);$rows=$s->fetchAll(PDO::FETCH_ASSOC);
    $state='PENDING_COUNTERPARTY';
    if(count($rows)===2) $state=hash_equals($rows[0]['payload_hash'],$rows[1]['payload_hash']) && (int)$rows[0]['bytes_transferred']===(int)$rows[1]['bytes_transferred'] && $rows[0]['edc_transfer_process_id']===$rows[1]['edc_transfer_process_id'] && $rows[0]['edc_agreement_id']===$rows[1]['edc_agreement_id'] ? 'MATCHED':'DISCREPANCY';
    successResponse(['contract_id'=>$id,'exchange_id'=>$exchange,'verification_status'=>$state]);
}
errorResponse(1050,405,'method_not_allowed','Unsupported integration action or method.');

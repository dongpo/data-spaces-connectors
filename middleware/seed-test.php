<?php
// Isolated integration fixtures only. No imported production users or tokens.
$pdo=new PDO('mysql:host=mysql;dbname=ttcdb;charset=utf8mb4', 'ttc', getenv('TTC_DB_PASSWORD'), [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec("SET time_zone='+00:00'");
foreach([[1,'TMS Provider Demo','TMS-DEMO'],[2,'Consumer Demo','CONSUMER-DEMO']] as $row){
 $s=$pdo->prepare('INSERT INTO res_entities (thisid,thisname,thiscode,isopen) VALUES (?,?,?,1) ON DUPLICATE KEY UPDATE isopen=1');$s->execute($row);
}
foreach([[11,'Provider EDC',1,'http://localhost:19194/protocol','/tmp/provider.pub'],[22,'Consumer EDC',2,'http://localhost:29194/protocol','/tmp/consumer.pub']] as $m){
 $s=$pdo->prepare('INSERT INTO machine_accounts (thisid,thisname,owner_id,machine_url,thispk,isopen) VALUES (?,?,?,?,?,1) ON DUPLICATE KEY UPDATE thispk=VALUES(thispk),isopen=1');$s->execute([$m[0],$m[1],$m[2],$m[3],file_get_contents($m[4])]);
}
$pdo->exec("INSERT INTO gaia_data_products (thisid,resource_id,resource_name,owner_id,current_version_id,current_version,isopen) VALUES (101,'00000000-0000-4000-8000-000000000101','TMS Integration Test',1,201,'v1',1) ON DUPLICATE KEY UPDATE isopen=1");
$policy=json_encode(['@type'=>'Agreement','permission'=>[['action'=>'use']]]);
$s=$pdo->prepare("INSERT INTO gaia_product_versions (thisid,product_id,owner_id,display_name,industry_id,description,version,contract_duration_days,allowed_actions,prohibited_actions,constraints,data_sensitivity_level,update_frequency,connector_id,published_by,isopen,odrl_policy_json,odrl_policy_simple_json) VALUES (201,101,1,'TMS Test',1,'Isolated fixture','v1',7,'[\"query\"]','[]','{}',4,'PT1M',11,1,2,?,?) ON DUPLICATE KEY UPDATE isopen=2");$s->execute([$policy,$policy]);
$pdo->exec("INSERT INTO gaia_subscriptions (thisid,product_id,version_id,applicant_org_id,provider_org_id,applied_actions,applied_constraints,status,approved_at) VALUES (301,101,201,2,1,'[\"query\"]','{}',1,NOW()) ON DUPLICATE KEY UPDATE status=1");
$s=$pdo->prepare("INSERT INTO gaia_contracts (thisid,application_id,product_id,version_id,provider_org_id,applicant_org_id,contract_duration_days,effective_at,expired_at,allowed_actions,constraints,prohibited_actions,version_snapshot,application_snapshot,odrl_agreement_json,status) VALUES (401,301,101,201,1,2,7,DATE_SUB(NOW(),INTERVAL 1 MINUTE),DATE_ADD(NOW(),INTERVAL 7 DAY),'[\"query\"]','{}','[]','{}','{}',?,1) ON DUPLICATE KEY UPDATE status=1,expired_at=DATE_ADD(NOW(),INTERVAL 7 DAY)");$s->execute([$policy]);
echo "Test fixtures seeded: provider 1, consumer 2, product 101/version 201, approved contract 401\n";

"""Create an isolated, sanitized TTC middleware copy; never change the handover."""
from pathlib import Path
import hashlib,json,re,shutil,sys
root=Path(sys.argv[1]).resolve()
dest=Path(__file__).resolve().parents[1]/'middleware'/'source'
if dest.exists(): raise SystemExit('Refusing to overwrite existing prepared middleware')
original=root/'ttcdb原始碼'
shutil.copytree(original,dest,ignore=shutil.ignore_patterns('*.key','*.pem','*.pfx','*.p12','uploadfile','.git'))
(dest/'uploadfile').mkdir()
config=dest/'adm/config/config.php'
s=config.read_text()
for var,env,default in [('dbhost','TTC_DB_HOST','mysql'),('dbname','TTC_DB_NAME','ttcdb'),('dbuser','TTC_DB_USER','ttc'),('dbpass','TTC_DB_PASSWORD',''),('frommail','TTC_SMTP_USER',''),('frompass','TTC_SMTP_PASSWORD','')]:
 s,count=re.subn(r'\$'+var+r'\s*=\s*[^;]*;',lambda _:f"${var} = getenv('{env}') ?: '{default}';",s)
 if count!=1: raise SystemExit(f'Unexpected config assignment count: {var}')
for name in ['SMS2_name','SMS2_code']:
 s=re.sub(r'define\(\s*[\'\"]'+name+r'[\'\"]\s*,[^;]*;',f'define("{name}", "");',s)
config.write_text(s)
manifest=[]
def change(name,old,new):
 p=dest/name;s=p.read_text()
 if s.count(old)!=1: raise SystemExit(f'Unexpected patch context: {name}: {old[:45]}')
 before=hashlib.sha256(s.encode()).hexdigest();s=s.replace(old,new);p.write_text(s)
 manifest.append({'file':name,'before':before,'after':hashlib.sha256(s.encode()).hexdigest()})
change('auth/verify.php',"($payload['type'] ?? '') !== 'contract_token'","($payload['type'] ?? '') !== 'transaction_token'")
change('auth/verify.php','Expected contract_token.','Expected transaction_token.')
change('auth/verify.php','SELECT * FROM gaia_contracts WHERE thisid = ? AND isopen = 1 LIMIT 1','SELECT * FROM gaia_contracts WHERE thisid = ? AND status = 1 AND effective_at <= NOW() AND expired_at > NOW() LIMIT 1')
needle='// 8. 資料庫查驗合約狀態 (gaia_contracts)'
extra="""// Bind the signed token to the stored issuance record.
if ((int)$tokenRecord['contract_id'] !== $agreementId ||
    strtotime($tokenRecord['effective_at']) > $now || strtotime($tokenRecord['expires_at']) <= $now) {
    errorResponse(1059, 401, 'invalid_grant', 'Token record does not match or is outside its valid period.');
}
"""
change('auth/verify.php',needle,extra+'\n'+needle)
needle='// 9. 驗證成功，回傳 Token 載荷資訊 (Payload)'
extra="""if ((int)($payload['dataset_id'] ?? 0) !== (int)$contract['product_id'] ||
    (string)($payload['aud'] ?? '') !== (string)$contract['provider_org_id']) {
    errorResponse(1059, 403, 'access_denied', 'Token resource or provider does not match its contract.');
}
"""
change('auth/verify.php',needle,extra+'\n'+needle)
change('audit/transaction/index.php',"SELECT thisid FROM gaia_contracts WHERE thisid = ?","SELECT thisid, provider_org_id, applicant_org_id FROM gaia_contracts WHERE thisid = ?")
change('audit/transaction/index.php',"if (!$contractCheck->fetch()) {","$auditContract = $contractCheck->fetch(PDO::FETCH_ASSOC);\n    if (!$auditContract) {")
needle="    if (empty($eventType)) {"
extra="""    $callerOrgId = (int)getOrgIdFromToken($payload);
    $expectedOrg = ($reporterType === 'PROVIDER') ? (int)$auditContract['provider_org_id'] : (int)$auditContract['applicant_org_id'];
    if ($callerOrgId <= 0 || $callerOrgId !== $expectedOrg || $reporterDid !== $callerDid) {
        errorResponse(1055, 403, 'unauthorized_audit_report', 'Reporter identity or role does not match the contract.');
    }
"""
change('audit/transaction/index.php',needle,extra+'\n'+needle)
# A dedicated exchange API supplies per-response identity and idempotence; existing API auth fix is independent.
(dest/'audit/clearing/log.php').write_text("<?php\n// Canonical audited implementation; removes divergent authorization paths.\nrequire dirname(__DIR__) . '/transaction/index.php';\n")
# Copy SQL schema only. No user accounts, hashes, tokens or private data rows are exported.
sql=(root/'ttcdb資料庫.sql').read_text()
from extract_schema import extract_schema
statements=extract_schema(sql)
if not statements: raise SystemExit('No DDL found')
(dest.parent/'schema-only.sql').write_text('SET NAMES utf8mb4;\n'+ '\n\n'.join(statements)+'\n')
(dest.parent/'patch-manifest.json').write_text(json.dumps(manifest,ensure_ascii=False,indent=2)+'\n')
print(f'Prepared sanitized middleware; {len(manifest)} guarded replacements; {len(statements)} DDL statements; original unchanged.')

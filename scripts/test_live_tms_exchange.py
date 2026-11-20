"""Real TMS API through native EDC + TTC contract gate and matched audit."""
from ttc_test_client import call
import copy,hashlib,json,os,threading,time,urllib.request,urllib.error,uuid,subprocess
from http.server import BaseHTTPRequestHandler,ThreadingHTTPServer
from pathlib import Path
NS='https://w3id.org/edc/v0.0.1/ns/'
CTX={'@vocab':NS,'odrl':'http://www.w3.org/ns/odrl/2/','dcat':'http://www.w3.org/ns/dcat#','dct':'http://purl.org/dc/terms/'}
PROVIDER='http://localhost:19193/management';CONSUMER='http://localhost:29193/management';DSP='http://localhost:19194/protocol'
KEY=os.environ.get('EDC_MANAGEMENT_KEY','REPLACE_WITH_LOCAL_MANAGEMENT_KEY')
PAYLOAD=b'[{"source":"MOCK_TMS","incidentId":"demo-001","description":"integration fixture"}]'
class Source(BaseHTTPRequestHandler):
 def do_GET(self):
  self.send_response(200);self.send_header('Content-Type','application/json');self.end_headers();self.wfile.write(PAYLOAD)
 def log_message(self,*args): pass

def request(base,path,payload=None):
 headers={'X-Api-Key':KEY,'Content-Type':'application/json'}
 req=urllib.request.Request(base+path,data=None if payload is None else json.dumps(payload).encode(),headers=headers)
 try:
  with urllib.request.urlopen(req,timeout=20) as r:return json.load(r)
 except urllib.error.HTTPError as e:raise RuntimeError(f'{path}: HTTP {e.code}: {e.read().decode()[:1500]}') from None

def field(o,name,default=None):
 for k,v in o.items():
  if k==name or k.endswith(':'+name) or k.endswith('#'+name) or k.endswith('/'+name):return v
 return default

def wait(path,expected):
 for _ in range(90):
  obj=request(CONSUMER,path);state=field(obj,'state')
  if state==expected:return obj
  if state in ['TERMINATED','ERROR']: raise RuntimeError(f'Unexpected terminal state: {obj}')
  time.sleep(1)
 raise RuntimeError(f'Timed out waiting for {expected}; last response: {obj}')

def main():
 suffix=uuid.uuid4().hex[:10];asset='live-tms-'+suffix;policy='demo-policy-'+suffix;definition='demo-contract-'+suffix
 request(PROVIDER,'/v3/assets',{'@context':CTX,'@id':asset,'properties':{'name':'TMS live traffic incidents'},'dataAddress':{'https://geomni.tw/ttc/contractId':'401','https://geomni.tw/ttc/productId':'101','https://geomni.tw/ttc/versionId':'201','https://geomni.tw/ttc/providerOrg':'1','https://geomni.tw/ttc/consumerOrg':'2','type':'HttpData','baseUrl':'http://www.1968.com.tw/TMSEventService/TrafficIncident.ashx?type=json','authKey':'Cookie','secretName':'tms-upstream-cookie','method':'GET'}})
 request(PROVIDER,'/v3/policydefinitions',{'@context':CTX,'@id':policy,'@type':'PolicyDefinition','policy':{'@context':'http://www.w3.org/ns/odrl.jsonld','@type':'Set'}})
 contract_policy=policy+'-ttc'
 request(PROVIDER,'/v3/policydefinitions',{'@context':CTX,'@id':contract_policy,'@type':'PolicyDefinition','policy':{'@context':'http://www.w3.org/ns/odrl.jsonld','@type':'Set','permission':[{'action':'use','constraint':[{'leftOperand':'https://geomni.tw/ttc/approvedContract','operator':'eq','rightOperand':'401:101:201'}]}]}})
 request(PROVIDER,'/v3/contractdefinitions',{'@context':CTX,'@id':definition,'@type':'ContractDefinition','accessPolicyId':policy,'contractPolicyId':contract_policy,'assetsSelector':[{'operandLeft':NS+'id','operator':'=','operandRight':asset}]})
 catalog=request(CONSUMER,'/v3/catalog/request',{'@context':CTX,'@type':'CatalogRequest','counterPartyId':'urn:connector:provider','counterPartyAddress':DSP,'protocol':'dataspace-protocol-http'})
 datasets=field(catalog,'dataset',[]);datasets=datasets if isinstance(datasets,list) else [datasets]
 dataset=next(d for d in datasets if d.get('@id')==asset)
 offer=field(dataset,'hasPolicy');offer=offer[0] if isinstance(offer,list) else offer
 offer=copy.deepcopy(offer);offer['@context']=catalog.get('@context',CTX);offer['odrl:assigner']={'@id':'urn:connector:provider'};offer['odrl:target']={'@id':asset}
 negotiation=request(CONSUMER,'/v3/contractnegotiations',{'@context':CTX,'@type':'ContractRequest','counterPartyAddress':DSP,'protocol':'dataspace-protocol-http','policy':offer})['@id']
 wait('/v3/contractnegotiations/'+negotiation+'/state','FINALIZED')
 agreement=request(CONSUMER,'/v3/contractnegotiations/'+negotiation+'/agreement')['@id']
 transfer=request(CONSUMER,'/v3/transferprocesses',{'@context':CTX,'@type':'TransferRequest','protocol':'dataspace-protocol-http','contractId':agreement,'connectorId':'urn:connector:provider','counterPartyAddress':DSP,'transferType':'HttpData-PULL'})['@id']
 wait('/v3/transferprocesses/'+transfer+'/state','STARTED')
 for _ in range(30):
  try:edr=request(CONSUMER,'/v3/edrs/'+transfer+'/dataaddress');break
  except RuntimeError:time.sleep(1)
 else:raise RuntimeError('EDR was not received')
 endpoint=field(edr,'endpoint');authorization=field(edr,'authorization')
 req=urllib.request.Request(endpoint,headers={'Authorization':authorization})
 with urllib.request.urlopen(req,timeout=20) as r:body=r.read();exchange=r.headers.get('X-Exchange-ID');provider_process=r.headers.get('X-EDC-Process-ID');provider_agreement=r.headers.get('X-EDC-Agreement-ID')
 parsed=json.loads(body)
 assert isinstance(parsed,list) and parsed and all(isinstance(x,dict) and {'TMSEventID','EventType','HappenedTime'}.issubset(x) for x in parsed),'Upstream did not return the expected TMS incident schema'
 try:
  urllib.request.urlopen(urllib.request.Request(endpoint,headers={'Authorization':'invalid'}),timeout=10)
 except urllib.error.HTTPError as e:
  assert e.code in (401,403),f'Unexpected unauthorized status {e.code}'
 else:raise AssertionError('Invalid EDR token accepted')
 assert exchange and provider_process and provider_agreement,'Missing trusted exchange headers'
 report={'contract_id':401,'exchange_id':exchange,'reporter_type':'CONSUMER','edc_transfer_process_id':provider_process,'edc_agreement_id':provider_agreement,'payload_hash':hashlib.sha256(body).hexdigest(),'bytes_transferred':len(body)}
 for _ in range(10):
  audit=call('consumer','audit',report)
  if audit['verification_status']=='MATCHED':break
  time.sleep(1)
 assert audit['verification_status']=='MATCHED',audit
 assert call('consumer','audit',report)['verification_status']=='MATCHED','Idempotent retry failed'
 conflict=dict(report,payload_hash='0'*64)
 try:call('consumer','audit',conflict)
 except urllib.error.HTTPError as e:assert e.code==409
 else:raise AssertionError('Conflicting duplicate accepted')
 def contract_status(status):
  env=dict(line.split('=',1) for line in Path('secrets/db.env').read_text().splitlines())
  subprocess.run(['docker','exec','-e','MYSQL_PWD='+env['MYSQL_ROOT_PASSWORD'],'ttc-demo-mysql','mysql','-uroot','ttcdb','-e',f'UPDATE gaia_contracts SET status={status} WHERE thisid=401'],check=True,capture_output=True)
 try:
  contract_status(3)
  assert not call('consumer','eligibility')['active']
  try:urllib.request.urlopen(req,timeout=20)
  except urllib.error.HTTPError as e:assert e.code==403,f'Suspended contract returned {e.code}'
  else:raise AssertionError('Existing EDR bypassed contract suspension')
  try:
   denied=request(CONSUMER,'/v3/contractnegotiations',{'@context':CTX,'@type':'ContractRequest','counterPartyAddress':DSP,'protocol':'dataspace-protocol-http','policy':offer})['@id']
  except RuntimeError as error:
   assert 'HTTP 400' in str(error) or 'HTTP 403' in str(error),str(error)
  else:
   for _ in range(45):
    state=field(request(CONSUMER,'/v3/contractnegotiations/'+denied+'/state'),'state')
    if state=='TERMINATED':break
    assert state!='FINALIZED','Suspended contract negotiated successfully'
    time.sleep(1)
   else:raise AssertionError('Suspended negotiation was not terminated')
 finally:contract_status(1)
 wrong_role=dict(report,reporter_type='PROVIDER')
 try:call('consumer','audit',wrong_role)
 except urllib.error.HTTPError as e:assert e.code in (400,403)
 else:raise AssertionError('Consumer impersonated provider audit')
 result={'completed_at_utc':time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime()),'suspended_negotiation_rejected':True,'suspended_existing_edr_rejected':True,'reporter_impersonation_rejected':True,'exchange_id':exchange,'matched_audit':True,'duplicate_idempotent':True,'conflict_rejected':True,'record_count':len(parsed),'upstream':'LIVE_TMS','edc_version':'0.14.1','asset_id':asset,'negotiation_id':negotiation,'agreement_id':agreement,'transfer_process_id':transfer,'bytes':len(body),'sha256':hashlib.sha256(body).hexdigest(),'invalid_token_rejected':True,'status':'PASS'}
 Path('logs/live-tms-exchange-result.json').write_text(json.dumps(result,indent=2)+'\n')
 print(json.dumps(result,indent=2))
if __name__=='__main__':main()

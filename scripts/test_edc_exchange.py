"""Integration driver against real EDC runtimes. The upstream here is explicitly MOCK data."""
import copy,hashlib,json,os,threading,time,urllib.request,urllib.error,uuid
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
 upstream=ThreadingHTTPServer(('127.0.0.1',18180),Source)
 threading.Thread(target=upstream.serve_forever,daemon=True).start()
 suffix=uuid.uuid4().hex[:10];asset='mock-tms-'+suffix;policy='demo-policy-'+suffix;definition='demo-contract-'+suffix
 request(PROVIDER,'/v3/assets',{'@context':CTX,'@id':asset,'properties':{'name':'Mock TMS test fixture'},'dataAddress':{'type':'HttpData','baseUrl':'http://localhost:18180/incidents','method':'GET'}})
 request(PROVIDER,'/v3/policydefinitions',{'@context':CTX,'@id':policy,'@type':'PolicyDefinition','policy':{'@context':'http://www.w3.org/ns/odrl.jsonld','@type':'Set'}})
 request(PROVIDER,'/v3/contractdefinitions',{'@context':CTX,'@id':definition,'@type':'ContractDefinition','accessPolicyId':policy,'contractPolicyId':policy,'assetsSelector':[{'operandLeft':NS+'id','operator':'=','operandRight':asset}]})
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
 with urllib.request.urlopen(req,timeout=20) as r:body=r.read()
 assert body==PAYLOAD,'Transferred bytes differ from source'
 try:
  urllib.request.urlopen(urllib.request.Request(endpoint,headers={'Authorization':'invalid'}),timeout=10)
 except urllib.error.HTTPError as e:
  assert e.code in (401,403),f'Unexpected unauthorized status {e.code}'
 else:raise AssertionError('Invalid EDR token accepted')
 result={'upstream':'MOCK_TMS','edc_version':'0.14.1','asset_id':asset,'negotiation_id':negotiation,'agreement_id':agreement,'transfer_process_id':transfer,'bytes':len(body),'sha256':hashlib.sha256(body).hexdigest(),'invalid_token_rejected':True,'status':'PASS'}
 Path('logs/native-exchange-result.json').write_text(json.dumps(result,indent=2)+'\n')
 print(json.dumps(result,indent=2));upstream.shutdown()
if __name__=='__main__':main()

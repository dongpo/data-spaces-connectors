"""Role-based demonstration UI backed by live EDC and TTC calls. Isolated fixture 401 only."""
import sys
from pathlib import Path
sys.path.insert(0,str(Path(__file__).resolve().parents[1]/'scripts'))
import live_walkthrough as edc
from ttc_test_client import call,token,BASE
import json,threading,time,secrets,urllib.request,urllib.error
from http.server import BaseHTTPRequestHandler,ThreadingHTTPServer
SESSION=Path('logs/workbench-'+time.strftime('%Y%m%dT%H%M%SZ',time.gmtime()));SESSION.mkdir(parents=True,exist_ok=True)
NONCE=secrets.token_urlsafe(32);LOCK=threading.Lock()
STATE={'busy':False,'phase':'ready','contract':{},'records':[],'events':[],'agreement':None,'transfer':None,'exchange':None,'bytes':0,'receipt':None,'error':None,'denied':False,'access_time':None}
def log(message):
 if 'PASS' in message or message.startswith('{'):return
 with LOCK:STATE['events'].append({'time':time.strftime('%H:%M:%S',time.gmtime()),'text':message})
 (SESSION/'events.json').write_text(json.dumps(STATE['events'],ensure_ascii=False,indent=2))
edc.emit=log
def contract():
 c=call('consumer','eligibility')
 with LOCK:STATE['contract']=c
 return c
def receipts():
 exchange=STATE['exchange']
 if not exchange:return []
 req=urllib.request.Request(BASE+'/integration/index.php?action=exchange_reports&contract_id=401&exchange_id='+exchange,headers={'Authorization':'Bearer '+token('consumer')})
 with urllib.request.urlopen(req,timeout=15) as r:return json.load(r)['reports']
def access():
 with LOCK:STATE.update(busy=True,error=None,denied=False,phase='checking')
 try:
  c=contract();log('Consumer 向中介層取得合約資格：'+('有效' if c['active'] else '已終止或失效'))
  if edc.DATA.get('endpoint'):
   with LOCK:STATE['phase']='transferring'
   # Re-use the actual EDR so the data plane itself enforces revocation.
   req=urllib.request.Request(edc.DATA['endpoint'],headers={'Authorization':edc.DATA['auth']})
   with urllib.request.urlopen(req,timeout=45) as r:
    body=r.read();h=r.headers
   edc.DATA.update(body=body,exchange=h['X-Exchange-ID'],provider_process=h['X-EDC-Process-ID'],provider_agreement=h['X-EDC-Agreement-ID'])
   log('Data Plane 已回傳新一次資料，建立獨立交換識別')
  else:
   with LOCK:STATE['phase']='negotiating'
   edc.step2()
   with LOCK:STATE.update(agreement=edc.DATA['agreement'],phase='transferring')
   edc.step3()
  records=json.loads(edc.DATA['body'])
  with LOCK:STATE.update(records=records,bytes=len(edc.DATA['body']),transfer=edc.DATA['transfer'],exchange=edc.DATA['exchange'],access_time=time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime()),phase='auditing')
  edc.step4();reports=receipts()
  with LOCK:STATE.update(receipt={'status':'MATCHED','reports':reports},phase='delivered')
  log('中介層已比對雙方回報：內容與傳輸量一致')
 except urllib.error.HTTPError as e:
  with LOCK:STATE.update(error=('合約已終止，Provider 的資料傳輸服務拒絕這次取用。' if not STATE['contract'].get('active') else '資料取用未獲授權，請重新確認合約或資料取用憑證。') if e.code==403 else '資料取用失敗，HTTP '+str(e.code),denied=e.code==403,phase='denied' if e.code==403 else 'error')
  log('Data Plane 實際回應 HTTP '+str(e.code)+'；本次沒有傳回新的事件資料')
 except Exception as e:
  with LOCK:STATE.update(error=type(e).__name__+': '+str(e),phase='error')
  log('操作發生錯誤，未宣告完成')
 finally:
  with LOCK:STATE['busy']=False
  (SESSION/'result.json').write_text(json.dumps({k:v for k,v in STATE.items() if k!='records'},ensure_ascii=False,indent=2))
def lifecycle(terminate):
 if STATE['busy']:raise RuntimeError('請等待資料取用完成')
 # This action intentionally applies only to the named synthetic contract fixture.
 edc.sql('UPDATE gaia_contracts SET status='+('3' if terminate else '1')+", terminated_at="+('NOW()' if terminate else 'NULL')+", terminated_reason="+("'Isolated demo lifecycle operation'" if terminate else 'NULL')+' WHERE thisid=401')
 c=contract();log('中介層測試合約 #401：'+('已終止，後續取用不再授權' if terminate else '已重設為生效中'))
 return c
class Handler(BaseHTTPRequestHandler):
 def send(self,data,kind='application/json',status=200):
  if isinstance(data,(dict,list)):data=json.dumps(data,ensure_ascii=False).encode()
  elif isinstance(data,str):data=data.encode()
  self.send_response(status);self.send_header('Content-Type',kind);self.send_header('Cache-Control','no-store');self.send_header('Content-Length',str(len(data)));self.end_headers();self.wfile.write(data)
 def do_GET(self):
  if self.path=='/':return self.send(Path(__file__).with_name('workbench.html').read_text().replace('__TOKEN__',NONCE),'text/html; charset=utf-8')
  if self.path=='/api/state':
   with LOCK:s=json.loads(json.dumps(STATE))
   return self.send(s)
  if self.path=='/api/provider':
   a=edc.request(edc.PROVIDER,'/v3/assets/'+edc.DATA['asset'])
   props=edc.field(a,'properties',{})
   return self.send({'asset_id':a['@id'],'found':True,'name':edc.field(props,'name','TMS live traffic incidents'),'contract':call('provider','eligibility')})
  if self.path=='/api/catalog':
   catalog=edc.request(edc.CONSUMER,'/v3/catalog/request',{'@context':edc.CTX,'@type':'CatalogRequest','counterPartyId':'urn:connector:provider','counterPartyAddress':edc.DSP,'protocol':'dataspace-protocol-http'})
   datasets=edc.field(catalog,'dataset',[]);datasets=datasets if isinstance(datasets,list) else [datasets]
   found=next((d for d in datasets if d.get('@id')==edc.DATA['asset']),None)
   return self.send({'asset_id':edc.DATA['asset'],'found':found is not None,'name':'TMS live traffic incidents','contract':contract()})
  if self.path=='/api/contract':return self.send(contract())
  if self.path=='/recording.webm' and (SESSION/'operation.webm').exists():return self.send((SESSION/'operation.webm').read_bytes(),'video/webm')
  return self.send({},status=404)
 def do_POST(self):
  if not secrets.compare_digest(self.headers.get('X-Demo-Token',''),NONCE):return self.send({},status=403)
  size=int(self.headers.get('Content-Length','0'))
  if size>100*1024*1024:return self.send({},status=413)
  body=self.rfile.read(size)
  if self.path=='/api/access':
   with LOCK:
    if STATE['busy']:return self.send({'error':'已有取用作業進行中'},status=409)
    STATE['busy']=True
   threading.Thread(target=access,daemon=True).start();return self.send({'started':True})
  if self.path in ['/api/terminate','/api/reset']:
   try:return self.send(lifecycle(self.path.endswith('terminate')))
   except RuntimeError as e:return self.send({'error':str(e)},status=409)
  if self.path=='/recording':
   (SESSION/'operation.webm').write_bytes(body);return self.send({'saved':True})
  return self.send({},status=404)
 def log_message(self,*args):pass
if __name__=='__main__':
 contract();edc.step1();log('已讀取中介層的核准測試合約，並載入實際 EDC 目錄商品')
 ThreadingHTTPServer(('127.0.0.1',8082),Handler).serve_forever()

"""Private, live five-step EDC/TTC demonstration and in-app video capture.
Every displayed protocol result originates in a new call to the running services.
"""
import copy,hashlib,json,os,secrets,subprocess,threading,time,urllib.request,urllib.error,uuid
from pathlib import Path
from http.server import BaseHTTPRequestHandler,ThreadingHTTPServer
from test_edc_exchange import CTX,NS,PROVIDER,CONSUMER,DSP,request,field
from ttc_test_client import call
TOKEN=secrets.token_urlsafe(32)
LOCK=threading.Lock();EVENTS=[];STATE={'completed':0,'running':False,'error':None};DATA={}
def emit(message):
 with LOCK:EVENTS.append({'time':time.strftime('%H:%M:%S',time.gmtime()),'text':message})
 Path('logs/walkthrough-events.json').write_text(json.dumps(EVENTS,ensure_ascii=False,indent=2))
def sql(statement):
 env=dict(x.split('=',1) for x in Path('secrets/db.env').read_text().splitlines())
 r=subprocess.run(['docker','exec','-e','MYSQL_PWD='+env['MYSQL_ROOT_PASSWORD'],'ttc-demo-mysql','mysql','-uroot','ttcdb','-N','-B','-e',statement],capture_output=True,text=True,check=True)
 return r.stdout.strip()
def wait_state(path,expected):
 last=None
 for _ in range(90):
  state=field(request(CONSUMER,path),'state')
  if state!=last:emit('EDC state → '+str(state));last=state
  if state==expected:return
  if state in ('TERMINATED','ERROR'):raise RuntimeError('Unexpected EDC terminal state: '+state)
  time.sleep(1)
 raise RuntimeError('EDC state timeout')
def step1():
 suffix=uuid.uuid4().hex[:8];asset='tms-video-'+suffix;policy='video-access-'+suffix;cp='video-contract-'+suffix
 emit('Provider registers live TMS asset and TTC contract policy')
 request(PROVIDER,'/v3/assets',{'@context':CTX,'@id':asset,'properties':{'name':'TMS live traffic incidents'},'dataAddress':{'type':'HttpData','baseUrl':'http://www.1968.com.tw/TMSEventService/TrafficIncident.ashx?type=json','method':'GET','authKey':'Cookie','secretName':'tms-upstream-cookie',**{'https://geomni.tw/ttc/'+k:str(v) for k,v in {'contractId':401,'productId':101,'versionId':201,'providerOrg':1,'consumerOrg':2}.items()}}})
 request(PROVIDER,'/v3/policydefinitions',{'@context':CTX,'@id':policy,'@type':'PolicyDefinition','policy':{'@context':'http://www.w3.org/ns/odrl.jsonld','@type':'Set'}})
 request(PROVIDER,'/v3/policydefinitions',{'@context':CTX,'@id':cp,'@type':'PolicyDefinition','policy':{'@context':'http://www.w3.org/ns/odrl.jsonld','@type':'Set','permission':[{'action':'use','constraint':[{'leftOperand':'https://geomni.tw/ttc/approvedContract','operator':'eq','rightOperand':'401:101:201'}]}]}})
 request(PROVIDER,'/v3/contractdefinitions',{'@context':CTX,'@id':'video-definition-'+suffix,'@type':'ContractDefinition','accessPolicyId':policy,'contractPolicyId':cp,'assetsSelector':[{'operandLeft':NS+'id','operator':'=','operandRight':asset}]})
 emit('Consumer POST /management/v3/catalog/request')
 catalog=request(CONSUMER,'/v3/catalog/request',{'@context':CTX,'@type':'CatalogRequest','counterPartyId':'urn:connector:provider','counterPartyAddress':DSP,'protocol':'dataspace-protocol-http'})
 ds=field(catalog,'dataset',[]);ds=ds if isinstance(ds,list) else [ds];dataset=next(d for d in ds if d.get('@id')==asset)
 offer=field(dataset,'hasPolicy');offer=offer[0] if isinstance(offer,list) else offer
 offer=copy.deepcopy(offer);offer['@context']=catalog.get('@context',CTX);offer['odrl:assigner']={'@id':'urn:connector:provider'};offer['odrl:target']={'@id':asset}
 DATA.update(asset=asset,offer=offer)
 emit('Provider: urn:connector:provider');emit('Catalog asset: '+asset);emit('TTC contract=401 · product=101 · version=201');emit('Policy constraint: approvedContract EQ 401:101:201');emit('STEP 1 PASS — live catalog response received')
def step2():
 c=call('consumer','eligibility');assert c['active'];emit('TTC machine authentication + contract eligibility: ACTIVE')
 emit('Consumer POST /management/v3/contractnegotiations')
 n=request(CONSUMER,'/v3/contractnegotiations',{'@context':CTX,'@type':'ContractRequest','counterPartyAddress':DSP,'protocol':'dataspace-protocol-http','policy':DATA['offer']})['@id']
 emit('Negotiation: '+n);wait_state('/v3/contractnegotiations/'+n+'/state','FINALIZED')
 a=request(CONSUMER,'/v3/contractnegotiations/'+n+'/agreement')['@id'];DATA.update(negotiation=n,agreement=a)
 emit('EDC agreement: '+a);emit('STEP 2 PASS — native DSP negotiation FINALIZED')
def step3():
 emit('Consumer POST /management/v3/transferprocesses · HttpData-PULL')
 t=request(CONSUMER,'/v3/transferprocesses',{'@context':CTX,'@type':'TransferRequest','protocol':'dataspace-protocol-http','contractId':DATA['agreement'],'connectorId':'urn:connector:provider','counterPartyAddress':DSP,'transferType':'HttpData-PULL'})['@id']
 emit('Transfer: '+t);wait_state('/v3/transferprocesses/'+t+'/state','STARTED')
 for _ in range(30):
  try:edr=request(CONSUMER,'/v3/edrs/'+t+'/dataaddress');break
  except RuntimeError:time.sleep(1)
 else:raise RuntimeError('No EDR received')
 endpoint=field(edr,'endpoint');auth=field(edr,'authorization');emit('EDR received · credential hidden · pulling live TMS data')
 req=urllib.request.Request(endpoint,headers={'Authorization':auth})
 with urllib.request.urlopen(req,timeout=45) as r:
  body=r.read();headers=r.headers;assert r.status==200
 records=json.loads(body);assert isinstance(records,list) and records and all('TMSEventID' in x for x in records)
 DATA.update(endpoint=endpoint,auth=auth,body=body,transfer=t,exchange=headers['X-Exchange-ID'],provider_process=headers['X-EDC-Process-ID'],provider_agreement=headers['X-EDC-Agreement-ID'],record_count=len(records))
 emit('HTTP 200 · '+str(len(records))+' REAL traffic incidents · '+str(len(body))+' bytes')
 for item in records[:3]:
  emit(json.dumps({k:item.get(k) for k in ['TMSEventID','RoadName','EventType','HappenedTime']},ensure_ascii=False))
 emit('Exchange: '+DATA['exchange']);emit('STEP 3 PASS — live data delivered through EDC data plane')
def step4():
 digest=hashlib.sha256(DATA['body']).hexdigest()
 report={'contract_id':401,'exchange_id':DATA['exchange'],'reporter_type':'CONSUMER','edc_transfer_process_id':DATA['provider_process'],'edc_agreement_id':DATA['provider_agreement'],'payload_hash':digest,'bytes_transferred':len(DATA['body'])}
 emit('Provider has independently reported the transmitted bytes and SHA-256')
 emit('Consumer POST /integration/index.php?action=audit')
 for _ in range(10):
  result=call('consumer','audit',report)
  if result['verification_status']=='MATCHED':break
  time.sleep(1)
 assert result['verification_status']=='MATCHED'
 # Exchange ID is server generated UUID, not browser-supplied SQL.
 rows=sql("SELECT reporter_type,bytes_transferred,payload_hash FROM gaia_edc_exchange_reports WHERE contract_id=401 AND exchange_id='"+DATA['exchange']+"' ORDER BY reporter_type")
 for row in rows.splitlines():emit(row.replace('\t',' | '))
 emit('TTC verification_status = '+result['verification_status']);emit('STEP 4 PASS — Provider / Consumer evidence MATCHED')
def step5():
 emit('Set isolated TTC test contract 401 to SUSPENDED')
 try:
  sql('UPDATE gaia_contracts SET status=3 WHERE thisid=401')
  assert not call('consumer','eligibility')['active'];emit('TTC eligibility.active = false')
  try:urllib.request.urlopen(urllib.request.Request(DATA['endpoint'],headers={'Authorization':DATA['auth']}),timeout=20)
  except urllib.error.HTTPError as e:
   assert e.code==403;emit('Existing EDR → HTTP 403 · data access DENIED')
  else:raise AssertionError('Suspended contract still allowed data')
  emit('Attempt a NEW native EDC negotiation while contract is suspended')
  try:n=request(CONSUMER,'/v3/contractnegotiations',{'@context':CTX,'@type':'ContractRequest','counterPartyAddress':DSP,'protocol':'dataspace-protocol-http','policy':DATA['offer']})['@id']
  except RuntimeError as e:
   assert 'HTTP 400' in str(e) or 'HTTP 403' in str(e);emit('New negotiation rejected by native EDC policy validation')
  else:
   last=None
   for _ in range(45):
    state=field(request(CONSUMER,'/v3/contractnegotiations/'+n+'/state'),'state')
    if state!=last:emit('New negotiation state → '+str(state));last=state
    if state=='TERMINATED':break
    assert state!='FINALIZED','Suspended negotiation succeeded';time.sleep(1)
   else:raise RuntimeError('Suspended negotiation was not rejected')
 finally:
  sql('UPDATE gaia_contracts SET status=1 WHERE thisid=401');emit('Test contract restored to ACTIVE')
 emit('STEP 5 PASS — both data access and new negotiation DENIED')
 result={k:v for k,v in DATA.items() if k in ('asset','negotiation','agreement','transfer','exchange','record_count')};result.update(completed_at_utc=time.strftime('%Y-%m-%dT%H:%M:%SZ',time.gmtime()),bytes=len(DATA['body']),sha256=hashlib.sha256(DATA['body']).hexdigest(),status='PASS',all_five_steps=True)
 Path('logs/walkthrough-result.json').write_text(json.dumps(result,indent=2));emit('ALL FIVE LIVE STEPS COMPLETED · PASS')
def run(n):
 try:
  [step1,step2,step3,step4,step5][n-1]()
  with LOCK:STATE['completed']=n
 except Exception as e:
  with LOCK:STATE['error']=type(e).__name__+': '+str(e)
  emit('ERROR: '+STATE['error'])
 finally:
  with LOCK:STATE['running']=False
HTML=r'''<!DOCTYPE html><html lang="zh-Hant"><meta charset="utf-8"><title>TMS EDC Live Walkthrough</title><style>body{margin:0;background:#111b2c;color:#dceafa;font:16px system-ui;display:flex;align-items:center;flex-direction:column}canvas{width:min(100vw,1600px);height:auto;cursor:pointer}#help{padding:10px}a{color:#8bd8ff}</style><canvas id="c" width="1600" height="900"></canvas><div id="help">五個按鈕直接呼叫雲端 EDC／TTC。錄影只擷取此操作畫面。<button id="rec">開始錄製實機操作</button> <span id="saved"></span></div><script>
const nonce='__TOKEN__',c=document.getElementById('c'),g=c.getContext('2d');let events=[],state={completed:0,running:false,error:null},current=0,clickAt=0,clickXY=[0,0],started=Date.now(),recorder,chunks=[],recording=false,stopping=false;
const labels=['1 目錄查詢','2 合約協商','3 真實資料取用','4 雙方稽核','5 停用與拒絕'];
const notes=['Consumer 向 Provider 查詢原生 EDC Catalog','EDC Dataspace Protocol 協商 + TTC 合約資格檢查','EDC Data Plane 以 EDR 取得即時 TMS 事件','Provider / Consumer 分別回報實際傳輸證據','停用合約，再試既有權杖與新的合約協商'];
function lines(s,max=95){const a=[];while(s.length>max){a.push(s.slice(0,max));s=s.slice(max)}a.push(s);return a}
function draw(){g.fillStyle='#111b2c';g.fillRect(0,0,1600,900);g.fillStyle='#82cfff';g.font='22px system-ui';g.fillText('GEOMNI  ·  Eclipse EDC 0.14.1 × TTC × TMS  ·  LIVE',48,42);g.fillStyle='#ffffff';g.font='bold 38px system-ui';g.fillText('資料空間實機操作：Provider ↔ Consumer ↔ 中介層',48,98);
for(let i=0;i<5;i++){let x=48+i*303;g.fillStyle=state.completed>i?'#145c49':current===i+1?'#235d96':'#25354d';g.fillRect(x,136,285,70);g.fillStyle='#fff';g.font='bold 24px system-ui';g.fillText(labels[i],x+18,179);if(state.completed>i){g.font='16px system-ui';g.fillText('✓',x+254,179)}}
g.fillStyle='#a7c5e9';g.font='23px system-ui';g.fillText(current?notes[current-1]:'依序點選五個步驟，實際執行雲端請求',48,250);g.fillStyle='#071222';g.fillRect(48,278,1504,530);let all=[];for(const e of events)for(const l of lines('['+e.time+' UTC] '+e.text))all.push(l);const visible=all.slice(-17);g.font='22px "SFMono-Regular",Consolas,"PingFang TC",monospace';for(let i=0;i<visible.length;i++){g.fillStyle=/PASS|MATCHED|HTTP 200/.test(visible[i])?'#8ee4b0':/DENIED|403|SUSPENDED|TERMINATED/.test(visible[i])?'#ffd27e':'#dceafa';g.fillText(visible[i],66,312+i*28)}
g.fillStyle=state.error?'#ff8585':state.running?'#ffcf75':'#91e5b4';g.font='24px system-ui';g.fillText(state.error?'ERROR — '+state.error:state.running?'執行中 '+['◐','◓','◑','◒'][Math.floor(Date.now()/250)%4]+'  正在等待真實服務回應':state.completed===5?'五個實機步驟全部通過 · PASS':'就緒 · 點選下一個步驟',48,852);g.fillStyle='#97aec8';g.font='18px system-ui';g.fillText(new Date().toISOString().replace('T',' ').slice(0,19)+' UTC  |  GCP connector-demo',920,852);if(recording){g.fillStyle='#ff7373';g.beginPath();g.arc(1480,38,8,0,Math.PI*2);g.fill();g.fillText('REC',1498,44)}if(Date.now()-clickAt<650){g.strokeStyle='#77ccff';g.lineWidth=3;g.beginPath();g.arc(clickXY[0],clickXY[1],18+(Date.now()-clickAt)/25,0,Math.PI*2);g.stroke()}requestAnimationFrame(draw)}
c.onclick=async e=>{let r=c.getBoundingClientRect(),x=(e.clientX-r.left)*1600/r.width,y=(e.clientY-r.top)*900/r.height;clickXY=[x,y];clickAt=Date.now();if(y<136||y>206||state.running)return;let n=Math.floor((x-48)/303)+1;if(n!==state.completed+1||n<1||n>5)return;current=n;state.running=true;try{let r=await fetch('/step',{method:'POST',headers:{'Content-Type':'application/json','X-Demo-Token':nonce},body:JSON.stringify({step:n})});if(!r.ok)throw Error('HTTP '+r.status)}catch(e){state.error=String(e);state.running=false}};
setInterval(async()=>{try{let r=await fetch('/events?after='+events.length);let d=await r.json();events.push(...d.events);state=d.state;if(state.completed===5&&recording&&!stopping){stopping=true;setTimeout(()=>recorder.stop(),7000)}}catch(e){}},400);
document.getElementById('rec').onclick=()=>{chunks=[];const mime=MediaRecorder.isTypeSupported('video/webm;codecs=vp9')?'video/webm;codecs=vp9':'video/webm';recorder=new MediaRecorder(c.captureStream(12),{mimeType:mime,videoBitsPerSecond:2400000});recorder.ondataavailable=e=>{if(e.data.size)chunks.push(e.data)};recorder.onstop=async()=>{recording=false;document.getElementById('saved').textContent='正在儲存實機錄影…';let b=new Blob(chunks,{type:'video/webm'});let r=await fetch('/recording',{method:'POST',headers:{'X-Demo-Token':nonce},body:b});document.getElementById('saved').innerHTML=r.ok?'<a href="/recording.webm" download="tms-edc-live-operation.webm">下載實機操作錄影</a>':'儲存失敗'};recorder.start(1000);recording=true;document.getElementById('rec').disabled=true};draw();
</script></html>'''
class Handler(BaseHTTPRequestHandler):
 def send(self,data,kind='application/json',status=200):
  if isinstance(data,(dict,list)):data=json.dumps(data,ensure_ascii=False).encode()
  elif isinstance(data,str):data=data.encode()
  self.send_response(status);self.send_header('Content-Type',kind);self.send_header('Cache-Control','no-store');self.send_header('Content-Length',str(len(data)));self.end_headers();self.wfile.write(data)
 def do_GET(self):
  if self.path=='/':return self.send(HTML.replace('__TOKEN__',TOKEN),'text/html; charset=utf-8')
  if self.path.startswith('/events?after='):
   try:n=int(self.path.split('=')[1])
   except ValueError:return self.send({},status=400)
   with LOCK:data={'events':EVENTS[n:],'state':dict(STATE)}
   return self.send(data)
  if self.path=='/recording.webm' and Path('logs/walkthrough.webm').exists():return self.send(Path('logs/walkthrough.webm').read_bytes(),'video/webm')
  return self.send({},status=404)
 def do_POST(self):
  if not secrets.compare_digest(self.headers.get('X-Demo-Token',''),TOKEN):return self.send({},status=403)
  size=int(self.headers.get('Content-Length','0'))
  if size>100*1024*1024:return self.send({},status=413)
  body=self.rfile.read(size)
  if self.path=='/recording':
   Path('logs/walkthrough.webm').write_bytes(body);return self.send({'saved':True})
  if self.path=='/step':
   try:n=int(json.loads(body)['step'])
   except Exception:return self.send({},status=400)
   with LOCK:
    if n!=STATE['completed']+1 or STATE['running'] or STATE['error'] or n>5:return self.send({},status=409)
    STATE['running']=True
   threading.Thread(target=run,args=(n,),daemon=True).start();return self.send({'started':n})
  return self.send({},status=404)
 def log_message(self,*args):pass
if __name__=='__main__':
 emit('LIVE session ready · actual Google Cloud VM · credentials never displayed')
 ThreadingHTTPServer(('127.0.0.1',8081),Handler).serve_forever()

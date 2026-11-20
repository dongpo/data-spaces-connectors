"""Test driver for existing middleware machine authentication, using isolated keys."""
import base64,json,time,uuid,subprocess,urllib.request,urllib.parse
from pathlib import Path
BASE='http://localhost:8088'
def b64(data):return base64.urlsafe_b64encode(data).decode().rstrip('=')
def token(role):
 client='did:web:localhost:8088:connector:'+('11' if role=='provider' else '22')
 now=int(time.time());header=b64(b'{"alg":"RS256","typ":"JWT"}')
 body=b64(json.dumps(dict(iss=client,sub=client,aud='https://localhost:8088/auth/token',iat=now,exp=now+300,jti=str(uuid.uuid4()))).encode())
 unsigned=header+'.'+body
 signed=subprocess.run(['openssl','dgst','-sha256','-sign',f'secrets/platform/{role}_private.key'],input=unsigned.encode(),capture_output=True,check=True).stdout
 form=urllib.parse.urlencode({'grant_type':'client_credentials','client_id':client,'client_assertion_type':'urn:ietf:params:oauth:client-assertion-type:jwt-bearer','client_assertion':unsigned+'.'+b64(signed)}).encode()
 with urllib.request.urlopen(urllib.request.Request(BASE+'/auth/token.php',data=form),timeout=20) as r:return json.load(r)['access_token']
def call(role,action,report=None):
 url=BASE+'/integration/index.php?action='+action+('&contract_id=401' if report is None else '')
 req=urllib.request.Request(url,data=None if report is None else json.dumps(report).encode(),headers={'Authorization':'Bearer '+token(role),'Content-Type':'application/json'})
 with urllib.request.urlopen(req,timeout=20) as r:return json.load(r)
if __name__=='__main__':
 for role in ('provider','consumer'):
  result=call(role,'eligibility');assert result['active'];print(role+' M2M and eligibility PASS')

"""Apply isolated TTC integration settings to each native EDC runtime."""
from pathlib import Path
for role in ('provider','consumer','dataplane'):
 p=Path('config')/(role+'.properties')
 lines=[line for line in p.read_text().splitlines() if not line.startswith('ttc.')]
 identity='consumer' if role=='consumer' else 'provider'
 if role=='dataplane':
  lines=[line for line in lines if not line.startswith('edc.participant.id=')]
  lines.append('edc.participant.id=urn:connector:provider')
 settings={'base.url':'http://localhost:8088','client.id':'did:web:localhost:8088:connector:'+('22' if identity=='consumer' else '11'),'private.key.path':'secrets/platform/'+identity+'_private.key','isolated.http':'true','provider.org.id':'1','consumer.org.id':'2','provider.participant.id':'urn:connector:provider','consumer.participant.id':'urn:connector:consumer'}
 p.write_text('\n'.join(lines+[f'ttc.{k}={v}' for k,v in settings.items()])+'\n')
print('Isolated TTC settings applied')

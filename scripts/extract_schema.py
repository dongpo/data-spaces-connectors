"""Extract DDL without truncating semicolons inside SQL string literals."""
import re,sys
from pathlib import Path

def extract_schema(sql):
 result=[]
 for match in re.finditer(r'^(?:CREATE TABLE|ALTER TABLE)\s+`[^`]+`',sql,re.M):
  quote=None;escape=False
  for i in range(match.start(),len(sql)):
   c=sql[i]
   if escape: escape=False;continue
   if quote:
    if c=='\\':escape=True
    elif c==quote:quote=None
   elif c in "'\"`":quote=c
   elif c==';':result.append(sql[match.start():i+1]);break
  else:raise ValueError('Unterminated DDL')
 return result
if __name__=='__main__':
 statements=extract_schema(Path(sys.argv[1]).read_text())
 Path(sys.argv[2]).write_text('SET NAMES utf8mb4;\n'+'\n\n'.join(statements)+'\n')
 print(f'Exported {len(statements)} DDL statements, no data rows')

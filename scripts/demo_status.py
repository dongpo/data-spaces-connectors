"""Read-only, loopback-only display of native runtime health and recorded test evidence."""
import json,urllib.request,html
from pathlib import Path
from http.server import BaseHTTPRequestHandler,ThreadingHTTPServer
class Handler(BaseHTTPRequestHandler):
 def do_GET(self):
  if self.path!='/':self.send_error(404);return
  health={}
  for role,port in [('Provider',19191),('Consumer',29191),('Data plane',19181)]:
   try:
    with urllib.request.urlopen(f'http://localhost:{port}/api/check/health',timeout=3) as r:health[role]=r.status==200
   except Exception:health[role]=False
  p=Path('logs/live-tms-exchange-result.json')
  proof=json.loads(p.read_text()) if p.exists() else {}
  checks=[('TMS 真實來源',proof.get('upstream')=='LIVE_TMS'),('雙方稽核配對',proof.get('matched_audit')),('無效資料取用憑證拒絕',proof.get('invalid_token_rejected')),('合約停用後拒絕',proof.get('suspended_existing_edr_rejected')),('重複回報一致性',proof.get('duplicate_idempotent') and proof.get('conflict_rejected')),('回報者角色檢查',proof.get('reporter_impersonation_rejected'))]
  cards=''.join(f'<div class="card"><span>{html.escape(k)}</span><strong>{"PASS" if v else "尚未通過"}</strong></div>' for k,v in checks)
  services=' · '.join(f'{k}: {"運作中" if v else "未就緒"}' for k,v in health.items())
  body=f'''<!doctype html><html lang="zh-Hant"><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>TMS Data Spaces 整合測試</title><style>body{{font:17px system-ui;background:#f3f6fb;color:#16243b;margin:0}}main{{max-width:1040px;margin:55px auto;padding:20px}}.eyebrow{{color:#3166ab}}h1{{font-size:38px}}.grid{{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin:28px 0}}.card{{background:white;border:1px solid #d4dfec;border-radius:12px;padding:22px}}strong{{display:block;color:#14734d;margin-top:12px}}pre{{background:#10233e;color:#dce9fa;padding:24px;overflow:auto;border-radius:12px;font-size:13px}}.note{{color:#54657c}}@media(max-width:700px){{.grid{{grid-template-columns:1fr 1fr}}}}</style><main><div class="eyebrow">Geomni · Eclipse EDC 0.14.1 × TTC × TMS</div><h1>資料空間點對點驗證</h1><p>原生 Provider／Consumer Connector → 合約檢查 → 真實交通事件 API → 雙方傳輸稽核</p><p class="note">{services}</p><div class="grid">{cards}</div><h2>本次驗證紀錄</h2><p>傳輸量：{proof.get('bytes','—')} bytes · 結果：{html.escape(proof.get('status','尚無紀錄'))}</p><pre>{html.escape(json.dumps(proof,ensure_ascii=False,indent=2))}</pre><p class="note">本頁僅讀取服務健康狀態與最近一次測試紀錄。僅限隔離展示環境。</p></main></html>'''.encode()
  self.send_response(200);self.send_header('Content-Type','text/html; charset=utf-8');self.send_header('Content-Length',str(len(body)));self.send_header('Cache-Control','no-store');self.end_headers();self.wfile.write(body)
 def log_message(self,*args):pass
if __name__=='__main__':ThreadingHTTPServer(('127.0.0.1',8080),Handler).serve_forever()

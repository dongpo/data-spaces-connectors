# 山海經（TTC）中介層：問題與修改

本文件依 2026-11-20 收到的 GCP 部署目錄原始碼、patch manifest 與先前測試紀錄整理。原始移交檔未直接改寫；修正位於隔離部署副本。

| 問題 | 已實作修改 | 相關檔案 |
|---|---|---|
| 簽發端輸出 transaction_token，驗證端卻要求 contract_token | 驗證端改與實際簽發端一致；另核對 jti 紀錄、合約、效期、產品及提供者 | middleware/source/auth/verify.php、auth/contract_token.php |
| 合約查詢使用不存在的 isopen 欄位 | 改用 status=1，並檢查 effective_at、expired_at | middleware/source/auth/verify.php |
| 回報者未充分綁定合約當事人與角色 | 核對 token 組織、合約提供者／申請者、reporter_type、reporter_did | middleware/source/audit/transaction/index.php |
| 多條稽核路徑實作不同，可能造成驗證落差 | clearing/log.php 統一轉交 transaction 實作 | middleware/source/audit/clearing/log.php |
| 中介層沒有 EDC 可直接使用的資格與逐次傳輸介面 | 新增經既有 M2M 驗證的 eligibility、audit、exchange_reports | middleware/source/integration/index.php |
| 同一合約／憑證可能用於多次 HTTP 取用，難以逐次配對 | 新增 exchange_id、EDC agreement/process ID、雙方 SHA-256 與 bytes | middleware/edc-schema.sql、integration/index.php |
| 重送與衝突回報無明確區分 | 同合約＋exchange_id＋角色設唯一鍵；相同內容重送接受，不同內容回 409；雙方一致才 MATCHED，否則 DISCREPANCY | middleware/edc-schema.sql、integration/index.php |
| 憑證及部署參數混在程式／移交環境中 | DB/SMTP 改環境變數、移除嵌入憑證、隔離測試金鑰、Docker 與啟動腳本 | middleware/source/adm/config/config.php、scripts/prepare_middleware.py、scripts/cloud-middleware-start.sh |

## Token 與合約不是同一件事

修正 type 不表示合約與傳輸憑證被合併。TTC 合約紀錄表示當事人之間的授權條件；中介層 transaction_token 是其簽發與驗證流程使用的憑證；EDC agreement、transfer process、EDR 及每次 HTTP exchange_id 仍是不同識別與生命週期。EDC 整合走 eligibility API 檢查合約，資料取用由 EDC 驗證 EDR；不能把 TTC token 直接當作 EDC EDR。

## 我們的 EDC connectors 另外負責什麼

中介層不是 DSP 對端。我們的 Provider／Consumer 原生 EDC 控制平面進行目錄查詢、DSP 協商及啟動傳輸。政策擴充將中介層有效合約與參與者映射接到 EDC 協商／傳輸授權；資料平面擴充每次 HTTP pull 重新確認合約，計算實際資料雜湊與 bytes，回報 Provider 稽核。Consumer 另行計算及回報，讓中介層配對。

先前實測發現 EDC 的 ODRL use 動作需一併綁定協商 scope；只註冊 constraint function 不足以保證新協商被拒絕。另曾遇到 Maven 增量 shade 保留舊類別，改用 clean package 並檢查打包內容。這兩項屬 Connector 整合問題，不能歸為原中介層缺陷。

## 已觀察的測試結果及限制

先前 GCP 實機測試觀察到 30 筆 TMS 事件、17,698 bytes、雙方稽核 MATCHED；無效 EDR、失效合約後舊 EDR 再取用、新協商、冒充回報者及衝突重送均遭拒絕。此處引用既有紀錄，未在此次匯入環境重新執行。

目前仍未涵蓋人工上架／申請／核准 UI 的完整測試、正式聯邦身分、EDC 持久儲存／正式 Vault、quota／用途／下載後使用限制、稽核 outbox 自動重送。合約終止只阻止後續取用，不刪除已下載資料，也不代表中止正在傳送的 stream。展示以測試合約 #401、已核准商品及同一 VM 的獨立服務執行。

## 此次原始碼檢查額外發現

移交副本的 adm/config/share.php 含固定帳號／日期型管理員驗證捷徑。公開 GitHub 副本已停用此捷徑；尚未套用 GCP。auth/verify.php 仍透過私鑰導出公鑰驗章，尚未完成金鑰使用最小化。舊 Python 錄影端點仍存在。這些不能列為 GCP 已修復事項。

公開上傳檢查另發現 SMSlock 的內嵌驗證金鑰，公開副本改由 TTC_SMS_LOCK_KEY 環境變數提供；此修改尚未套用 GCP。

公開副本另移除寄信測試程式的個人收件資料、測試訊息與自動寄信呼叫；未改動 GCP。

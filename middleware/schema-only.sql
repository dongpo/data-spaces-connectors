SET NAMES utf8mb4;
CREATE TABLE `fun_enttype` (
  `thisid` int NOT NULL,
  `thisname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名稱\r\n',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人\r\n',
  `dateadd` timestamp NULL DEFAULT NULL COMMENT '新增日期\r\n',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `fun_group` (
  `thisid` int NOT NULL,
  `thisname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名稱\r\n',
  `sorting` int NOT NULL COMMENT '排序\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `fun_list` (
  `thisid` int NOT NULL,
  `fun_group_thisid` int NOT NULL COMMENT '分類ID\r\n',
  `thisname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名稱\r\n',
  `funclink` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '網頁\r\n',
  `funcval` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '變數\r\n',
  `sorting` int NOT NULL DEFAULT '0' COMMENT '排序\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `fun_log` (
  `thisid` int NOT NULL COMMENT '主鍵',
  `lastadmin` int NOT NULL COMMENT '管理者ID',
  `mytable` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '操作資料表',
  `mykey` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '操作欄位',
  `myval` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '操作值',
  `dateadd` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '操作時間',
  `type` int NOT NULL DEFAULT '0' COMMENT '操作類型(1=新增,2=更新,3=刪除)',
  `client_ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '客戶端IP位址'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='操作日誌記錄表';

CREATE TABLE `fun_rights` (
  `thisid` int NOT NULL,
  `fun_list_thisid` int NOT NULL COMMENT '功能\r\n',
  `fun_user_thisid` int NOT NULL COMMENT '管理者\r\n',
  `sorting` int DEFAULT NULL COMMENT '排序\r\n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `fun_user` (
  `thisid` int NOT NULL,
  `thisname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '名稱\r\n',
  `pword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '密碼password_hash("mypassword", PASSWORD_DEFAULT);\r\n',
  `thisenttype` int NOT NULL DEFAULT '0' COMMENT '組織類別\r\n',
  `thisentid` int DEFAULT '0' COMMENT '組織ID(可空白)\r\n',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Email(聯繫使用)\r\n',
  `isopen` tinyint NOT NULL DEFAULT '1' COMMENT '上架',
  `issuper` tinyint DEFAULT '0' COMMENT '工程師\r\n',
  `thisbid` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '認證的瀏覽器的代碼',
  `thispk` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '保存的共鑰',
  `creator` int DEFAULT NULL COMMENT '建立者',
  `pdate` datetime NOT NULL COMMENT '密碼日期',
  `dateadd` datetime DEFAULT NULL COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
  `activation_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '啟用token(一次性，啟用後清空)',
  `token_expire` datetime DEFAULT NULL COMMENT 'token過期時間(啟用後清空)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `gaia_allowed_actions` (
  `thisid` int NOT NULL,
  `thisname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '行為名稱(下載/查詢/批次)',
  `thiscode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '行為代碼(download/query/batch)',
  `sorting` int DEFAULT '0' COMMENT '排序',
  `isopen` tinyint DEFAULT '1' COMMENT '上架:0=下架,1=上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Gaia-X允許行為表';

CREATE TABLE `gaia_clearing_logs` (
  `thisid` int NOT NULL,
  `contract_id` int NOT NULL COMMENT 'FK → gaia_contracts.thisid',
  `jti` varchar(100) NOT NULL COMMENT '提貨 Token 唯一識別碼 JTI',
  `reporter_type` varchar(20) NOT NULL COMMENT '填報角色: CONSUMER / PROVIDER',
  `reporter_did` varchar(255) NOT NULL COMMENT '填報者 DID 識別碼',
  `event_type` varchar(50) NOT NULL COMMENT '事件類型',
  `bytes_transferred` bigint NOT NULL COMMENT '傳輸位元組數',
  `payload_hash` varchar(64) NOT NULL COMMENT '資料內容 SHA-256 Checksum',
  `prev_log_hash` varchar(64) NOT NULL COMMENT 'Hash Tree 上一筆 Log 指紋',
  `current_log_hash` varchar(64) NOT NULL COMMENT 'Hash Tree 本筆 Log 指紋',
  `http_status_code` int NOT NULL DEFAULT '200',
  `transfer_time` float DEFAULT '0' COMMENT '傳輸耗時(秒)',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='清算中心雙向 Hash Tree 存證日誌表';

CREATE TABLE `gaia_constraints_type` (
  `thisid` int NOT NULL,
  `thisname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '約束名稱(每日呼叫限制)',
  `thiscode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '約束代碼(daily_limit)',
  `valtype` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '值類型(integer/string/boolean/array)',
  `sorting` int DEFAULT '0' COMMENT '排序',
  `isopen` tinyint DEFAULT '1' COMMENT '上架:0=下架,1=上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='約束條件類型表';

CREATE TABLE `gaia_contracts` (
  `thisid` int NOT NULL COMMENT '合約ID',
  `application_id` int NOT NULL COMMENT '關聯申請表 (FK → gaia_subscriptions.thisid)',
  `product_id` int NOT NULL COMMENT '產品ID (FK → gaia_data_products.thisid)',
  `version_id` int NOT NULL COMMENT '版本ID (FK → gaia_product_versions.thisid)',
  `provider_org_id` int NOT NULL COMMENT '提供者組織ID',
  `applicant_org_id` int NOT NULL COMMENT '申請者組織ID',
  `contract_duration_days` int NOT NULL COMMENT '合約有效天數',
  `effective_at` datetime NOT NULL COMMENT '生效時間 (核准時間)',
  `expired_at` datetime NOT NULL COMMENT '到期時間 (生效日 + 合約天數)',
  `allowed_actions` json DEFAULT NULL COMMENT '核准的權限 (JSON陣列)',
  `constraints` json DEFAULT NULL COMMENT '核准的限制條件 (JSON物件)',
  `prohibited_actions` json DEFAULT NULL COMMENT '禁止的操作 (JSON陣列)',
  `version_snapshot` json NOT NULL COMMENT '版本檔完整鏡像 (核准時複製)',
  `application_snapshot` json NOT NULL COMMENT '申請表完整鏡像 (核准時複製)',
  `odrl_agreement_json` json NOT NULL COMMENT 'ODRL Agreement (核准時產生)',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1=生效中, 2=已過期, 3=已終止',
  `terminated_at` datetime DEFAULT NULL COMMENT '終止時間',
  `terminated_by` int DEFAULT NULL COMMENT '終止人ID (FK → fun_user.thisid)',
  `terminated_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '終止原因',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '建立時間',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Gaia-X 合約表';

CREATE TABLE `gaia_contract_log` (
  `thisid` int NOT NULL,
  `target_type` varchar(20) NOT NULL COMMENT '目標類型：subscription',
  `target_id` int NOT NULL COMMENT '目標ID（subscription_id）',
  `action` varchar(30) NOT NULL COMMENT '操作類型',
  `action_description` varchar(255) DEFAULT NULL COMMENT '操作說明',
  `snapshot_data` json DEFAULT NULL COMMENT '完整資料快照',
  `change_summary` varchar(255) DEFAULT NULL COMMENT '變更摘要',
  `changed_by` int NOT NULL COMMENT '操作者ID',
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作時間',
  `ip_address` varchar(45) DEFAULT NULL COMMENT '操作者IP',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '操作來源',
  `owner_id` int NOT NULL COMMENT '上游提供者ID',
  `prev_hash` varchar(64) DEFAULT NULL COMMENT '前一筆紀錄的 hash 值',
  `current_hash` varchar(64) NOT NULL COMMENT '當前紀錄的 hash 值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='申請/合約流程操作紀錄表';

CREATE TABLE `gaia_contract_tokens` (
  `thisid` int NOT NULL,
  `jti` varchar(100) NOT NULL COMMENT 'Token 唯一識別碼',
  `contract_id` int NOT NULL COMMENT 'FK → gaia_contracts.thisid',
  `consumer_ent_id` int NOT NULL COMMENT 'FK → res_entities.thisid (消費者組織)',
  `issued_at` datetime NOT NULL COMMENT '簽發時間',
  `effective_at` datetime NOT NULL COMMENT '生效時間',
  `expires_at` datetime NOT NULL COMMENT '過期時間',
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '1=有效, 0=已無效/作廢',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='提貨 Token 生命週期與黑名單表';

CREATE TABLE `gaia_data_products` (
  `thisid` int NOT NULL COMMENT 'PK',
  `resource_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '平台唯一識別碼 (UUID)',
  `resource_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '內部名稱（供內部管理用）',
  `owner_id` int NOT NULL COMMENT '上游提供者ID（權限檢查用）',
  `current_version_id` int NOT NULL COMMENT '當前啟用的版本ID (FK → gaia_product_versions.thisid)',
  `current_version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '當前版本號（冗餘欄位，方便列表顯示）',
  `isopen` tinyint NOT NULL DEFAULT '1' COMMENT '上架: 0=下架, 1=上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人 (FK → fun_user.thisid)',
  `product_image` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '產品圖片 (URL或檔案路徑)',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Gaia-X 資料產品主檔';

CREATE TABLE `gaia_industry` (
  `thisid` int NOT NULL COMMENT '產業分類ID',
  `thisname` varchar(100) NOT NULL COMMENT '產業名稱（顯示用）',
  `thiscode` varchar(50) NOT NULL COMMENT '產業代碼（英文簡稱）',
  `isopen` tinyint DEFAULT '1' COMMENT '上架: 0=下架, 1=上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人 (FK → fun_user.thisid)',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='產業分類表（第1層）';

CREATE TABLE `gaia_jti_blacklist` (
  `thisid` int NOT NULL,
  `jti` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JWT ID',
  `expires_at` datetime NOT NULL COMMENT '過期時間',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='JTI 黑名單（防重放攻擊）';

CREATE TABLE `gaia_machine_log` (
  `thisid` int NOT NULL,
  `target_type` varchar(20) NOT NULL COMMENT '目標類型：machine_account',
  `target_id` int NOT NULL COMMENT '目標ID（machine_account.thisid）',
  `action` varchar(30) NOT NULL COMMENT '操作類型',
  `action_description` varchar(255) DEFAULT NULL COMMENT '操作說明',
  `snapshot_data` json DEFAULT NULL COMMENT '完整資料快照',
  `change_summary` varchar(255) DEFAULT NULL COMMENT '變更摘要',
  `changed_by` int NOT NULL COMMENT '操作者ID',
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作時間',
  `ip_address` varchar(45) DEFAULT NULL COMMENT '操作者IP',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '操作來源',
  `owner_id` int NOT NULL COMMENT '上游提供者ID',
  `prev_hash` varchar(64) DEFAULT NULL COMMENT '前一筆紀錄的 hash 值',
  `current_hash` varchar(64) NOT NULL COMMENT '當前紀錄的 hash 值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='機器帳號操作紀錄表';

CREATE TABLE `gaia_operation_log` (
  `thisid` int NOT NULL COMMENT '紀錄ID (PK)',
  `target_type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '目標類型：product / version / news',
  `target_id` int NOT NULL COMMENT '目標ID（該表的thisid）',
  `action` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作類型',
  `action_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作說明',
  `snapshot_data` json DEFAULT NULL COMMENT '完整資料快照',
  `change_summary` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '變更摘要',
  `changed_by` int NOT NULL COMMENT '操作者ID (FK → fun_user.thisid)',
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作時間',
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作者IP',
  `user_agent` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '操作來源',
  `owner_id` int NOT NULL COMMENT '	上游提供者ID (平台給予)',
  `prev_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '前一筆紀錄的 hash 值（SHA256）',
  `current_hash` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '當前紀錄的 hash 值（SHA256）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Gaia-X操作紀錄表';

CREATE TABLE `gaia_policy_template` (
  `thisid` int NOT NULL COMMENT '範本ID (PK)',
  `thisname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '範本名稱',
  `policy_json` json NOT NULL COMMENT '政策 JSON 內容（ODRL 格式）',
  `isopen` tinyint NOT NULL DEFAULT '1' COMMENT '上架: 0=下架, 1=上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人 (FK → fun_user.thisid)',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='政策範本表';

CREATE TABLE `gaia_product_log` (
  `thisid` int NOT NULL,
  `target_type` varchar(20) NOT NULL COMMENT '目標類型：product / version',
  `target_id` int NOT NULL COMMENT '目標ID（產品ID 或 版本ID）',
  `action` varchar(30) NOT NULL COMMENT '操作類型',
  `action_description` varchar(255) DEFAULT NULL COMMENT '操作說明',
  `snapshot_data` json DEFAULT NULL COMMENT '完整資料快照',
  `change_summary` varchar(255) DEFAULT NULL COMMENT '變更摘要',
  `changed_by` int NOT NULL COMMENT '操作者ID',
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作時間',
  `ip_address` varchar(45) DEFAULT NULL COMMENT '操作者IP',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '操作來源',
  `owner_id` int NOT NULL COMMENT '上游提供者ID',
  `prev_hash` varchar(64) DEFAULT NULL COMMENT '前一筆紀錄的 hash 值',
  `current_hash` varchar(64) NOT NULL COMMENT '當前紀錄的 hash 值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='產品/版本操作紀錄表';

CREATE TABLE `gaia_product_versions` (
  `thisid` int NOT NULL COMMENT 'PK',
  `product_id` int NOT NULL COMMENT 'FK → gaia_data_products',
  `owner_id` int NOT NULL COMMENT '提供者ID',
  `display_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '對外顯示名稱 (dct:title)',
  `industry_id` int NOT NULL COMMENT '主題分類 (FK → gaia_industry) (dcat:theme)',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '產品描述 (dct:description)',
  `version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '版本號 (dcat:version / owl:versionInfo)',
  `keywords` json DEFAULT NULL COMMENT '關鍵字陣列 (dcat:keyword)',
  `data_format` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CSV / JSON / API (dcat:format)',
  `contract_duration_days` int NOT NULL DEFAULT '30' COMMENT '合約有效天數 (ODRL odrl:elapsedTime)',
  `odrl_policy_json` json DEFAULT NULL COMMENT 'ODRL 完整政策（Dataset+Policy）',
  `odrl_policy_simple_json` json DEFAULT NULL COMMENT 'ODRL 單純政策（Policy only）',
  `allowed_actions` json DEFAULT NULL COMMENT '可做什麼 (odrl:permission / odrl:action)',
  `prohibited_actions` json DEFAULT NULL COMMENT '不可做什麼 (odrl:prohibition / odrl:action)',
  `constraints` json DEFAULT NULL COMMENT '限制條件 (odrl:constraint)',
  `data_sensitivity_level` int NOT NULL DEFAULT '0' COMMENT '0-4（決定審核流程）',
  `update_frequency` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '更新頻率 (dcat:accrualPeriodicity)',
  `availability` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '可用性',
  `connector_id` int DEFAULT NULL COMMENT '連結器ID (FK → machine_accounts) (dcat:endpointURL)',
  `published_at` datetime DEFAULT NULL COMMENT '發布時間 (dct:issued)',
  `published_by` int NOT NULL COMMENT '發布者ID (FK → fun_user)',
  `isopen` tinyint NOT NULL DEFAULT '0' COMMENT '0=草稿, 1=確認, 2=目前',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='產品版本表';

CREATE TABLE `gaia_prohibited_actions` (
  `thisid` int NOT NULL,
  `thisname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '行為名稱(禁止下載/禁止商業使用)',
  `thiscode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '行為代碼(no_download/no_commercial_use)',
  `sorting` int DEFAULT '0' COMMENT '排序',
  `isopen` tinyint DEFAULT '1' COMMENT '上架:0=下架,1=上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Gaia-X禁止行為表';

CREATE TABLE `gaia_schema` (
  `thisid` int NOT NULL COMMENT 'Schema ID',
  `industry_id` int NOT NULL COMMENT '所屬產業ID (FK → gaia_industry.thisid)',
  `schemaname` varchar(100) NOT NULL COMMENT 'Schema 名稱（顯示用）',
  `schemacode` varchar(50) NOT NULL COMMENT 'Schema 代碼（英文簡稱）',
  `isopen` tinyint DEFAULT '1' COMMENT '上架: 0=下架, 1=上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人 (FK → fun_user.thisid)',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Schema名稱表（第2層）';

CREATE TABLE `gaia_schema_version` (
  `thisid` int NOT NULL COMMENT '版本ID',
  `schema_id` int NOT NULL COMMENT '所屬Schema ID (FK → gaia_schema.thisid)',
  `version` varchar(50) NOT NULL COMMENT '版本號（使用者自由輸入）',
  `schema_json` json NOT NULL COMMENT '行業 Schema JSON 定義',
  `isopen` tinyint DEFAULT '1' COMMENT '啟用: 0=停用, 1=啟用',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人 (FK → fun_user.thisid)',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Schema版本表（第3層）';

CREATE TABLE `gaia_subscriptions` (
  `thisid` int NOT NULL COMMENT '申請ID',
  `product_id` int NOT NULL COMMENT '產品ID (FK → gaia_data_products.thisid)',
  `version_id` int NOT NULL COMMENT '版本ID (FK → gaia_product_versions.thisid)',
  `applicant_org_id` int NOT NULL COMMENT '申請者組織ID',
  `provider_org_id` int NOT NULL COMMENT '提供者組織ID',
  `applied_actions` json DEFAULT NULL COMMENT '申請的權限 (JSON陣列)',
  `applied_constraints` json DEFAULT NULL COMMENT '申請的限制條件 (JSON物件)',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0=待審核, 1=已核准, 2=已拒絕',
  `review_comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '審核備註/拒絕原因',
  `approved_at` datetime DEFAULT NULL COMMENT '核准時間',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '申請時間',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Gaia-X 申請紀錄表';

CREATE TABLE `gaia_transaction_log` (
  `thisid` int NOT NULL,
  `target_type` varchar(20) NOT NULL COMMENT '目標類型：transaction',
  `target_id` int NOT NULL COMMENT '目標ID（交易ID）',
  `action` varchar(30) NOT NULL COMMENT '操作類型',
  `action_description` varchar(255) DEFAULT NULL COMMENT '操作說明',
  `snapshot_data` json DEFAULT NULL COMMENT '完整資料快照',
  `change_summary` varchar(255) DEFAULT NULL COMMENT '變更摘要',
  `changed_by` int NOT NULL COMMENT '操作者ID',
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作時間',
  `ip_address` varchar(45) DEFAULT NULL COMMENT '操作者IP',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '操作來源',
  `owner_id` int NOT NULL COMMENT '上游提供者ID',
  `prev_hash` varchar(64) DEFAULT NULL COMMENT '前一筆紀錄的 hash 值',
  `current_hash` varchar(64) NOT NULL COMMENT '當前紀錄的 hash 值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='交易紀錄表（暫緩）';

CREATE TABLE `gaia_user_roles` (
  `thisid` int NOT NULL COMMENT '身份ID (PK)',
  `thisname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '身份名稱（政府機關、學術機構、一般使用者）',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人 (FK → fun_user.thisid)',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='身份字典表';

CREATE TABLE `iblink` (
  `thisid` int NOT NULL,
  `ibmain_thisid` int DEFAULT NULL COMMENT 'blockID',
  `tag` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '關鍵字',
  `val` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '連結'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ibmain` (
  `thisid` int NOT NULL,
  `pagename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pageid` int DEFAULT NULL,
  `mainid` int DEFAULT NULL COMMENT '分割框架的ID',
  `maintype` int DEFAULT NULL COMMENT '類別,左=1,右=2',
  `thisrow` int DEFAULT NULL COMMENT '排序',
  `type` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `attr` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `val` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note1` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note2` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `note3` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note4` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note5` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note6` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `ibsub` (
  `thisid` int NOT NULL,
  `ibmain_thisid` int DEFAULT NULL COMMENT 'blockID',
  `thisrow` int DEFAULT NULL COMMENT '行數',
  `col` int DEFAULT NULL COMMENT '列數',
  `attr` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'td or th',
  `val` varchar(4000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '內容',
  `note1` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note2` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note3` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note4` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `note5` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `logtokens` (
  `thisid` int NOT NULL,
  `tk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `timekey` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `machine_accounts` (
  `thisid` int NOT NULL COMMENT '機器帳號ID',
  `thisname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名稱',
  `owner_id` int NOT NULL COMMENT '上游提供者ID',
  `machine_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '機器網址',
  `thispk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `isopen` tinyint DEFAULT '1' COMMENT '啟用狀態',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人',
  `dateadd` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='機器帳號表';

CREATE TABLE `new_list` (
  `thisid` int NOT NULL,
  `typeid` int NOT NULL,
  `thisname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '主標題',
  `thisnamee` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '次標題',
  `thishtml` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT '預留中文編輯軟體內容',
  `seointro` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'SEO描述/頁面上描述',
  `seokey` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'SEO關鍵字',
  `seopic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'SEO圖片',
  `thispic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '上方banner圖片',
  `listpic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '列表圖片',
  `mydate` date NOT NULL,
  `opentime` date DEFAULT NULL,
  `closetime` date DEFAULT NULL,
  `isopen` int NOT NULL DEFAULT '0' COMMENT '上架',
  `isindex` int DEFAULT NULL,
  `dateadd` datetime NOT NULL COMMENT '新增日期',
  `dateupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
  `sorting` int NOT NULL COMMENT '排序',
  `lastadmin` int NOT NULL COMMENT '最後更新者'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `new_type` (
  `thisid` int NOT NULL,
  `thisname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '中文名/SEO標題',
  `thisnamee` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '英文名',
  `seointro` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'SEO描述/頁面上描述',
  `seokey` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'SEO關鍵字',
  `seopic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'SEO圖片',
  `thispic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '上方banner圖片',
  `thispicm` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `listpic` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '列表圖片',
  `isopen` int NOT NULL DEFAULT '0' COMMENT '上架',
  `dateadd` datetime NOT NULL COMMENT '新增日期',
  `dateupdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
  `sorting` int NOT NULL COMMENT '排序',
  `lastadmin` int NOT NULL COMMENT '最後更新者'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `reject_reasons` (
  `thisid` int NOT NULL COMMENT '原因ID (PK)',
  `thisname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '退件原因文字',
  `isopen` tinyint DEFAULT '1' COMMENT '是否啟用: 1=啟用, 0=停用',
  `sorting` int DEFAULT '0' COMMENT '排序',
  `dateadd` datetime DEFAULT NULL COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新者 (FK → fun_user.thisid)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='退件原因清單';

CREATE TABLE `res_entities` (
  `thisid` int NOT NULL COMMENT '供應商ID',
  `thisname` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名稱',
  `thiscode` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '供應商代碼(由平台填寫不能重複)',
  `contact_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '聯絡人',
  `contact_phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '連絡電話',
  `isopen` tinyint DEFAULT '1' COMMENT '上架',
  `lastadmin` int DEFAULT NULL COMMENT '最後更新人',
  `dateadd` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '新增日期',
  `dateupdate` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日期',
  `thisenttype` int NOT NULL DEFAULT '1' COMMENT '組織類別',
  `usertype` int NOT NULL DEFAULT '1' COMMENT '身份',
  `vc_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '憑證VC代碼'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='供應商組織';

CREATE TABLE `sit_pages` (
  `thisid` int NOT NULL,
  `thispage` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '頁面名字',
  `thisname` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '標題 ',
  `seointro` varchar(1000) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'SEO描述/頁面上描述 ',
  `seokey` varchar(300) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'SEO關鍵字 ',
  `seopic` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT 'SEO圖片(分享圖片)',
  `thispic` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `thispicm` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci DEFAULT NULL,
  `lastadmin` int NOT NULL COMMENT '最後更新者',
  `dateadd` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `mydate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `fun_enttype`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `fun_group`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `fun_list`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `fun_log`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `fun_rights`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `fun_user`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_activation_token` (`activation_token`);

ALTER TABLE `gaia_allowed_actions`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `thiscode` (`thiscode`);

ALTER TABLE `gaia_clearing_logs`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_contract_jti` (`contract_id`,`jti`);

ALTER TABLE `gaia_constraints_type`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `thiscode` (`thiscode`);

ALTER TABLE `gaia_contracts`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_application_id` (`application_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_provider_org_id` (`provider_org_id`),
  ADD KEY `idx_applicant_org_id` (`applicant_org_id`),
  ADD KEY `idx_status` (`status`);

ALTER TABLE `gaia_contract_log`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_changed_at` (`changed_at`);

ALTER TABLE `gaia_contract_tokens`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `jti` (`jti`),
  ADD KEY `idx_contract_status` (`contract_id`,`status`),
  ADD KEY `idx_jti` (`jti`);

ALTER TABLE `gaia_data_products`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_owner_id` (`owner_id`),
  ADD KEY `idx_isopen` (`isopen`),
  ADD KEY `idx_current_version_id` (`current_version_id`);

ALTER TABLE `gaia_industry`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `uk_thiscode` (`thiscode`),
  ADD KEY `idx_isopen` (`isopen`);

ALTER TABLE `gaia_jti_blacklist`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `jti` (`jti`),
  ADD KEY `idx_jti` (`jti`),
  ADD KEY `idx_expires_at` (`expires_at`);

ALTER TABLE `gaia_machine_log`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_changed_at` (`changed_at`);

ALTER TABLE `gaia_operation_log`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_changed_at` (`changed_at`),
  ADD KEY `idx_action` (`action`);

ALTER TABLE `gaia_policy_template`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `gaia_product_log`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_changed_at` (`changed_at`);

ALTER TABLE `gaia_product_versions`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_owner_id` (`owner_id`),
  ADD KEY `idx_industry_id` (`industry_id`),
  ADD KEY `idx_connector_id` (`connector_id`),
  ADD KEY `idx_isopen` (`isopen`),
  ADD KEY `idx_published_at` (`published_at`);

ALTER TABLE `gaia_prohibited_actions`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `thiscode` (`thiscode`);

ALTER TABLE `gaia_schema`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `uk_industry_schemacode` (`industry_id`,`schemacode`),
  ADD KEY `idx_industry_id` (`industry_id`),
  ADD KEY `idx_isopen` (`isopen`);

ALTER TABLE `gaia_schema_version`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `uk_schema_version` (`schema_id`,`version`),
  ADD KEY `idx_schema_id` (`schema_id`),
  ADD KEY `idx_isopen` (`isopen`);

ALTER TABLE `gaia_subscriptions`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_applicant_org_id` (`applicant_org_id`),
  ADD KEY `idx_provider_org_id` (`provider_org_id`),
  ADD KEY `idx_status` (`status`);

ALTER TABLE `gaia_transaction_log`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_target` (`target_type`,`target_id`),
  ADD KEY `idx_owner` (`owner_id`),
  ADD KEY `idx_changed_at` (`changed_at`);

ALTER TABLE `gaia_user_roles`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `iblink`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `ibmain`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `ibsub`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `logtokens`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `machine_accounts`
  ADD PRIMARY KEY (`thisid`),
  ADD KEY `idx_owner_id` (`owner_id`),
  ADD KEY `idx_isopen` (`isopen`);

ALTER TABLE `new_list`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `new_type`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `reject_reasons`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `res_entities`
  ADD PRIMARY KEY (`thisid`),
  ADD UNIQUE KEY `thiscode` (`thiscode`);

ALTER TABLE `sit_pages`
  ADD PRIMARY KEY (`thisid`);

ALTER TABLE `fun_enttype`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `fun_group`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

ALTER TABLE `fun_list`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

ALTER TABLE `fun_log`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '主鍵';

ALTER TABLE `fun_rights`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

ALTER TABLE `fun_user`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

ALTER TABLE `gaia_allowed_actions`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `gaia_clearing_logs`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=398;

ALTER TABLE `gaia_constraints_type`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

ALTER TABLE `gaia_contracts`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '合約ID', AUTO_INCREMENT=100046;

ALTER TABLE `gaia_contract_log`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

ALTER TABLE `gaia_contract_tokens`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=515;

ALTER TABLE `gaia_data_products`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT 'PK', AUTO_INCREMENT=100027;

ALTER TABLE `gaia_industry`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '產業分類ID', AUTO_INCREMENT=10004;

ALTER TABLE `gaia_jti_blacklist`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `gaia_machine_log`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

ALTER TABLE `gaia_operation_log`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '紀錄ID (PK)', AUTO_INCREMENT=183;

ALTER TABLE `gaia_policy_template`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '範本ID (PK)', AUTO_INCREMENT=4;

ALTER TABLE `gaia_product_log`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

ALTER TABLE `gaia_product_versions`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT 'PK', AUTO_INCREMENT=100027;

ALTER TABLE `gaia_prohibited_actions`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `gaia_schema`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT 'Schema ID', AUTO_INCREMENT=10010;

ALTER TABLE `gaia_schema_version`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '版本ID', AUTO_INCREMENT=10015;

ALTER TABLE `gaia_subscriptions`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '申請ID', AUTO_INCREMENT=100065;

ALTER TABLE `gaia_transaction_log`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `gaia_user_roles`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '身份ID (PK)', AUTO_INCREMENT=4;

ALTER TABLE `iblink`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `ibmain`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

ALTER TABLE `ibsub`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `logtokens`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1897;

ALTER TABLE `machine_accounts`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '機器帳號ID', AUTO_INCREMENT=100038;

ALTER TABLE `new_list`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

ALTER TABLE `new_type`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `reject_reasons`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '原因ID (PK)', AUTO_INCREMENT=7;

ALTER TABLE `res_entities`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT COMMENT '供應商ID', AUTO_INCREMENT=100028;

ALTER TABLE `sit_pages`
  MODIFY `thisid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `gaia_product_versions`
  ADD CONSTRAINT `fk_version_connector` FOREIGN KEY (`connector_id`) REFERENCES `machine_accounts` (`thisid`),
  ADD CONSTRAINT `fk_version_product` FOREIGN KEY (`product_id`) REFERENCES `gaia_data_products` (`thisid`);

ALTER TABLE `gaia_schema`
  ADD CONSTRAINT `gaia_schema_ibfk_1` FOREIGN KEY (`industry_id`) REFERENCES `gaia_industry` (`thisid`) ON DELETE CASCADE;

ALTER TABLE `gaia_schema_version`
  ADD CONSTRAINT `gaia_schema_version_ibfk_1` FOREIGN KEY (`schema_id`) REFERENCES `gaia_schema` (`thisid`) ON DELETE CASCADE;

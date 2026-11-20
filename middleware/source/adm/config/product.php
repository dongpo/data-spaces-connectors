<?php
function getContractDetail($mypage, $viewType) {
    global $imgurl;
    
    $typeid = $GLOBALS['typeid'] ?? 0;
    $myOrgId = checkUserOrganization();
    
    //檢查契約是否存在
    $contract = share_getinfo('gaia_contracts', 'thisid', $typeid);
    if (!$contract) {
        echo "<div class='error-msg'>契約不存在</div>";
        echo "<a href='?tf=".htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."&job=getContractList' class='gray-btn'>← 返回</a>";
        return;
    }
    //權限檢查
    if ($viewType == 'incoming' && $contract['applicant_org_id'] != $myOrgId) {
        echo "<div class='error-msg'>您沒有權限查看此契約</div>";
        echo "<a href='?tf=".htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."&job=getContractList' class='gray-btn'>← 返回</a>";
        return;
    }
    if ($viewType == 'outgoing' && $contract['provider_org_id'] != $myOrgId) {
        echo "<div class='error-msg'>您沒有權限查看此契約</div>";
        echo "<a href='?tf=".htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."&job=getContractList' class='gray-btn'>← 返回</a>";
        return;
    }
    
    // 發布資料名稱（從版本檔抓 display_name）
    $versionData = share_getinfo('gaia_product_versions', 'thisid', $contract['version_id']);
    $product = share_getinfo('gaia_data_products', 'thisid', $contract['product_id']);
    
    // 提供者名稱
    $provider = share_getinfo('res_entities', 'thisid', $contract['provider_org_id']);
    $applicant = share_getinfo('res_entities', 'thisid', $contract['applicant_org_id']);
    
    // 解析 JSON
    $allowedActions = json_decode($contract['allowed_actions'] ?? '', true) ?: [];
    $constraints = json_decode($contract['constraints'] ?? '', true) ?: [];
    $prohibitedActions = json_decode($contract['prohibited_actions'] ?? '', true) ?: [];
    $odrlAgreement = json_decode($contract['odrl_agreement_json'] ?? '', true);
    
    // 取得可做什麼的名稱對照
    $allowedList = share_gettable("gaia_allowed_actions WHERE isopen = 1 ORDER BY sorting");
    $allowedMap = [];
    foreach ($allowedList as $row) {
        $allowedMap[$row['thiscode']] = $row['thisname'];
    }
    
    // 取得不可做什麼的名稱對照
    $prohibitedList = share_gettable("gaia_prohibited_actions WHERE isopen = 1 ORDER BY sorting");
    $prohibitedMap = [];
    foreach ($prohibitedList as $row) {
        $prohibitedMap[$row['thiscode']] = $row['thisname'];
    }
    $statusText = '';
    $statusClass = '';
    if ($contract['status'] == 1) {
        $statusText = '🟢 生效中';
        $statusClass = 'status-approved';
    } elseif ($contract['status'] == 2) {
        $statusText = '🔴 已過期';
        $statusClass = 'status-rejected';
    } elseif ($contract['status'] == 3) {
        $statusText = '⚪ 暫停中';
        $statusClass = 'status-cancelled';
    } else {
        $statusText = '❓ 未知';
        $statusClass = 'status-none';
    }
    
    $productName = !empty($versionData['display_name']) ? $versionData['display_name'] : ($product['resource_name'] ?? '已刪除');
    $providerName = $provider['thisname'] ?? '未知';
    $applicantName = $applicant['thisname'] ?? '未知';
    
    echo "<div class='contract-detail'>";   

    // 標題
    echo "<h2 class='form-title'>契約詳情</h2>";
    echo "<hr class='form-hr'>";
    
    // 基本資訊
    echo "<TABLE class='formtable'>";
    echo "<tr><th colspan='2' class='section-title'>基本資訊</th></tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>契約編號</th>";
    echo "<td class='input-col'>CT-" . str_pad(htmlspecialchars($contract['thisid'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), 6, '0', STR_PAD_LEFT) . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>狀態</th>";
    echo "<td class='input-col'><span class='status-badge " . htmlspecialchars($statusClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>" . htmlspecialchars($statusText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</span></td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>發布資料名稱</th>";
    echo "<td class='input-col'>" . htmlspecialchars($productName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>版本</th>";
    echo "<td class='input-col'>" . htmlspecialchars($versionData['version'] ?? '-', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>提供者</th>";
    echo "<td class='input-col'>" . htmlspecialchars($providerName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>申請者</th>";
    echo "<td class='input-col'>" . htmlspecialchars($applicantName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>契約期限</th>";
    echo "<td class='input-col'>";
    echo htmlspecialchars(date('Y-m-d', strtotime($contract['effective_at'])), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " ~ " . htmlspecialchars(date('Y-m-d', strtotime($contract['expired_at'])), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo " <span class='hint-text'>（共 " . htmlspecialchars($contract['contract_duration_days'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " 天）</span>";
    echo "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>簽署時間</th>";
    echo "<td class='input-col'>" . htmlspecialchars($contract['created_at'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
    echo "</tr>";
    
    echo "</TABLE>";
    
    // 權限與限制
    echo "<TABLE class='formtable'>";
    echo "<tr><th colspan='2' class='section-title'> 權限與限制</th></tr>";
    
    // 可做什麼
    echo "<tr>";
    echo "<th class='label-col'>可做什麼<br><span class='field-en'>Allowed Actions</span></th>";
    echo "<td class='input-col'>";
    if (!empty($allowedActions)) {
        echo "<div class='action-tags'>";
        foreach ($allowedActions as $actionCode) {
            $actionName = $allowedMap[$actionCode] ?? $actionCode;
            echo "<span class='action-tag allowed'>" . htmlspecialchars($actionName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</span>";
        }
        echo "</div>";
    } else {
        echo "<span class='text-muted'>未設定</span>";
    }
    echo "</td>";
    echo "</tr>";
    
    // 不可做什麼
    echo "<tr>";
    echo "<th class='label-col'>不可做什麼<br><span class='field-en'>Prohibited Actions</span></th>";
    echo "<td class='input-col'>";
    if (!empty($prohibitedActions)) {
        echo "<div class='action-tags'>";
        foreach ($prohibitedActions as $actionCode) {
            $actionName = $prohibitedMap[$actionCode] ?? $actionCode;
            echo "<span class='action-tag prohibited'>" . htmlspecialchars($actionName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</span>";
        }
        echo "</div>";
    } else {
        echo "<span class='text-muted'>未設定</span>";
    }
    echo "</td>";
    echo "</tr>";
    
    // 限制條件
    echo "<tr>";
    echo "<th class='label-col'>限制條件<br><span class='field-en'>Constraints</span></th>";
    echo "<td class='input-col'>";
    if (!empty($constraints)) {
        echo "<div class='constraint-tags-display'>";
        foreach ($constraints as $key => $value) {
            $displayValue = is_array($value) ? implode(', ', $value) : 
                           (is_bool($value) ? ($value ? '✅ 啟用' : '❌ 停用') : $value);
            echo "<span class='constraint-tag'>";
            echo "<span class='tag-key'>" . htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "：</span>";
            echo "<span class='tag-value'>" . htmlspecialchars($displayValue, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</span>";
            echo "</span>";
        }
        echo "</div>";
    } else {
        echo "<span class='text-muted'>未設定</span>";
    }
    echo "</td>";
    echo "</tr>";
    
    echo "</TABLE>";
    
    // ODRL Agreement（可展開）
    if (!empty($odrlAgreement)) {
        echo "<TABLE class='formtable'>";
        echo "<tr><th colspan='2' class='section-title'> ODRL Agreement</th></tr>";
        echo "<tr>";
        echo "<td colspan='2' class='input-col'>";
        echo "<div class='odrl-json-wrapper'>";
        echo "<pre class='odrl-json'>" . htmlspecialchars(json_encode($odrlAgreement, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
        echo "</div>";
        echo "</td>";
        echo "</tr>";
        echo "</TABLE>";
    }
    
    echo "</div>"; // contract-detail
}

function getContractList($mypage, $viewType) {
    global $imgurl;
    
    $myOrgId = checkUserOrganization();

    if ($viewType == 'incoming') {
        $where = " c.applicant_org_id = '" . addslashes($myOrgId) . "' ";
        $title = " 契約管理";
        $subtitle = "<a href='資料共享協議條款（草案）_v3.1.docx' class='blue-btn'>資料共享協議條款</a><br>查看你已完成申請及使用的契約<br>契約經雙方簽署生效後即鎖定，其Hash 值同步寫入稽核日誌（Audit Log）；雙方可自行點選「契約」下載文本契約留存";
        $showProvider = true;
        $showApplicant = false;
        $emptyMsg = "您還沒有任何契約，<a href='product_market.html?tf=" . htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "&job=getContact1' class='green-link'>前往發布資料市集</a> 申請使用發布資料。";
    } else {
        $where = " c.provider_org_id = '" . addslashes($myOrgId) . "' ";
        $title = " 訂閱管理";
        $subtitle = "查看誰訂閱了您的發布資料";
        $showProvider = false;
        $showApplicant = true;
        $emptyMsg = "目前還沒有任何人訂閱您的發布資料。";
    }    
    $statusFilter = $_GET['status'] ?? $_POST['status'] ?? '';
    $keyword = $_GET['keyword'] ?? $_POST['keyword'] ?? '';
    
    if ($statusFilter !== '') {
        $where .= " AND c.status = '" . addslashes($statusFilter) . "' ";
    }
    
    if (!empty($keyword)) {
        $where .= " AND (v.display_name LIKE '%" . addslashes($keyword) . "%' OR r.thisname LIKE '%" . addslashes($keyword) . "%' OR a.thisname LIKE '%" . addslashes($keyword) . "%') ";
    }
	$contractList = share_getfree("SELECT c.thisid, c.contract_duration_days, c.effective_at, c.expired_at, c.status, c.allowed_actions, c.constraints, c.prohibited_actions, c.odrl_agreement_json, c.created_at, v.display_name, v.version, p.thisid AS product_id, p.resource_name, r.thisname AS provider_name, a.thisname AS applicant_name FROM gaia_contracts c LEFT JOIN gaia_data_products p ON c.product_id = p.thisid LEFT JOIN gaia_product_versions v ON c.version_id = v.thisid LEFT JOIN res_entities r ON c.provider_org_id = r.thisid LEFT JOIN res_entities a ON c.applicant_org_id = a.thisid WHERE " . $where . " ORDER BY CASE WHEN c.status = 1 THEN 0 ELSE 1 END, c.created_at DESC");
    
    echo "<div class='search-area'>";
    echo "<div class='search-left'>";
    echo "<div class='search-item search-keyword'>";
    echo "<label>🔍 關鍵字</label>";
    echo "<input type='text' name='keyword' value='" . htmlspecialchars($keyword, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "' class='input-full' placeholder='搜尋發布資料名稱或公司名稱...' form='searchForm'>";
    echo "</div>";
    echo "<div class='search-item'>";
    echo "<label>📊 狀態</label>";
    echo "<select name='status' form='searchForm'>";
    echo "<option value=''>全部</option>";
    echo "<option value='1' " . ($statusFilter === '1' ? 'selected' : '') . ">🟢 生效中</option>";
    echo "<option value='2' " . ($statusFilter === '2' ? 'selected' : '') . ">🔴 已過期</option>";
    echo "<option value='3' " . ($statusFilter === '3' ? 'selected' : '') . ">⚪ 暫停中</option>";
    echo "</select>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div class='search-right'>";
    echo "<form action='' method='get' id='searchForm'>";
    echo "<input type='hidden' name='tf' value='" . htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>";
    echo "<input type='hidden' name='job' value='getContractList'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 搜尋</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
    
    echo "<h2>" . htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</h2>";
    echo "<p class='subtitle'>" . $subtitle . "</p>";
    echo "<hr>";
    
    if (!empty($contractList)) {
        echo "<div class='table-wrapper'>";
        echo "<TABLE class='formtable'>";
        echo "<tr>";
        echo "<th width='15%'>契約編號</th>";
        echo "<th width='22%'>發布資料名稱</th>";
        if ($showProvider) {
            echo "<th width='15%'>提供者</th>";
        }
        if ($showApplicant) {
            echo "<th width='15%'>申請者</th>";
        }
        echo "<th width='22%'>契約期限</th>";
        echo "<th width='10%'>狀態</th>";
        echo "<th width='16%'>操作</th>";
        echo "</tr>";
        
        foreach ($contractList as $row) {
            $statusText = '';
            $statusClass = '';
            
            if ($row['status'] == 1) {
                $statusText = '🟢 生效中';
                $statusClass = 'status-approved';
            } elseif ($row['status'] == 2) {
                $statusText = '🔴 已過期';
                $statusClass = 'status-rejected';
            } elseif ($row['status'] == 3) {
                $statusText = '⚪ 暫停中';
                $statusClass = 'status-cancelled';
            } else {
                $statusText = '❓ 未知';
                $statusClass = 'status-none';
            }

            $productName = !empty($row['display_name']) ? $row['display_name'] : ($row['resource_name'] ?? '已刪除');
            $providerName = $row['provider_name'] ?? '未知';
            $applicantName = $row['applicant_name'] ?? '未知';

            $effectiveDate = date('Y-m-d', strtotime($row['effective_at']));
            $expiredDate = date('Y-m-d', strtotime($row['expired_at']));
            $durationDisplay = $effectiveDate . ' ~ ' . $expiredDate;
            
            echo "<tr>";
            echo "<td>CT-" . str_pad(htmlspecialchars($row['thisid'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), 6, '0', STR_PAD_LEFT) . "</td>";
            echo "<td class='tleft'>" . htmlspecialchars($productName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            
            if ($showProvider) {
                echo "<td>" . htmlspecialchars($providerName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            }
            if ($showApplicant) {
                echo "<td>" . htmlspecialchars($applicantName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            }
            
            echo "<td>" . htmlspecialchars($durationDisplay, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</td>";
            echo "<td><span class='status-badge " . htmlspecialchars($statusClass, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>" . htmlspecialchars($statusText, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</span></td>";
            echo "<td>";
            echo "<div class='btn-group'>";
            echo "<a href='?tf=" . htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "&job=getdetaillist1&typeid=" . htmlspecialchars($row['thisid'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "' class='blue-btn btn-sm'> 查看</a>";
			echo "<a href='?tf=" . htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "&job=monthlyAudit&typeid=" . htmlspecialchars($row['thisid'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "' class='orange-btn' > 稽核</a>";	 
			if ($showProvider) {
				echo "<a href='資料共享協議條款（草案）_v3.1.docx' class='green-btn'>契約</a>";	
			}
            if ($showApplicant) {
                if ($row['status'] == 1) {
                    echo "<a href='?tf=" . htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "&job=toggleStatus&id=" . htmlspecialchars($row['thisid'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "&st=3' class='gray-btn btn-sm action-confirm' data-msg='確定要停用此訂閱合約嗎？'>停用</a>";
                } elseif ($row['status'] == 3) {
                    echo "<a href='?tf=" . htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "&job=toggleStatus&id=" . htmlspecialchars($row['thisid'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "&st=1' class='green-btn btn-sm action-confirm' data-msg='確定要重新啟用此訂閱合約嗎？'>重新啟用</a>";
                }
            }
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</TABLE>";
        echo "</div>";
        echo "<div class='table-footer'>顯示 " . count($contractList) . " 筆</div>";
    } else {
        echo "<div class='empty-state'>";
        echo "<div class='empty-icon'></div>";
        echo "<h3>尚無契約紀錄</h3>";
        echo "<p>" . $emptyMsg . "</p>";
        echo "</div>";
    }
}

function convertFrequency($code) {
    $map = ['PT1S'=>'即時', 'PT5M'=>'每5分鐘', 'PT10M'=>'每10分鐘',
            'PT15M'=>'每15分鐘', 'PT30M'=>'每30分鐘', 'PT1H'=>'每小時',
            'P1D'=>'每日', 'P1W'=>'每週', 'P1M'=>'每月',
            'P3M'=>'每季', 'P1Y'=>'每年'];
    return $map[$code] ?? $code;
}

function generateUUID() { 
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

function checkUserOrganization() {
    $userInfo = share_getinfo("fun_user", "thisid", $_SESSION['companyid']);
    if (!$userInfo) {
        session_destroy();
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('使用者不存在，請重新登入'); window.location.href='login.php';</script>";
        exit;
    }
    
    $_SESSION['cthisentid'] = $userInfo['thisentid'];
    
    $orgInfo = share_getinfo("res_entities", "thisid", $userInfo['thisentid']);
    if (!$orgInfo) {
        session_destroy();
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('組織資料不存在，請重新登入'); window.location.href='login.php';</script>";
        exit;
    }
    
    $orgType = share_getinfo("fun_enttype", "thisid", $orgInfo['thisenttype']);
    if (!$orgType) {
        session_destroy();
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('類別資料不存在，請重新登入'); window.location.href='login.php';</script>";
        exit;
    }
    $_SESSION['org_type'] = $orgType['thisname'];
    $_SESSION['org_type_id'] = $orgInfo['thisenttype'];
    
    $userRole = share_getinfo("gaia_user_roles", "thisid", $orgInfo['usertype']);
    if (!$userRole) {
        session_destroy();
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('身份資料不存在，請重新登入'); window.location.href='login.php';</script>";
        exit;
    }
    $_SESSION['user_role'] = $userRole['thisname'];
    $_SESSION['user_role_id'] = $orgInfo['usertype'];
    
    return $userInfo['thisentid'];
}

function checkPagePermission($page) {
    checkUserOrganization();    
    $orgType = $_SESSION['org_type'] ?? '';
    $issuper = $_SESSION['issuper'] ?? '';
    
    $pagePermissions = [
        'my_products' => ['資料提供者', '兩者皆有'],
        'product_market' => ['資料使用者', '兩者皆有'],
        'my_applications' => ['資料使用者', '兩者皆有'],
        'review_management' => ['資料提供者', '兩者皆有'],
        'machine_accounts' => [
            'allowed_org' => ['資料提供者', '資料使用者', '兩者皆有'],  
            'issuper' => ['2', '0']  
        ],
        'sub_accounts' => [
            'allowed_org' => ['資料提供者', '資料使用者', '兩者皆有'],
            'issuper' => ['2', '0']  
        ],
    ];
    
    if (!isset($pagePermissions[$page])) {
        return true;
    }
    
    $perm = $pagePermissions[$page];
    
    if (isset($perm['allowed_org']) && !in_array($orgType, $perm['allowed_org'])) {
        header("Location: member.html");
        exit;
    }
    
    if (isset($perm['issuper']) && !in_array($issuper, $perm['issuper'])) {
        header("Location: member.html");
        exit;
    }
    
    return true;
}

function checkDataExists($mytable, $thisid) {
    $me = share_getinfo($mytable, "thisid", $thisid);
    if (!$me) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('資料不存在'); history.back();</script>";
        exit;
    }
    return $me;
}

function writeGaiaOperationLog($targetType, $targetId, $ownerId, $action, $actionDescription, $snapshotData, $changeSummary, $changedBy) {
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
   
    $tableMap = [
        'product'         => 'gaia_product_log',
        'version'         => 'gaia_product_log',
        'subscription'    => 'gaia_contract_log',
        'machine_account' => 'gaia_machine_log',
    ];
    
    $table = $tableMap[$targetType] ?? 'gaia_operation_log';
    
    if ($table === 'gaia_operation_log') {
        error_log("⚠️ writeGaiaOperationLog: 未知的 target_type='{$targetType}'，寫入 gaia_operation_log");
    }
    $snapshotJson = 'NULL';
    if (!empty($snapshotData)) {
        if (is_array($snapshotData)) {
            $filteredData = [];
            foreach ($snapshotData as $key => $value) {
                if (is_string($key)) {
                    $filteredData[$key] = $value;
                }
            }
            $snapshotJson = "'" . addslashes(json_encode($filteredData, JSON_UNESCAPED_UNICODE)) . "'";
        } else {
            $snapshotJson = "'" . addslashes(json_encode($snapshotData, JSON_UNESCAPED_UNICODE)) . "'";
        }
    }
 
    $lastRecord = share_getfree("SELECT current_hash FROM {$table} ORDER BY thisid DESC LIMIT 1");
    
    $prevHash = 'NULL';
    if (!empty($lastRecord)) {
        $prevHash = "'" . addslashes($lastRecord[0]['current_hash']) . "'";
    }
    $hashData = $targetType . $targetId . $ownerId . $action . $actionDescription . 
                (is_array($snapshotData) ? json_encode($snapshotData) : '') . 
                $changeSummary . $changedBy . date('Y-m-d H:i:s') . $ipAddress . $userAgent;
    
    if (!empty($lastRecord)) {
        $hashData .= $lastRecord[0]['current_hash'];
    }
    
    $currentHash = hash('sha256', $hashData);
	$result = share_insert($table, 'target_type, target_id, owner_id, action, action_description, snapshot_data, change_summary, changed_by, changed_at, ip_address, user_agent, prev_hash, current_hash', "'" . addslashes($targetType) . "', '" . addslashes($targetId) . "', '" . addslashes($ownerId) . "', '" . addslashes($action) . "', '" . addslashes($actionDescription) . "', " . $snapshotJson . ", '" . addslashes($changeSummary) . "', '" . addslashes($changedBy) . "', NOW(), '" . addslashes($ipAddress) . "', '" . addslashes($userAgent) . "', " . $prevHash . ", '" . addslashes($currentHash) . "'");
    
    return $result;
}

function showOperationLogList($targetType, $targetId, $limit = 50) {
    $html = '';
    $userId = $_SESSION['companyid'] ?? 0;
    $userOrgId = checkUserOrganization();
    $tableMap = [
        'product'         => 'gaia_product_log',
        'version'         => 'gaia_product_log',
        'subscription'    => 'gaia_contract_log',
        'machine_account' => 'gaia_machine_log',
    ];
    
    $table = $tableMap[$targetType] ?? 'gaia_operation_log';
    $hasPermission = false;
    $targetData = null;
    $versionIds = [];
    
    switch ($targetType) {
        case 'product':
            $targetData = checkDataExists('gaia_data_products', $targetId);
            if ($targetData && $targetData['owner_id'] == $userOrgId) {
                $hasPermission = true;
                $versions = share_gettable("gaia_product_versions WHERE product_id = '" . addslashes($targetId) . "'");
                if ($versions) {
                    foreach ($versions as $v) {
                        $versionIds[] = $v['thisid'];
                    }
                }
            }
            break;
            
        case 'version':
            $versionData = share_getinfo('gaia_product_versions', 'thisid', $targetId);
            if ($versionData) {
                $productData = checkDataExists('gaia_data_products', $versionData['product_id']);
                if ($productData && $productData['owner_id'] == $userOrgId) {
                    $hasPermission = true;
                    $targetData = $versionData;
                }
            }
            break;
            
        default:
            return '<div class="log-empty">❌ 不支援的目標類型</div>';
    }
    
    if (!$hasPermission) {
        return '<div class="log-empty log-error">⛔ 您沒有權限查看此操作紀錄</div>';
    }
    $where = "target_type = '" . addslashes($targetType) . "' AND target_id = '" . addslashes($targetId) . "'";
    
    if ($targetType == 'product' && !empty($versionIds)) {
        $versionIdsStr = implode("','", array_map('addslashes', $versionIds));
        $where .= " OR (target_type = 'version' AND target_id IN ('$versionIdsStr'))";
    }
    
    $sql = "{$table} WHERE {$where} ORDER BY changed_at DESC LIMIT " . intval($limit);
    
    $logs = share_gettable($sql);
    
    if (empty($logs)) {
        return '<div class="log-empty">尚無操作紀錄</div>';
    }
    $html .= '<div class="log-table-wrapper">';
    $html .= '<table class="log-table" id="logTable">';
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '  <th class="log-col-no">#</th>';
    $html .= '  <th class="log-col-action">操作類型</th>';
    $html .= '  <th class="log-col-desc">操作說明</th>';
    $html .= '  <th class="log-col-summary">變更摘要</th>';
    $html .= '  <th class="log-col-user">操作者</th>';
    $html .= '  <th class="log-col-time">操作時間</th>';
    $html .= '  <th class="log-col-toggle">展開</th>';
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    
    $actionLabels = [
        'CREATE_PRODUCT' => '<span class="log-badge log-badge-create">新增發布資料</span>',
        'UPDATE_PRODUCT' => '<span class="log-badge log-badge-update">更新發布資料</span>',
        'DELETE_PRODUCT' => '<span class="log-badge log-badge-delete">刪除發布資料</span>',
        'PUBLISH_PRODUCT' => '<span class="log-badge log-badge-publish">發布資料上架</span>',
        'UNPUBLISH_PRODUCT' => '<span class="log-badge log-badge-unpublish">發布資料下架</span>',
        'CREATE_VERSION' => '<span class="log-badge log-badge-create">新增版本</span>',
        'UPDATE_VERSION' => '<span class="log-badge log-badge-update">更新版本</span>',
        'PUBLISH_VERSION' => '<span class="log-badge log-badge-publish">版本上架</span>',
        'UNPUBLISH_VERSION' => '<span class="log-badge log-badge-unpublish">版本下架</span>',
        'SWITCH_VERSION' => '<span class="log-badge log-badge-switch">切換版本</span>'
    ];
    
    $no = 1;
    foreach ($logs as $log) {
        $logId = $log['thisid'] ?? 0;
        if (empty($logId)) {
            continue;
        }
        $actionLabel = $actionLabels[$log['action']] ?? '<span class="log-badge log-badge-default">' . htmlspecialchars($log['action'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</span>';
        $userName = 'ID:' . $log['changed_by'];
        $userInfo = share_getinfo('fun_user', 'thisid', $log['changed_by']);
        if ($userInfo) {
            $userName = $userInfo['thisname'] ?? $userName;
        }
        $changeSummary = $log['change_summary'] ?? '';
        if ($log['action'] == 'SWITCH_VERSION' && !empty($log['snapshot_data'])) {
            $snapshot = json_decode($log['snapshot_data'], true);
            if ($snapshot && isset($snapshot['from_version']) && isset($snapshot['to_version'])) {
                $changeSummary = 'v' . $snapshot['from_version'] . ' → v' . $snapshot['to_version'];
            }
        }
        
        if (mb_strlen($changeSummary) > 40) {
            $changeSummary = mb_substr($changeSummary, 0, 40) . '...';
        }
        
        $changedAt = date('Y-m-d H:i:s', strtotime($log['changed_at']));
        $snapshotHtml = '';
        
        if (!empty($log['snapshot_data'])) {
            $snapshot = json_decode($log['snapshot_data'], true);
            
            if ($snapshot) {
                array_walk_recursive($snapshot, function(&$value) {
                    if (is_string($value)) {
                        $cleaned = trim($value, "'");
                        $decoded = json_decode(stripslashes($cleaned), true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $value = $decoded;
                        }
                    }
                });

                $snapshotHtml = '<pre class="log-snapshot">' . htmlspecialchars(json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</pre>';
            }
        }

        $detailHtml = '';
        $detailHtml .= '<div class="log-detail-inner">';
        $detailHtml .= '<table class="log-detail-table">';
        $targetTypeLabel = $log['target_type'];
        if ($log['target_type'] == 'version') {
            $versionInfo = share_getinfo('gaia_product_versions', 'thisid', $log['target_id']);
            if ($versionInfo) {
                $versionNumber = $versionInfo['version_number'] ?? '未知';
                $targetTypeLabel = '版本 (v' . $versionNumber . ')';
            } else {
                $targetTypeLabel = '版本 (已刪除)';
            }
        } elseif ($log['target_type'] == 'product') {
            $targetTypeLabel = '發布資料 (主檔)';
        }
        
        $detailHtml .= '<tr><td class="log-detail-label">目標類型</td><td class="log-detail-value">' . htmlspecialchars($targetTypeLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td></tr>';
        $detailHtml .= '<tr><td class="log-detail-label">目標ID</td><td class="log-detail-value">' . htmlspecialchars($log['target_id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td></tr>';
        
        if (!empty($log['ip_address'])) {
            $detailHtml .= '<tr><td class="log-detail-label">IP位址</td><td class="log-detail-value">' . htmlspecialchars($log['ip_address'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td></tr>';
        }
        if (!empty($log['user_agent'])) {
            $detailHtml .= '<tr><td class="log-detail-label">瀏覽器</td><td class="log-detail-value log-detail-ua">' . htmlspecialchars($log['user_agent'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td></tr>';
        }
        if ($snapshotHtml) {
            $detailHtml .= '<tr><td class="log-detail-label">資料內容</td><td class="log-detail-value">' . $snapshotHtml . '</td></tr>';
        }
        $detailHtml .= '</table>';
        $detailHtml .= '</div>';

        $html .= '<tr id="logRow_' . htmlspecialchars($logId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">';
        $html .= '  <td class="log-col-no">' . htmlspecialchars($no, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        $html .= '  <td class="log-col-action">' . $actionLabel . '</td>';
        $html .= '  <td class="log-col-desc">' . htmlspecialchars($log['action_description'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        $html .= '  <td class="log-col-summary">' . htmlspecialchars($changeSummary, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        $html .= '  <td class="log-col-user">' . htmlspecialchars($userName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        $html .= '  <td class="log-col-time">' . htmlspecialchars($changedAt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        $html .= '  <td class="log-col-toggle">';
        $html .= '    <button class="log-toggle-btn" data-id="' . htmlspecialchars($logId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" id="btn_' . htmlspecialchars($logId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"> 展開</button>';
        $html .= '  </td>';
        $html .= '</tr>';

        $html .= '<tr id="logDetail_' . htmlspecialchars($logId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" class="log-detail-row" style="display:none;">';
        $html .= '  <td colspan="7" class="log-detail-cell">';
        $html .= '    <div class="log-detail-content">';
        $html .= $detailHtml;
        $html .= '    </div>';
        $html .= '  </td>';
        $html .= '</tr>';
        
        $no++;
    }
    
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';
    
    $html .= '<div class="log-footer">📊 共 ' . count($logs) . ' 筆操作紀錄</div>';
    
    $html .= '
    <script nonce="' . htmlspecialchars($_SESSION['nonce'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">
    document.addEventListener("DOMContentLoaded", function() {
        var table = document.getElementById("logTable");
        if (!table) return;
        
        table.addEventListener("click", function(e) {
            var btn = e.target.closest(".log-toggle-btn");
            if (!btn) return;
            
            var logId = btn.getAttribute("data-id");
            if (!logId) return;
            
            var detailRow = document.getElementById("logDetail_" + logId);
            if (!detailRow) return;
            
            if (detailRow.style.display === "none" || detailRow.style.display === "") {
                detailRow.style.display = "table-row";
                btn.textContent = " 收起";
            } else {
                detailRow.style.display = "none";
                btn.textContent = " 展開";
            }
        });
    });
    </script>
    ';
    
    return $html;
}

function buildAgreement($productId, $applicantOrgId, $providerOrgId, $applyId, $versionData, $appliedActions, $appliedConstraints, $domain) {
    $odrlActions = [];
    foreach ($appliedActions as $action) {
        $odrlActions[] = 'odrl:' . strtolower($action);
    }
    
    $constraints = [];
    foreach ($appliedConstraints as $key => $value) {
        if ($key === 'daily_limit') {
            $constraints[] = [
                'leftOperand' => 'odrl:count',
                'operator' => 'odrl:lteq',
                'rightOperand' => intval($value),
                'unit' => 'odrl:day'
            ];
        } elseif ($key === 'max_concurrent') {
            $constraints[] = [
                'leftOperand' => 'odrl:count',
                'operator' => 'odrl:lteq',
                'rightOperand' => intval($value),
                'unit' => 'odrl:concurrent'
            ];
        }
    }
    
    $constraints[] = [
        'leftOperand' => 'odrl:elapsedTime',
        'operator' => 'odrl:lteq',
        'rightOperand' => [
            '@value' => intval($versionData['contract_duration_days']),
            '@type' => 'xsd:integer'
        ],
        'unit' => 'odrl:day'
    ];
    
    $agreement = [
        '@context' => 'http://www.w3.org/ns/odrl/2/',
        '@type' => 'odrl:Agreement',
        '@id' => "did:web:{$domain}:agreement:{$applyId}",
        'assigner' => "did:web:{$domain}:participant:{$providerOrgId}",
        'assignee' => "did:web:{$domain}:participant:{$applicantOrgId}",
        'permission' => [
            [
                'action' => $odrlActions,
                'constraint' => $constraints
            ]
        ],
        'prohibition' => [
            [
                'action' => ['odrl:write', 'odrl:delete', 'odrl:share']
            ]
        ],
        'obligation' => [
            [
                'action' => 'odrl:attribute',
                'constraint' => [
                    [
                        'leftOperand' => 'odrl:purpose',
                        'operator' => 'odrl:eq',
                        'rightOperand' => 'attribution'
                    ]
                ]
            ]
        ]
    ];
    
    return $agreement;
}

function buildContractData($typeid, $applyId, $versionData, $appliedActions, $appliedConstraints, $provider_org_id, $applicant_org_id, $productOwnerId) {
 //   $domain = "ttcdb.demosite.com.tw";
 $domain = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'];
    $connector = share_getinfo('machine_accounts', 'thisid', $versionData['connector_id']);
    $versionSnapshot = [
        'display_name' => $versionData['display_name'],
        'description' => $versionData['description'],
        'version' => $versionData['version'],
        'industry_id' => $versionData['industry_id'],
        'data_format' => $versionData['data_format'],
        'contract_duration_days' => $versionData['contract_duration_days'],
        'allowed_actions' => json_decode($versionData['allowed_actions'] ?? '', true) ?: [],
        'prohibited_actions' => json_decode($versionData['prohibited_actions'] ?? '', true) ?: [],
        'constraints' => json_decode($versionData['constraints'] ?? '', true) ?: [],
        'connector' => [
            'id' => $connector['thisid'] ?? null,
            'name' => $connector['thisname'] ?? null,
            'url' => $connector['machine_url'] ?? null
        ]
    ];
    $applicationSnapshot = [
        'applied_actions' => $appliedActions,
        'applied_constraints' => $appliedConstraints
    ];
    $odrlActions = [];
    foreach ($appliedActions as $action) {
        $odrlActions[] = 'odrl:' . strtolower($action);
    }
    $constraints = [];
    foreach ($appliedConstraints as $key => $value) {
        if ($key === 'daily_limit') {
            $constraints[] = [
                'leftOperand' => 'odrl:count',
                'operator' => 'odrl:lteq',
                'rightOperand' => intval($value),
                'unit' => 'odrl:day'
            ];
        } elseif ($key === 'max_concurrent') {
            $constraints[] = [
                'leftOperand' => 'odrl:count',
                'operator' => 'odrl:lteq',
                'rightOperand' => intval($value),
                'unit' => 'odrl:concurrent'
            ];
        }
    }
    $constraints[] = [
        'leftOperand' => 'odrl:elapsedTime',
        'operator' => 'odrl:lteq',
        'rightOperand' => [
            '@value' => intval($versionData['contract_duration_days']),
            '@type' => 'xsd:integer'
        ],
        'unit' => 'odrl:day'
    ];
	$prohibitedActions = json_decode($versionData['prohibited_actions'] ?? '', true) ?: [];
	$odrlProhibited = [];
	foreach ($prohibitedActions as $action) {
		$cleanAction = str_replace('no_', '', $action);
		$odrlProhibited[] = 'odrl:' . strtolower($cleanAction);
	}
		
    $odrlAgreement = [
        '@context' => 'http://www.w3.org/ns/odrl/2/',
        '@type' => 'odrl:Agreement',
        '@id' => "did:web:{$domain}:agreement:{$applyId}",
        'assigner' => "did:web:{$domain}:participant:{$provider_org_id}",
        'assignee' => "did:web:{$domain}:participant:{$applicant_org_id}",
        'connector' => [
            'id' => "did:web:{$domain}:connector:{$connector['thisid']}",
            'name' => $connector['thisname'] ?? null,
            'endpoint' => $connector['machine_url'] ?? null
        ],
        'permission' => [
            [
                'action' => $odrlActions,
                'constraint' => $constraints
            ]
        ],
        'prohibition' => [
            [
                'action' => $odrlProhibited
            ]
        ],
        'obligation' => [
            [
                'action' => 'odrl:attribute',
                'constraint' => [
                    [
                        'leftOperand' => 'odrl:purpose',
                        'operator' => 'odrl:eq',
                        'rightOperand' => 'attribution'
                    ]
                ]
            ]
        ]
    ];

    return [
        'version_snapshot' => $versionSnapshot,
        'application_snapshot' => $applicationSnapshot,
        'odrl_agreement_json' => $odrlAgreement,
        'effective_at' => date('Y-m-d H:i:s'),
        'expired_at' => date('Y-m-d H:i:s', strtotime('+' . $versionData['contract_duration_days'] . ' days'))
    ];
}

function getMonthlyAuditList($mypage, $contractId) {
    global $pdo, $imgurl;

    $myOrgId = checkUserOrganization();
    $typeid = intval($_GET['typeid'] ?? $contractId);

    $contractStmt = $pdo->prepare("
        SELECT c.thisid, c.effective_at, v.display_name, p.resource_name, a.thisname AS applicant_name 
        FROM gaia_contracts c 
        LEFT JOIN gaia_data_products p ON c.product_id = p.thisid 
        LEFT JOIN gaia_product_versions v ON c.version_id = v.thisid 
        LEFT JOIN res_entities a ON c.applicant_org_id = a.thisid 
        WHERE c.thisid = ? AND (c.provider_org_id = ? OR c.applicant_org_id = ?)
        LIMIT 1
    ");
    $contractStmt->execute([$typeid, $myOrgId, $myOrgId]);
    $contract = $contractStmt->fetch(PDO::FETCH_ASSOC);

    if (!$contract) {
        echo "<div class='empty-state'><div class='empty-icon'></div><h3>查無契約資料</h3><p>您無權存取該契約或契約不存在。</p></div>";
        return;
    }

    $productName = !empty($contract['display_name']) ? $contract['display_name'] : ($contract['resource_name'] ?? '已刪除');
    
    $startMonthStr = !empty($contract['effective_at']) ? date('Y-m', strtotime($contract['effective_at'])) : date('Y-01');
    $currentMonthStr = date('Y-m');

    $selectedYM = $_GET['year_month'] ?? $currentMonthStr;
    $startDate = $selectedYM . '-01';
    $endDate = date('Y-m-t', strtotime($startDate));

    $stmt = $pdo->prepare("
        SELECT 
            DATE(dateadd) AS log_date,
            COUNT(*) AS total_transactions,
            SUM(bytes_transferred) AS total_bytes,
            SUM(CASE WHEN reporter_type = 'CONSUMER' THEN 1 ELSE 0 END) AS consumer_total,
            SUM(CASE WHEN reporter_type = 'PROVIDER' AND http_status_code = 200 THEN 1 ELSE 0 END) AS provider_success
        FROM gaia_clearing_logs
        WHERE contract_id = ?
          AND DATE(dateadd) BETWEEN ? AND ?
        GROUP BY DATE(dateadd)
        ORDER BY log_date DESC
    ");
    $stmt->execute([$typeid, $startDate, $endDate]);
    $monthlyList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $title = " 資料共享稽核 — 月報表";
    $subtitle = "契約編號：CT-" . str_pad($contract['thisid'], 6, '0', STR_PAD_LEFT) . " ｜ 發布資料：" . htmlspecialchars($productName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " ｜ 統計月份：" . htmlspecialchars($selectedYM, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    echo "<div class='search-area'>";
    echo "<div class='search-left'>";
    
    echo "<div class='search-item'>";
    echo "<label>📅 統計月份</label>";
    echo "<select name='year_month' form='searchForm'>";
    
    $loopTime = strtotime($currentMonthStr . '-01');
    $stopTime = strtotime($startMonthStr . '-01');
    
    while ($loopTime >= $stopTime) {
        $ymValue = date('Y-m', $loopTime);
        $ymLabel = date('Y 年 m 月', $loopTime);
        if ($ymValue === $currentMonthStr) {
            $ymLabel .= " (本月)";
        }
        
        $selected = ($selectedYM === $ymValue) ? 'selected' : '';
        echo "<option value='" . htmlspecialchars($ymValue, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "' " . $selected . ">" . htmlspecialchars($ymLabel, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</option>";
        
        $loopTime = strtotime('-1 month', $loopTime);
    }
    
    echo "</select>";
    echo "</div>";
    
    echo "</div>";

    echo "<div class='search-right'>";
    echo "<form action='' method='get' id='searchForm'>";
    echo "<input type='hidden' name='job' value='monthlyAudit'>";
    echo "<input type='hidden' name='typeid' value='" . htmlspecialchars($typeid, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 查詢月報</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
    echo "</div>";

    echo "<h2>" . htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</h2>";
    echo "<p class='subtitle'>" . htmlspecialchars($subtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</p>";
    echo "<hr>";

    if (!empty($monthlyList)) {
        echo "<div class='table-wrapper'>";
        echo "<TABLE class='formtable'>";
        echo "<tr>";
        echo "<th width='15%'>日期</th>";
        echo "<th width='15%'>Provider回報數</th>";
        echo "<th width='15%'>Consumer回報數</th>";
        echo "<th width='15%'>差異數</th>";
        echo "<th width='15%'>差異比例</th>";
        echo "<th width='25%'>稽核結果</th>";
        echo "</tr>";

		foreach ($monthlyList as $row) {
            $consumerTotal = intval($row['consumer_total']);
            $providerSuccess = intval($row['provider_success']);
            
            $diffCount = abs($providerSuccess - $consumerTotal);
            $diffPercentage = ($consumerTotal > 0) ? (abs($providerSuccess - $consumerTotal) / $consumerTotal) * 100 : 0;
            
            if ($diffPercentage <= 1.0) {
                $statusBadge = "<span class='status-badge status-approved'>🟢 相符 / 正常差額</span>";
            } elseif ($diffPercentage <= 5.0) {
                $statusBadge = "<span class='status-badge status-none' style='color: #856404; background: #fff3cd;'>🟡 建議檢視</span>";
            } else {
                $statusBadge = "<span class='status-badge status-rejected'>🔴 須立案調查</span>";
            }

            $diffColor = ($diffCount > 0) ? '#d9534f' : '#6c757d';

            echo "<tr>";
            echo "<td><b>" . htmlspecialchars($row['log_date'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</b></td>";
            echo "<td>" . number_format($providerSuccess) . " 筆</td>";
            echo "<td>" . number_format($consumerTotal) . " 筆</td>";
            echo "<td><span style='color: " . $diffColor . "; font-weight: bold;'>" . number_format($diffCount) . " 筆</span></td>";
            echo "<td><b>" . number_format($diffPercentage, 2) . "%</b></td>";
            echo "<td>" . $statusBadge . "</td>";
            echo "</tr>";
        }

        echo "</TABLE>";
        echo "</div>";
        echo "<div class='audit-legend-box' style='margin-top: 15px; padding: 12px 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 6px; font-size: 13px; color: #495057;'>";
        echo "<b style='display: block; margin-bottom: 6px; color: #333;'>📋 說明：</b>";
        echo "<ul style='margin: 0; padding-left: 20px; line-height: 1.6;'>";
        echo "<li><span style='color: #28a745; font-weight: bold;'>🟢 相符 / 正常差額：</span> 誤差百分比 &le; 1.0%，雙方資料共享紀錄高度吻合。</li>";
        echo "<li><span style='color: #856404; font-weight: bold;'>🟡 建議檢視：</span> 誤差百分比介於 1.0% ~ 5.0% 之間，可能存在輕微網路延遲或漏報，建議人工抽查。</li>";
        echo "<li><span style='color: #dc3545; font-weight: bold;'>🔴 須立案調查：</span> 誤差百分比 &gt; 5.0%，雙方資料落差過大，需進行技術對帳與調查。</li>";
        echo "</ul>";
        echo "</div>";		
        echo "<div class='table-footer'>該月份共計 " . count($monthlyList) . " 天有資料共享紀錄</div>";
    } else {
        echo "<div class='empty-state'>";
        echo "<div class='empty-icon'></div>";
        echo "<h3>月份尚無紀錄</h3>";
        echo "<p>" . htmlspecialchars($selectedYM, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " 期間內，無任何資料共享稽核紀錄。</p>";
        echo "</div>";
    }
}

function getJsonDownloadPage($mypage, $contractId) {
    global $pdo, $imgurl;

    $myOrgId = checkUserOrganization();
    $typeid = intval($_GET['typeid'] ?? $contractId);

    $contractStmt = $pdo->prepare("
        SELECT c.thisid, v.display_name, p.resource_name, a.thisname AS applicant_name 
        FROM gaia_contracts c 
        LEFT JOIN gaia_data_products p ON c.product_id = p.thisid 
        LEFT JOIN gaia_product_versions v ON c.version_id = v.thisid 
        LEFT JOIN res_entities a ON c.applicant_org_id = a.thisid 
        WHERE c.thisid = ? AND (c.provider_org_id = ? OR c.applicant_org_id = ?)
        LIMIT 1
    ");
    $contractStmt->execute([$typeid, $myOrgId, $myOrgId]);
    $contract = $contractStmt->fetch(PDO::FETCH_ASSOC);

    if (!$contract) {
        echo "<div class='empty-state'><div class='empty-icon'></div><h3>查無契約資料</h3><p>您無權存取該契約或契約不存在。</p></div>";
        return;
    }

    $productName = !empty($contract['display_name']) ? $contract['display_name'] : ($contract['resource_name'] ?? '已刪除');
    $today = date('Y-m-d');

    $startDate = $_GET['start_date'] ?? $today;
    $endDate = $_GET['end_date'] ?? $today;

    $stmt = $pdo->prepare("
        SELECT 
            thisid AS clearing_log_id, 
            contract_id, 
            jti, 
            reporter_type, 
            reporter_did, 
            event_type, 
            bytes_transferred, 
            payload_hash, 
            prev_log_hash, 
            current_log_hash, 
            http_status_code, 
            transfer_time, 
            dateadd 
        FROM gaia_clearing_logs 
        WHERE contract_id = ? 
          AND DATE(dateadd) BETWEEN ? AND ? 
        ORDER BY thisid ASC
    ");
    $stmt->execute([$typeid, $startDate, $endDate]);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $exportData = [
        'contract_id' => $typeid,
        'start_date'  => $startDate,
        'end_date'    => $endDate,
        'total_logs'  => count($logs),
        'logs'        => $logs
    ];

    $jsonString = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $filename = "contract_clearing_audit_CT" . str_pad($typeid, 6, '0', STR_PAD_LEFT) . "_{$startDate}_to_{$endDate}.json";

    $title = " 稽核紀錄：JSON 下載";
    $subtitle = "契約編號：CT-" . str_pad($contract['thisid'], 6, '0', STR_PAD_LEFT) . " ｜ 發布資料：" . htmlspecialchars($productName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " ｜ 區間：" . htmlspecialchars($startDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . " ~ " . htmlspecialchars($endDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    echo "<div class='search-area'>";
    echo "<div class='search-left'>";
    
    echo "<div class='search-item'>";
    echo "<label>📅 開始日期</label>";
    echo "<input type='date' name='start_date' value='" . htmlspecialchars($startDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "' form='searchForm'>";
    echo "</div>";

    echo "<div class='search-item' style='margin-left: 15px;'>";
    echo "<label>📅 結束日期</label>";
    echo "<input type='date' name='end_date' value='" . htmlspecialchars($endDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "' form='searchForm'>";
    echo "</div>";
    
    echo "</div>";

    echo "<div class='search-right'>";
    echo "<form action='' method='get' id='searchForm'>";
    echo "<input type='hidden' name='tf' value='" . htmlspecialchars($mypage, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>";
    echo "<input type='hidden' name='job' value='jsonDownload'>";
    echo "<input type='hidden' name='typeid' value='" . htmlspecialchars($typeid, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 重新整理區間</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
    echo "</div>";

    echo "<h2>" . htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</h2>";
    echo "<p class='subtitle'>" . htmlspecialchars($subtitle, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</p>";
    echo "<hr>";

    echo "<div class='empty-state' style='padding: 30px; text-align: center;'>";
    echo "<h3>已成功載入 " . count($logs) . " 筆資料共享稽核紀錄</h3>";
    echo "<p style='margin-bottom: 20px;'>點擊下方按鈕即可將產生的 <b>" . htmlspecialchars($filename, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</b> 下載至您的電腦。</p>";
    echo "<button type='button' id='downloadJsonBtn' class='gray-btn' style='background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;'>📥 下載 JSON 檔案</button>";
    echo "</div>";

    echo "<script nonce='" . htmlspecialchars($_SESSION['nonce'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "'>
    document.getElementById('downloadJsonBtn').addEventListener('click', function() {
        const jsonString = " . json_encode($jsonString) . ";
        const blob = new Blob([jsonString], { type: 'application/json;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = " . json_encode($filename) . ";
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
    </script>";

    echo "<h3>📄 檔案內容預覽</h3>";
    echo "<div class='table-wrapper' style='max-height: 400px; overflow-y: auto; background: #f8f9fa; padding: 15px; border: 1px solid #ddd;'>";
    echo "<pre style='margin: 0; font-size: 13px;'>" . htmlspecialchars($jsonString, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
    echo "</div>";
}
?>
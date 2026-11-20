<?php 
session_start();
require_once 'adm/config/config.php';
require_once 'adm/config/getformin.php';
require_once 'adm/config/createimg.php';
require_once 'adm/config/share.php';
global $wedaddress,$imwebgurl;
$imgurl=$wedaddress.$imwebgurl;	
$URL=$wedaddress.$_SERVER['REQUEST_URI'];
$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);
require_once 'Share.php';//共用資料
requireLogin();
$temp=share_getinfo("`sit_pages`","thispage",'indexseo');
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);
$currentPage = 'review_management';
$mypage="review_management";
require_once 'seonew.php';
require_once 'headerlogin.php';
$_SESSION['nonce']=$nonce;
$typetable="gaia_data_products";
$uptable="gaia_subscriptions";
require_once 'adm/config/product.php';
$responz="";
global $imgurl;
 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;
 }elseif($job=="getdetaillist1"){	
   $tagon=2;
   $tagshow=2;
 }elseif($job=="submitReview"){	
   $tagon=2;
   $tagshow=2;   
   submitReview($mypage, $uptable);
 }else{
   $tagon=1;
   $tagshow=1;	 
 }
?>
<main id="main" role="main">
<section aria-label="審核管理 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="審核管理 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">審核管理</h1>
</div>
</section>
<section aria-label="審核管理" class="detail-page-hero detail-intro-after-query">
<div class="member-container">

    <?php include 'sidebar.php'; ?>
    <div class="content-area">
<div class='contentin'>

  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>
		<a href='review_management.html?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>審核列表</div>
        </a>
<?php 
if 	($tagon>1 ){
?>	
		<a href='review_management.html?tf=<?=$mypage?>&job=getdetaillist1&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'>審核詳細頁</div>
        </a>
<?php 
}
?>		
    </div>
      <div class='tagbody'>
  <div class='error log-empty log-error'>
  </div> 
        <div class='tagbodyin'>
<?php
		if($tagshow==1){
			getContact1($mypage,$typetable);
		}else if($tagshow==2){
			getdetail1($mypage,$typetable);
		}
		?>		
        </div>
      </div>
   </div>
 <?php
function getContact1($mypage, $typetable) {
    global $imgurl;
    
    
    // 1. 取得登入者組織ID（提供者）
    
    $myOrgId = checkUserOrganization();
    
    
    // 2. 取得搜尋/篩選條件
    
    $statusFilter = $_GET['status'] ?? $_POST['status'] ?? '';
    $keyword = $_GET['keyword'] ?? $_POST['keyword'] ?? '';
    
    
    // 3. 組合查詢條件（只顯示自己發布資料的申請）
    
    $where = " p.owner_id = '" . addslashes($myOrgId) . "' ";
    
    if ($statusFilter !== '') {
        $where .= " AND s.status = '" . addslashes($statusFilter) . "' ";
    }
    
    if (!empty($keyword)) {
        $where .= " AND (v.display_name LIKE '%" . addslashes($keyword) . "%' OR p.resource_name LIKE '%" . addslashes($keyword) . "%' OR e.thisname LIKE '%" . addslashes($keyword) . "%') ";
    }
    
    
    // 4. 抓申請列表（抓版本檔的 display_name）
    
    $applyList = share_getfree("
        SELECT 
            s.thisid AS apply_id,
            s.product_id,
            s.version_id,
            s.applied_actions,
            s.applied_constraints,
            s.status,
            s.created_at,
            s.updated_at,
            s.review_comment,
            p.resource_name,
            p.owner_id,
            p.current_version,
            p.isopen,
            r.thisname AS provider_name,
            e.thisname AS applicant_name,
            e.contact_name AS applicant_contact_name,
            e.contact_phone AS applicant_phone,
            v.display_name
        FROM gaia_subscriptions s
        LEFT JOIN gaia_data_products p ON s.product_id = p.thisid
        LEFT JOIN gaia_product_versions v ON s.version_id = v.thisid
        LEFT JOIN res_entities r ON p.owner_id = r.thisid
        LEFT JOIN res_entities e ON s.applicant_org_id = e.thisid
        WHERE " . $where . "
        ORDER BY 
            CASE WHEN s.status = 0 THEN 0 ELSE 1 END,
            s.created_at DESC
    ");
    
    
    // 篩選區塊
    
    echo "<div class='search-area'>";
    echo "<div class='search-left'>";

    // 關鍵字搜尋（發布資料名稱或申請公司）
    echo "<div class='search-item search-keyword'>";
    echo "<label>🔍 關鍵字</label>";
    echo "<input type='text' name='keyword' value='" . htmlspecialchars($keyword) . "' class='input-full' placeholder='搜尋發布資料名稱或申請公司...' form='searchForm'>";
    echo "</div>";

    // 狀態篩選
    echo "<div class='search-item'>";
    echo "<label>📊 狀態</label>";
    echo "<select name='status' form='searchForm'>";
    echo "<option value=''>全部</option>";
    echo "<option value='0' " . ($statusFilter === '0' ? 'selected' : '') . ">⏳ 待審核</option>";
    echo "<option value='1' " . ($statusFilter === '1' ? 'selected' : '') . ">✅ 已核准</option>";
    echo "<option value='2' " . ($statusFilter === '2' ? 'selected' : '') . ">❌ 已拒絕</option>";
    echo "</select>";
    echo "</div>";

    echo "</div>";

    echo "<div class='search-right'>";
    echo "<form action='review_management.html' method='get' id='searchForm'>";
    echo "<input type='hidden' name='tf' value='" . $mypage . "'>";
    echo "<input type='hidden' name='job' value='getContact1'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 搜尋</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
    
    
    // 申請列表
    
    if (!empty($applyList)) {
        echo "<div class='table-wrapper'>";
        echo "<TABLE class='formtable'>";
        echo "<tr>";
        echo "<th width='12%'>申請公司</th>";
        echo "<th width='15%'>聯絡資訊</th>";
        echo "<th width='20%'>發布資料名稱</th>";
        echo "<th width='15%'>申請時間</th>";
        echo "<th width='10%'>狀態</th>";
        echo "<th width='28%'>操作</th>";
        echo "</tr>";
        
        foreach ($applyList as $row) {
            // 狀態顯示
            $statusText = '';
            $statusClass = '';
            
            if ($row['status'] == 0) {
                $statusText = '⏳ 待審核';
                $statusClass = 'status-pending';
            } elseif ($row['status'] == 1) {
                $statusText = '✅ 已核准';
                $statusClass = 'status-approved';
            } elseif ($row['status'] == 2) {
                $statusText = '❌ 已拒絕';
                $statusClass = 'status-rejected';
            } else {
                $statusText = '❓ 未知';
                $statusClass = 'status-none';
            }
            
            $applicantName = $row['applicant_name'] ?? '未知';
            $contactName = $row['applicant_contact_name'] ?? '';
            $contactPhone = $row['applicant_phone'] ?? '';
            // 發布資料名稱：優先顯示 display_name（對外名稱）
            $productName = !empty($row['display_name']) ? $row['display_name'] : ($row['resource_name'] ?? '已刪除');
            
            // 聯絡資訊
            $contactInfo = '';
            if (!empty($contactName)) {
                $contactInfo .= "👤 " . htmlspecialchars($contactName);
            }
            if (!empty($contactPhone)) {
                if (!empty($contactInfo)) {
                    $contactInfo .= "<br>";
                }
                $contactInfo .= "📱 " . htmlspecialchars($contactPhone);
            }
            if (empty($contactInfo)) {
                $contactInfo = '-';
            }
            
            $actionBtns = '';

            // 審核按鈕（只有待審核可以審核）
            if ($row['status'] == 0 && $row['product_id'] > 0) {
                $actionBtns .= "<a href='review_management.html?tf=".$mypage."&job=getdetaillist1&typeid=" . $row['apply_id'] . "' class='orange-btn'>📋 審核</a> ";
            }

            // 若無任何按鈕，顯示「-」
            if (empty($actionBtns)) {
                $actionBtns = '-';
            }
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($applicantName) . "</td>";
            echo "<td class='contact-info-cell'>" . $contactInfo . "</td>";
            echo "<td class='tleft'>" . htmlspecialchars($productName) . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "<td><span class='status-badge " . $statusClass . "'>" . $statusText . "</span></td>";
            echo "<td>";
            echo "<div class='btn-group'>";
            echo $actionBtns;
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
        
        echo "</TABLE>";
        echo "</div>";
        echo "<div class='table-footer'>顯示 " . count($applyList) . " 筆</div>";
    } else {
        echo "<div class='empty-state'>";
        echo "<div class='empty-icon'>📭</div>";
        echo "<h3>尚無申請紀錄</h3>";
        echo "<p>目前還沒有任何人申請您的發布資料。</p>";
        echo "</div>";
    }
}
function getdetail1($mypage, $typetable) {
    global $imgurl;
    
    $typeid = $GLOBALS['typeid'] ?? 0;
    $error = '';
    $success = '';
    
    
    // 1. 檢查申請是否存在
    
    $apply = share_getinfo('gaia_subscriptions', 'thisid', $typeid);
    if (!$apply) {
        echo "<div class='error-msg'>❌ 申請不存在</div>";
        echo "<a href='review_management.html?tf=".$mypage."&job=getContact1' class='gray-btn'>← 返回審核管理</a>";
        return;
    }
    
    
    // 2. 檢查權限（只能審核自己發布資料的申請）
    
    $myOrgId = checkUserOrganization();
    $product = checkDataExists($typetable, $apply['product_id']);
    if ($product['owner_id'] != $myOrgId) {
        echo "<div class='error-msg'>⛔ 您沒有權限審核此申請</div>";
        echo "<a href='review_management.html?tf=".$mypage."&job=getContact1' class='gray-btn'>← 返回審核管理</a>";
        return;
    }
    
    
    // 3. 檢查是否已審核（防止重複審核）
    
    if ($apply['status'] != 0) {
        $statusText = ($apply['status'] == 1) ? '已核准' : '已拒絕';
        echo "<div class='error-msg'>⚠️ 此申請已 " . $statusText . "，無需再次審核</div>";
        echo "<a href='review_management.html?tf=".$mypage."&job=getContact1' class='gray-btn'>← 返回審核管理</a>";
        return;
    }
    
    
    // 4. 取得申請人公司資訊 + 版本資訊
    
    $applicant = share_getinfo('res_entities', 'thisid', $apply['applicant_org_id']);
    $versionData = share_getinfo('gaia_product_versions', 'thisid', $apply['version_id']);
    
    // 解析版本中的 allowed_actions 和 constraints
    $versionActions = json_decode($versionData['allowed_actions'] ?? '', true) ?: [];
    $versionConstraints = json_decode($versionData['constraints'] ?? '', true) ?: [];
    
    // 解析申請中的 allowed_actions 和 constraints
    $appliedActions = json_decode($apply['applied_actions'] ?? '', true) ?: [];
    $appliedConstraints = json_decode($apply['applied_constraints'] ?? '', true) ?: [];
    
    // 取得可做什麼的名稱對照
    $allowedList = share_gettable("gaia_allowed_actions WHERE isopen = 1 ORDER BY sorting");
    $allowedMap = [];
    foreach ($allowedList as $row) {
        $allowedMap[$row['thiscode']] = $row['thisname'];
    }
    
    
    // 5. 取得產業名稱
$industryName = '';
$industryCode = '';
$schemaName   = '';
$schemaCode   = '';
$versionNum   = '';
$schemaJson   = '';    
// 這裡的 $versionData['industry_id'] 存放的是 gaia_schema_version.thisid
$schema_version_id = intval($versionData['industry_id'] ?? 0);

if ($schema_version_id > 0) {
    $sql = "SELECT 
                i.thisname AS ind_name, i.thiscode AS ind_code,
                s.schemaname, s.schemacode,
                v.version, v.schema_json
            FROM gaia_schema_version v
            INNER JOIN gaia_schema s ON v.schema_id = s.thisid
            INNER JOIN gaia_industry i ON s.industry_id = i.thisid
            WHERE v.thisid = $schema_version_id";

    $res = share_getfree($sql);
    if (!empty($res)) {
        $info = $res[0];
        $industryName = $info['ind_name'] ?? '';
        $industryCode = $info['ind_code'] ?? '';
        $schemaName   = $info['schemaname'] ?? '';
        $schemaCode   = $info['schemacode'] ?? '';
        $versionNum   = $info['version'] ?? '';
        $schemaJson   = $info['schema_json'] ?? '';
    }
}
    
    // 6. 敏感等級標籤
    
    $sensitivityLabels = [
        0 => '公開 (Public)',
        1 => '內部 (Internal)',
        2 => '機密 (Confidential)',
        3 => '高度機密 (Highly Confidential)',
        4 => '需人工審核 (Restricted)'
    ];
    $sensitivity = $sensitivityLabels[$versionData['data_sensitivity_level'] ?? 0] ?? '未設定';
    
    
    // 7. 更新頻率轉換
    

    
    
    // 8. 顯示頁面
    

    // 標題
    echo "<h2 class='form-title'>📋 審核申請</h2>";
    echo "<hr class='form-hr'>";
    
    
    // 申請基本資訊（formtable 樣式）
    
    echo "<TABLE class='formtable'>";
    echo "<tr><th colspan='2' class='section-title'>申請基本資訊</th></tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>申請公司</th>";
    echo "<td class='input-col'>" . htmlspecialchars($applicant['thisname'] ?? '未知') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>聯絡人</th>";
    echo "<td class='input-col'>" . htmlspecialchars($applicant['contact_name'] ?? '-') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>聯絡電話</th>";
    echo "<td class='input-col'>" . htmlspecialchars($applicant['contact_phone'] ?? '-') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>申請發布資料</th>";
    echo "<td class='input-col'>" . htmlspecialchars($versionData['display_name'] ?? $product['resource_name']) . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>申請版本</th>";
    echo "<td class='input-col'>" . htmlspecialchars($versionData['version'] ?? '-') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>申請時間</th>";
    echo "<td class='input-col'>" . $apply['created_at'] . "</td>";
    echo "</tr>";
    
    echo "</TABLE>";
    
    
    // 版本完整資訊（formtable 樣式）
    
    echo "<TABLE class='formtable'>";
    echo "<tr><th colspan='2' class='section-title'>📦 版本完整資訊</th></tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>對外顯示名稱<br><span class='field-en'>Display Name</span></th>";
    echo "<td class='input-col'>" . htmlspecialchars($versionData['display_name'] ?? '-') . "</td>";
    echo "</tr>";
    
// 呈現 1：目錄分類 / 產業標準範本 (顯示完整 3 層結構)
echo "<tr>";
echo "<th class='label-col'>目錄分類 / 產業標準範本<br><span class='field-en'>Industry / Schema & Version</span></th>";
echo "<td class='input-col'>";
if (!empty($industryName)) {
    // 格式：產業名稱 (產業代碼) > Schema名稱 (Schema代碼) - v1.0.0
    echo htmlspecialchars($industryName) . " (" . htmlspecialchars($industryCode) . ") &nbsp;&gt;&nbsp; ";
    echo "<strong>" . htmlspecialchars($schemaName) . " (" . htmlspecialchars($schemaCode) . ")</strong> ";
    echo "<span class='badge' style='background:#e0e0e0; padding:2px 6px; border-radius:4px; font-size:0.9em;'>" . htmlspecialchars($versionNum) . "</span>";
} else {
    echo "<span class='text-muted'>未選擇範本</span>";
}
echo "</td>";
echo "</tr>";
	


		
    echo "<tr>";
    echo "<th class='label-col'>發布資料描述<br><span class='field-en'>Description</span></th>";
    echo "<td class='input-col'>" . nl2br(htmlspecialchars($versionData['description'] ?? '')) . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>版本號<br><span class='field-en'>Version</span></th>";
    echo "<td class='input-col'>" . htmlspecialchars($versionData['version'] ?? '-') . "</td>";
    echo "</tr>";
    
    // 關鍵字
    $keywords = json_decode($versionData['keywords'] ?? '', true) ?: [];
    $keywordStr = !empty($keywords) ? implode('、', $keywords) : '-';
    echo "<tr>";
    echo "<th class='label-col'>關鍵字/標籤<br><span class='field-en'>Keywords</span></th>";
    echo "<td class='input-col'>" . htmlspecialchars($keywordStr) . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>資料格式<br><span class='field-en'>Data Format</span></th>";
    echo "<td class='input-col'>" . htmlspecialchars($versionData['data_format'] ?? '-') . "</td>";
    echo "</tr>";
    
    // 契約
    echo "<tr><th colspan='2' class='section-title'>契約 (Contract)</th></tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>契約有效天數<br><span class='field-en'>Contract Duration</span></th>";
    echo "<td class='input-col'>" . ($versionData['contract_duration_days'] ?? 30) . " 天</td>";
    echo "</tr>";
    
    // 連接器
    if (!empty($versionData['connector_id'])) {
        $connector = share_getinfo('machine_accounts', 'thisid', $versionData['connector_id']);
        echo "<tr><th colspan='2' class='section-title'>連接器 </th></tr>";
        echo "<tr>";
        echo "<th class='label-col'>連接器<br><span class='field-en'>Connector</span></th>";
        echo "<td class='input-col'>" . htmlspecialchars($connector['thisname'] ?? '-') . " (" . htmlspecialchars($connector['machine_url'] ?? '') . ")</td>";
        echo "</tr>";
    }
    
    // 使用政策
    echo "<tr><th colspan='2' class='section-title'>使用政策 (Usage Policy)</th></tr>";
    
    // 可做什麼（版本）
    echo "<tr>";
    echo "<th class='label-col'>可做什麼<br><span class='field-en'>Allowed Actions</span></th>";
    echo "<td class='input-col'>";
    if (!empty($versionActions)) {
        echo "<div class='action-tags'>";
        foreach ($versionActions as $actionCode) {
            $actionName = $allowedMap[$actionCode] ?? $actionCode;
            echo "<span class='action-tag allowed'>" . htmlspecialchars($actionName) . "</span>";
        }
        echo "</div>";
    } else {
        echo "<span class='text-muted'>未設定</span>";
    }
    echo "</td>";
    echo "</tr>";
    
    // 不可做什麼（版本）
    $prohibitedActions = json_decode($versionData['prohibited_actions'] ?? '', true) ?: [];
    echo "<tr>";
    echo "<th class='label-col'>不可做什麼<br><span class='field-en'>Prohibited Actions</span></th>";
    echo "<td class='input-col'>";
    if (!empty($prohibitedActions)) {
        echo "<div class='action-tags'>";
        foreach ($prohibitedActions as $actionCode) {
            $actionName = $allowedMap[$actionCode] ?? $actionCode;
            echo "<span class='action-tag prohibited'>" . htmlspecialchars($actionName) . "</span>";
        }
        echo "</div>";
    } else {
        echo "<span class='text-muted'>未設定</span>";
    }
    echo "</td>";
    echo "</tr>";
    
    // 敏感等級
    echo "<tr>";
    echo "<th class='label-col'>敏感等級<br><span class='field-en'>Sensitivity Level</span></th>";
    echo "<td class='input-col'>" . $sensitivity . "</td>";
    echo "</tr>";
    
    // 技術 Metadata
    echo "<tr><th colspan='2' class='section-title'>技術 Metadata (Technical Metadata)</th></tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>更新頻率<br><span class='field-en'>Update Frequency</span></th>";
    echo "<td class='input-col'>" . convertFrequency($versionData['update_frequency'] ?? '') . "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<th class='label-col'>可用性<br><span class='field-en'>Availability</span></th>";
    echo "<td class='input-col'>" . htmlspecialchars($versionData['availability'] ?? '-') . "</td>";
    echo "</tr>";
    
    echo "</TABLE>";
    
    
    // 申請內容比對（簡化版）
    
    echo "<TABLE class='formtable'>";
    echo "<tr><th colspan='2' class='section-title'>📝 申請內容</th></tr>";
    
    // 申請的權限
    echo "<tr>";
    echo "<th class='label-col'>申請權限<br><span class='field-en'>Applied Actions</span></th>";
    echo "<td class='input-col'>";
    if (!empty($appliedActions)) {
        echo "<div class='action-tags'>";
        foreach ($appliedActions as $actionCode) {
            $actionName = $allowedMap[$actionCode] ?? $actionCode;
            $isValid = in_array($actionCode, $versionActions);
            $class = $isValid ? 'action-tag allowed' : 'action-tag invalid';
            echo "<span class='$class'>" . htmlspecialchars($actionName) . ($isValid ? '' : ' ⚠️') . "</span>";
        }
        echo "</div>";
    } else {
        echo "<span class='text-muted'>未勾選任何權限</span>";
    }
    echo "</td>";
    echo "</tr>";
    
    // 申請的限制
    echo "<tr>";
    echo "<th class='label-col'>申請限制<br><span class='field-en'>Applied Constraints</span></th>";
    echo "<td class='input-col'>";
    if (!empty($appliedConstraints)) {
        echo "<div class='constraint-tags-display'>";
        foreach ($appliedConstraints as $key => $value) {
            $displayValue = is_array($value) ? implode(', ', $value) : $value;
            // 檢查是否超過上限
            $isOverLimit = false;
            if (isset($versionConstraints[$key]) && is_numeric($versionConstraints[$key]) && is_numeric($value)) {
                if ($value > $versionConstraints[$key]) {
                    $isOverLimit = true;
                }
            }
            $class = $isOverLimit ? 'constraint-tag over-limit' : 'constraint-tag';
            echo "<span class='$class'>";
            echo "<span class='tag-key'>" . htmlspecialchars($key) . "：</span>";
            echo "<span class='tag-value'>" . htmlspecialchars($displayValue) . "</span>";
            if ($isOverLimit) {
                echo " <span class='over-hint'>⚠️ 超過上限</span>";
            }
            echo "</span>";
        }
        echo "</div>";
    } else {
        echo "<span class='text-muted'>未填寫任何限制</span>";
    }
    echo "</td>";
    echo "</tr>";
    
    echo "</TABLE>";

// 版本 vs 申請 比對（審核重點）

echo "<TABLE class='formtable'>";
echo "<tr><th colspan='2' class='section-title'>📊 權限比對（版本 vs 申請）</th></tr>";

//  可做什麼比對 
echo "<tr>";
echo "<th class='label-col'>可做什麼<br><span class='field-en'>Allowed Actions</span></th>";
echo "<td class='input-col'>";
echo "<div class='compare-section'>";

// 版本（上方）
echo "<div class='compare-version'>";
echo "<div class='compare-label'>📦 版本允許</div>";
echo "<div class='compare-tags'>";
if (!empty($versionActions)) {
    foreach ($versionActions as $actionCode) {
        $actionName = $allowedMap[$actionCode] ?? $actionCode;
        echo "<span class='action-tag allowed'>" . htmlspecialchars($actionName) . "</span>";
    }
} else {
    echo "<span class='text-muted'>未設定</span>";
}
echo "</div>";
echo "</div>";

// 申請（下方）
echo "<div class='compare-apply'>";
echo "<div class='compare-label'>📝 客戶申請</div>";
echo "<div class='compare-tags'>";
if (!empty($appliedActions)) {
    foreach ($appliedActions as $actionCode) {
        $actionName = $allowedMap[$actionCode] ?? $actionCode;
        $isValid = in_array($actionCode, $versionActions);
        $class = $isValid ? 'action-tag allowed' : 'action-tag invalid';
        echo "<span class='$class'>" . htmlspecialchars($actionName) . ($isValid ? '' : ' ⚠️') . "</span>";
    }
} else {
    echo "<span class='text-muted'>未勾選任何權限</span>";
}
echo "</div>";
echo "</div>";

echo "</div>";
echo "</td>";
echo "</tr>";

//  限制比對 
echo "<tr>";
echo "<th class='label-col'>限制<br><span class='field-en'>Constraints</span></th>";
echo "<td class='input-col'>";
echo "<div class='compare-section'>";

// 版本（上方）
echo "<div class='compare-version'>";
echo "<div class='compare-label'>📦 版本上限</div>";
echo "<div class='compare-items'>";
if (!empty($versionConstraints)) {
    foreach ($versionConstraints as $key => $maxValue) {
        $displayMax = is_bool($maxValue) ? ($maxValue ? '✅ 已啟用' : '❌ 已停用') : 
                      (is_array($maxValue) ? implode(', ', $maxValue) : $maxValue);
        echo "<div class='compare-item'><span class='item-key'>" . htmlspecialchars($key) . "：</span><span class='item-value'>" . htmlspecialchars($displayMax) . "</span></div>";
    }
} else {
    echo "<span class='text-muted'>未設定</span>";
}
echo "</div>";
echo "</div>";

// 申請（下方）
echo "<div class='compare-apply'>";
echo "<div class='compare-label'>📝 客戶填寫</div>";
echo "<div class='compare-items'>";
if (!empty($appliedConstraints)) {
    foreach ($appliedConstraints as $key => $value) {
        // 檢查是否超過上限
        $isOverLimit = false;
        $maxDisplay = '';
        if (isset($versionConstraints[$key])) {
            $maxValue = $versionConstraints[$key];
            if (is_numeric($maxValue) && is_numeric($value) && $value > $maxValue) {
                $isOverLimit = true;
                $maxDisplay = '（上限 ' . $maxValue . '）';
            } elseif (is_bool($maxValue) && is_bool($value) && $value !== $maxValue) {
                $isOverLimit = true;
                $maxDisplay = '（版本設定 ' . ($maxValue ? '已啟用' : '已停用') . '）';
            }
        }
        $displayValue = is_bool($value) ? ($value ? '✅ 啟用' : '❌ 停用') : 
                        (is_array($value) ? implode(', ', $value) : $value);
        $class = $isOverLimit ? 'compare-item over-limit' : 'compare-item';
        echo "<div class='$class'>";
        echo "<span class='item-key'>" . htmlspecialchars($key) . "：</span>";
        echo "<span class='item-value'>" . htmlspecialchars($displayValue) . "</span>";
        if ($isOverLimit) {
            echo " <span class='over-hint'>⚠️ 超過上限 " . htmlspecialchars($maxDisplay) . "</span>";
        }
        echo "</div>";
    }
} else {
    echo "<span class='text-muted'>未填寫任何限制</span>";
}
echo "</div>";
echo "</div>";

echo "</div>";
echo "</td>";
echo "</tr>";

echo "</TABLE>";
    
    // 審核表單
    

    echo "<TABLE class='formtable'>";
    echo "<tr><th colspan='2' class='section-title'>審核結果</th></tr>";
    
    if (!empty($error)) {
        echo "<tr><td colspan='2' class='input-col'><div class='error-msg'>" . htmlspecialchars($error) . "</div></td></tr>";
    }
    if (!empty($success)) {
        echo "<tr><td colspan='2' class='input-col'><div class='success-msg'>" . htmlspecialchars($success) . "</div></td></tr>";
    }
    
    echo "<tr>";
    echo "<td colspan='2' class='input-col'>";
    echo "<form action='review_management.html?tf=".$mypage."&job=submitReview' method='post'>";
    echo "<input type='hidden' name='typeid' value='" . $typeid . "'>";
    
    // 審核結果選項
    echo "<div class='form-group'>";
    echo "<label><span class='redtext'>＊</span> 審核結果</label>";
    echo "<div class='radio-group'>";
    echo "<label class='radio-label'><input type='radio' name='review_result' value='approve' checked> ✅ 核准通過</label>";
    echo "<label class='radio-label'><input type='radio' name='review_result' value='reject'> ❌ 拒絕申請</label>";
    echo "</div>";
    echo "</div>";
    
    // 退件原因
    $rejectReasons = share_gettable("reject_reasons WHERE isopen = 1 ORDER BY sorting");
    echo "<div class='form-group reject-reasons'>";
    echo "<label>退件原因（拒絕時請選擇或填寫）</label>";
    echo "<div class='checkbox-group'>";
    foreach ($rejectReasons as $reason) {
        echo "<label class='checkbox-label'>";
        echo "<input type='checkbox' name='reject_reasons[]' value='" . $reason['thisid'] . "'> ";
        echo htmlspecialchars($reason['thisname']);
        echo "</label>";
    }
    echo "</div>";
    echo "<div class='form-group form-group-top'>";
    echo "<label>其他原因</label>";
    echo "<input type='text' name='reject_reason_custom' class='form-control' placeholder='請輸入其他退件原因（選填）'>";
    echo "</div>";
    echo "<span class='hint-text'>💡 拒絕申請時，請至少選擇一個退件原因或填寫其他原因</span>";
    echo "</div>";
    
    echo "<div class='form-actions'>";
    echo "<a href='review_management.html?tf=".$mypage."&job=getContact1' class='cancel-btn'>取消</a> ";
    echo "<button type='submit' class='green-btn'>確認送出</button>";
    echo "</div>";
    
    echo "</form>";
    echo "</td>";
    echo "</tr>";
    
    echo "</TABLE>";

    

}
function submitReview($mypage, $listtable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    $reviewResult = $GLOBALS['review_result'] ?? '';
    $rejectReasons = $_POST['reject_reasons'] ?? [];
    $rejectReasonCustom = trim($GLOBALS['reject_reason_custom'] ?? '');
    
    
    // 1. 驗證：審核結果
    
    if (empty($reviewResult)) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 請選擇審核結果'); history.back();</script>";
        exit;
    }
    
    if (!in_array($reviewResult, ['approve', 'reject'])) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 無效的審核結果'); history.back();</script>";
        exit;
    }
    
    
    // 2. 檢查申請是否存在
    
    $apply = share_getinfo($listtable, 'thisid', $typeid);
    if (!$apply) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 申請不存在'); history.back();</script>";
        exit;
    }
    
    
    // 3. 檢查權限（只能審核自己發布資料的申請）
    
    $myOrgId = checkUserOrganization();
    $product = checkDataExists('gaia_data_products', $apply['product_id']);
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⛔ 您沒有權限審核此申請'); history.back();</script>";
        exit;
    }
    
    
    // 4. 檢查是否已審核
    
    if ($apply['status'] != 0) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 此申請已審核'); history.back();</script>";
        exit;
    }
	
    if ($reviewResult === 'approve') {
        $hasReason = !empty($rejectReasons);
        $hasCustom = !empty($rejectReasonCustom);
        
        if ($hasReason || $hasCustom) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 核准通過時，請勿選擇或填寫退件原因。如需退件，請選擇「拒絕申請」'); history.back();</script>";
            exit;
        }
    }	
    
    
    // 5. 拒絕時驗證：必須有退件原因
    
    if ($reviewResult === 'reject') {
        $hasReason = !empty($rejectReasons);
        $hasCustom = !empty($rejectReasonCustom);
        
        if (!$hasReason && !$hasCustom) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 請選擇或填寫退件原因'); history.back();</script>";
            exit;
        }
    }
    
    
    // 6. 組裝審核備註
    
    $reviewComment = '';
    if ($reviewResult === 'reject') {
        $reasonNames = [];
        if (!empty($rejectReasons)) {
            $idsStr = implode(',', array_map('intval', $rejectReasons));
            $reasonList = share_gettable("reject_reasons WHERE thisid IN (" . $idsStr . ") ORDER BY sorting");
            foreach ($reasonList as $reason) {
                $reasonNames[] = $reason['thisname'];
            }
        }
        if (!empty($rejectReasonCustom)) {
            $reasonNames[] = '其他：' . $rejectReasonCustom;
        }
        $reviewComment = '退件原因：' . implode('、', $reasonNames);
    }
    
    
    // 7. 更新申請狀態
    
    $status = ($reviewResult === 'approve') ? 1 : 2;
    $approvedAt = ($status == 1) ? 'NOW()' : 'NULL';
    
    $result = share_update(
        $listtable,
        "status = '" . addslashes($status) . "',
         review_comment = '" . addslashes($reviewComment) . "',
         approved_at = " . $approvedAt . ",
         updated_at = NOW()
         ",
        "thisid = '" . addslashes($typeid) . "'"
    );
    
    if ($result) {
        
        // 8. 核准時：產生契約
        
        if ($status == 1) {
            // 取得版本資料
            $versionData = share_getinfo('gaia_product_versions', 'thisid', $apply['version_id']);			
            
            // 解析申請內容
            $appliedActions = json_decode($apply['applied_actions'] ?? '', true) ?: [];
            $appliedConstraints = json_decode($apply['applied_constraints'] ?? '', true) ?: [];
            

			// 產生契約資料
			$contractData = buildContractData(
				$typeid,
				$typeid,
				$versionData,
				$appliedActions,
				$appliedConstraints,
				$product['owner_id'],
				$apply['applicant_org_id'],
				$product['owner_id']
			);

            // 寫入契約表
share_insert(
    'gaia_contracts',
    'application_id, product_id, version_id, provider_org_id, applicant_org_id,
     contract_duration_days, effective_at, expired_at,
     allowed_actions, constraints, prohibited_actions,
     version_snapshot, application_snapshot, odrl_agreement_json,
     status, created_at',
                "'" . addslashes($typeid) . "',
                 '" . addslashes($apply['product_id']) . "',
                 '" . addslashes($apply['version_id']) . "',
                 '" . addslashes($product['owner_id']) . "',
                 '" . addslashes($apply['applicant_org_id']) . "',
                 '" . addslashes($versionData['contract_duration_days']) . "',
     '" . addslashes($contractData['effective_at']) . "',
     '" . addslashes($contractData['expired_at']) . "',
                 '" . addslashes(json_encode($appliedActions)) . "',
                 '" . addslashes(json_encode($appliedConstraints)) . "',
                 '" . addslashes($versionData['prohibited_actions']) . "',
     '" . addslashes(json_encode($contractData['version_snapshot'])) . "',
     '" . addslashes(json_encode($contractData['application_snapshot'])) . "',
     '" . addslashes(json_encode($contractData['odrl_agreement_json'])) . "',
                 1,
                 NOW()
                "
);
        }
        
        
        // 9. 寫入操作紀錄
        
        if (function_exists('writeGaiaOperationLog')) {
            $actionText = ($status == 1) ? '核准' : '拒絕';
            writeGaiaOperationLog(
                'subscription',
                $typeid,
                $product['owner_id'],
                ($status == 1) ? 'APPROVE_SUBSCRIPTION' : 'REJECT_SUBSCRIPTION',
                $actionText . '申請',
                [
                    'status' => $status,
                    'review_comment' => $reviewComment
                ],
                $actionText . '申請 - ' . $product['resource_name'],
                $userId
            );
        }
        
        
        // 10. 導回審核列表
        
        $actionText = ($status == 1) ? '核准' : '拒絕';
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 申請已" . $actionText . "！'); location.href='review_management.html?tf=" . $mypage . "&job=getContact1';</script>";
        exit;
    } else {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 審核失敗，請稍後再試'); history.back();</script>";
        exit;
    }
}
?>
	</div>
</div>
</section>
</main>
<?php 
$pdo = null;
require_once 'footer.php';?>  
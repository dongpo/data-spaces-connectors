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
$currentPage = 'product_market';
$mypage="product_market";
require_once 'seonew.php';
require_once 'headerlogin.php';
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
 }else if($job=="apply_form" ){
   $tagon=3;
   $tagshow=3;  
}else if($job=="uptypedetaillist1" ){
   $tagon=3;
   $tagshow=3;  
   $responz=uptypedetaillist1($typetable,$mypage);   
 }else{
   $tagon=1;
   $tagshow=1;	 
 }
?>
<main id="main" role="main">
<section aria-label="目錄 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="目錄 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">目錄</h1>
</div>
</section>
<section aria-label="目錄" class="detail-page-hero detail-intro-after-query">
<div class="member-container">
    <?php include 'sidebar.php'; ?>

    <div class="content-area">
<div class='contentin'>

  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>
		<a href='product_market.html?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>目錄列表</div>
        </a>
<?php 
if 	($tagon>1 ){
?>	
		<a href='product_market.html?tf=<?=$mypage?>&job=getdetaillist1&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'>發布資料明細</div>
        </a>
<?php
} 
if 	($tagon>2 ){
?>	
		<span class='tagitem  <?=($tagon==3?"on":"")?> ' >
          <div class='tagin'>申請表單</div>
        </span>
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
		}else if($tagshow==3){
			getdetail2($mypage,$typetable);				
		}	
?>		
        </div>
      </div>
   </div>
 <?php
function getdetail2($mypage, $typetable) {
    global $imgurl;
    
    $typeid = $GLOBALS['typeid'] ?? 0;
    $action = $_GET['action'] ?? 'detail';
    $myOrgId = checkUserOrganization();
    $error = '';
    $success = '';
    
    
    // 1. 檢查發布資料是否存在且已上架
    
    $product = checkDataExists($typetable, $typeid);
    if ($product['isopen'] != 1) {
        echo "<div class='error-msg'>❌ 此發布資料尚未上架</div>";
        echo "<a href='product_market.html' class='gray-btn'>← 返回目錄</a>";
        return;
    }
    
    
    // 2. 取得版本詳細資料（目前版本）
    
    $versionData = [];
    if ($product['current_version_id'] > 0) {
        $versionData = share_getinfo('gaia_product_versions', 'thisid', $product['current_version_id']);
    }
    
    
    // 3. 取得分類名稱（從版本明細抓）
    
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
    
    
    // 4. 取得提供者名稱
    
    $ownerList = share_gettable("res_entities WHERE isopen = 1 ORDER BY thisname");
    $ownerMap = [];
    foreach ($ownerList as $ind) {
        $ownerMap[$ind['thisid']] = $ind['thisname'];
    }
    $ownerName = $ownerMap[$product['owner_id']] ?? '未知';
    
    
    // 5. 查詢申請狀態
    
    $applyStatus = null;
    $applyId = 0;
    if ($myOrgId > 0) {
        $applyList = share_getfree("
            SELECT thisid, status 
            FROM gaia_subscriptions 
            WHERE product_id = '" . addslashes($typeid) . "' 
              AND applicant_org_id = '" . addslashes($myOrgId) . "'
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        if (!empty($applyList)) {
            $applyStatus = $applyList[0]['status'];
            $applyId = $applyList[0]['thisid'];
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
    
    
    // 7. 解析 JSON 欄位
    
    $allowedActions = json_decode($versionData['allowed_actions'] ?? '', true) ?: [];
    $prohibitedActions = json_decode($versionData['prohibited_actions'] ?? '', true) ?: [];
    $constraintsData = json_decode($versionData['constraints'] ?? '', true) ?: [];
    $keywords = json_decode($versionData['keywords'] ?? '', true) ?: [];
    
    // 取得可做什麼/不可做什麼的名稱對照
    $allowedList = share_gettable("gaia_allowed_actions WHERE isopen = 1 ORDER BY sorting");
    $prohibitedList = share_gettable("gaia_prohibited_actions WHERE isopen = 1 ORDER BY sorting");
    
    $allowedMap = [];
    foreach ($allowedList as $row) {
        $allowedMap[$row['thiscode']] = $row['thisname'];
    }
    $prohibitedMap = [];
    foreach ($prohibitedList as $row) {
        $prohibitedMap[$row['thiscode']] = $row['thisname'];
    }    
    
    // 發布資料詳情
    
    echo "<div class='product-detail'>";
    
    // 主區塊
    echo "<div class='detail-main'>";
    
    // 圖片
    echo "<div class='detail-image'>";
    if (!empty($product['product_image'])) {
        echo "<img src='" . htmlspecialchars($product['product_image']) . "' alt='" . htmlspecialchars($product['resource_name']) . "'>";
    } else {
        echo "<div class='detail-image-placeholder'>📦</div>";
    }
    echo "</div>";
    
    // 資訊
    $displayName = !empty($versionData['display_name']) ? $versionData['display_name'] : $product['resource_name'];
    
    echo "<div class='detail-info'>";
    echo "<h2>" . htmlspecialchars($displayName) . "</h2>";
    echo "<div class='detail-meta'>";
    echo "<span class='detail-category'>📂 " . htmlspecialchars($industryName) . "</span>";
    echo "<span class='detail-provider'>🏢 " . htmlspecialchars($ownerName) . "</span>";
    echo "<span class='detail-version'>📌 版本：" . (!empty($versionData['version']) ? htmlspecialchars($versionData['version']) : '-') . "</span>";
    echo "<span class='detail-sensitivity'>🔒 " . $sensitivity . "</span>";
    echo "</div>";
    
    echo "<div class='detail-description'>";
    echo "<h4>📝 發布資料描述</h4>";
    echo "<p>" . nl2br(htmlspecialchars($versionData['description'] ?? '')) . "</p>";
    echo "</div>";
    
    // 顯示關鍵字
    if (!empty($keywords)) {
        echo "<div class='detail-keywords'>";
        echo "<h4>🏷️ 關鍵字</h4>";
        echo "<div class='action-tags'>";
        foreach ($keywords as $kw) {
            echo "<span class='tag'>#" . htmlspecialchars($kw) . "</span>";
        }
        echo "</div>";
        echo "</div>";
    }
    
    // 顯示資料格式 + 更新頻率 + 可用性
    echo "<div class='detail-meta-info'>";
    echo "<div class='detail-meta-grid'>";
    if (!empty($versionData['data_format'])) {
        echo "<div><strong>📁 資料格式：</strong>" . htmlspecialchars($versionData['data_format']) . "</div>";
    }
    if (!empty($versionData['update_frequency'])) {
        echo "<div><strong>🔄 更新頻率：</strong>" . convertFrequency($versionData['update_frequency']) . "</div>";
    }
    if (!empty($versionData['availability'])) {
        echo "<div><strong>📊 可用性：</strong>" . htmlspecialchars($versionData['availability']) . "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    echo "</div>"; // detail-info
    echo "</div>"; // detail-main
    
    
    // 版本詳細資訊（完整）
    
    if (!empty($versionData)) {
        echo "<div class='detail-version-info'>";
        echo "<h3>📋 版本詳細資訊</h3>";
        
        echo "<TABLE class='formtable'>";
        
        
        // 版本資訊 (Version Information)
        
        echo "<tr><th colspan='2' class='section-title'>版本資訊 (Version Information)</th></tr>";
        
        echo "<tr>";
        echo "<th class='label-col'>對外顯示名稱<br><span class='field-en'>Display Name</span></th>";
        echo "<td class='input-col'>" . htmlspecialchars($versionData['display_name'] ?? '') . "</td>";
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
        echo "<td class='input-col'>" . htmlspecialchars($versionData['version'] ?? '') . "</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<th class='label-col'>關鍵字/標籤<br><span class='field-en'>Keywords</span></th>";
        echo "<td class='input-col'>";
        if (!empty($keywords)) {
            echo "<div class='action-tags'>";
            foreach ($keywords as $kw) {
                echo "<span class='tag'>#" . htmlspecialchars($kw) . "</span>";
            }
            echo "</div>";
        } else {
            echo "<span class='text-muted'>未設定</span>";
        }
        echo "</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "<th class='label-col'>資料格式<br><span class='field-en'>Data Format</span></th>";
        echo "<td class='input-col'>" . htmlspecialchars($versionData['data_format'] ?? '-') . "</td>";
        echo "</tr>";
        
        
        // 契約 (Contract)
        
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
        
        
        // 產業資料標準範本 (Industry Data Standard Template)
        
        if (!empty($schemaJson)) {
            echo "<tr><th colspan='2' class='section-title'>產業資料標準範本 (Industry Data Standard Template)</th></tr>";
            echo "<tr>";
            echo "<th class='label-col'>資料綱要(Data Schema)<br><span class='field-en'>Industry Schema</span></th>";
            echo "<td class='input-col'>";
            
            $jsonDisplay = $schemaJson;
            if (is_string($schemaJson)) {
                $decoded = json_decode($schemaJson, true);
                if ($decoded !== null) {
                    $jsonDisplay = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }
            } elseif (is_array($schemaJson)) {
                $jsonDisplay = json_encode($schemaJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
            echo "<div class='schema-json-preview'>";
            echo "<pre>" . htmlspecialchars($jsonDisplay) . "</pre>";
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
    

        
        // 使用政策 (Usage Policy)
        
        echo "<tr><th colspan='2' class='section-title'>使用政策 (Usage Policy)</th></tr>";
        
        // 可做什麼
        echo "<tr>";
        echo "<th class='label-col'>可做什麼<br><span class='field-en'>Allowed Actions</span></th>";
        echo "<td class='input-col'>";
        if (!empty($allowedActions)) {
            $selectedNames = [];
            foreach ($allowedActions as $code) {
                $selectedNames[] = $allowedMap[$code] ?? $code;
            }
            echo "<div class='action-tags'>";
            foreach ($selectedNames as $name) {
                echo "<span class='action-tag allowed'>" . htmlspecialchars($name) . "</span>";
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
            $selectedNames = [];
            foreach ($prohibitedActions as $code) {
                $selectedNames[] = $prohibitedMap[$code] ?? $code;
            }
            echo "<div class='action-tags'>";
            foreach ($selectedNames as $name) {
                echo "<span class='action-tag prohibited'>" . htmlspecialchars($name) . "</span>";
            }
            echo "</div>";
        } else {
            echo "<span class='text-muted'>未設定</span>";
        }
        echo "</td>";
        echo "</tr>";
        
        // 限制 - constraints
        echo "<tr>";
        echo "<th class='label-col'>限制<br><span class='field-en'>Constraints</span></th>";
        echo "<td class='input-col'>";
        if (!empty($constraintsData)) {
            echo "<div class='constraint-tags-display'>";
            foreach ($constraintsData as $key => $value) {
                $displayValue = is_array($value) ? implode(', ', $value) : 
                               (is_bool($value) ? ($value ? '✅ 啟用' : '❌ 停用') : $value);
                echo "<span class='constraint-tag'>";
                echo "<span class='tag-key'>" . htmlspecialchars($key) . "：</span>";
                echo "<span class='tag-value'>" . htmlspecialchars($displayValue) . "</span>";
                echo "</span>";
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
        
        
        // 技術 Metadata (Technical Metadata)
        
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
        echo "</div>";
    }
    
    
    // 申請區塊
    
    echo "<div class='detail-apply-section'>";
    
    // 顯示成功/錯誤訊息
    if (!empty($success)) {
        echo "<div class='success-msg'>" . $success . "</div>";
    }
    if (!empty($error)) {
        echo "<div class='error-msg'>" . $error . "</div>";
    }
    
    if ($applyStatus === null) {
        echo "<div class='apply-box apply-box-empty'>";
        echo "<p>📝 您尚未申請此發布資料，請填寫下方表單</p>";
        echo "</div>";
        
        echo "<div class='apply-form'>";
        echo "<form action='product_market.html?tf=".$mypage."&job=uptypedetaillist1&typeid=".$typeid."' method='post'>";
        echo "<input type='hidden' name='version_id' value='" . ($versionData['thisid'] ?? 0) . "'>";
        echo "<input type='hidden' name='provider_org_id' value='" . ($product['owner_id'] ?? 0) . "'>";
        
        // 可做什麼
        echo "<div class='form-group'>";
        echo "<label>📋 可做什麼（請取消您不需要的權限）</label>";
        echo "<div class='checkbox-group'>";
        if (!empty($allowedActions)) {
            foreach ($allowedActions as $actionCode) {
                $actionName = $allowedMap[$actionCode] ?? $actionCode;
                echo "<label class='checkbox-label'>";
                echo "<input type='checkbox' name='applied_actions[]' value='" . htmlspecialchars($actionCode) . "' checked>";
                echo htmlspecialchars($actionName);
                echo "</label>";
            }
        } else {
            echo "<span class='text-muted'>此版本未設定可做什麼</span>";
        }
        echo "</div>";
        echo "</div>";

        // 限制
        if (!empty($constraintsData)) {
            echo "<div class='form-group'>";
            echo "<label>📊 使用限制（請輸入您的需求，不得超過版本限制，超過則需要審核）</label>";
            echo "<div class='constraint-inputs'>";
            
            foreach ($constraintsData as $key => $maxValue) {
                $fieldName = 'constraint_' . $key;
                
                if (is_bool($maxValue)) {
                    $statusText = $maxValue ? '已啟用' : '已停用';
                    echo "<div class='constraint-input-row constraint-readonly'>";
                    echo "<span class='constraint-label'>" . htmlspecialchars($key) . "：</span>";
                    echo "<span class='max-hint'>（版本限制：" . $statusText . "，系統自動套用）</span>";
                    echo "<input type='hidden' name='" . $fieldName . "' value='" . ($maxValue ? '1' : '0') . "'>";
                    echo "</div>";
                    continue;
                }
                
                $displayValue = is_array($maxValue) ? implode(', ', $maxValue) : $maxValue;
                
                echo "<div class='constraint-input-row'>";
                echo "<span class='constraint-label'>" . htmlspecialchars($key) . "：</span>";
                echo "<span class='max-hint'>（最大 " . htmlspecialchars($displayValue) . "）</span>";
                
                if (is_array($maxValue)) {
                    echo "<input type='text' name='" . $fieldName . "[]' placeholder='請輸入值...'>";
                } else {
                    $inputType = is_numeric($maxValue) ? 'number' : 'text';
//                    $maxAttr = is_numeric($maxValue) ? " max='" . htmlspecialchars($maxValue) . "'" : "";
                    echo "<input type='" . $inputType . "' name='" . $fieldName . "' placeholder='請輸入數值...'" . $maxAttr . ">";
                }
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
        }
        
        echo "<div class='form-actions'>";
        echo "<button type='submit' class='green-btn'>📝 送出申請</button>";
        echo "</div>";
        
        echo "</form>";
        echo "</div>";
        
    } elseif ($applyStatus == 0) {
        echo "<div class='apply-box apply-box-pending'>";
        echo "<p>⏳ 您的申請正在審核中，請耐心等待</p>";
        echo "<span class='btn-disabled'>⏳ 審核中</span>";
        echo "</div>";
        
    } elseif ($applyStatus == 1) {
        echo "<div class='apply-box apply-box-approved'>";
        echo "<p>✅ 您的申請已核准！</p>";
        echo "<span class='btn-disabled'>✅ 已核准</span>";
        echo "</div>";
        
    } elseif ($applyStatus == 2) {
        echo "<div class='apply-box apply-box-rejected'>";
        echo "<p>❌ 您的申請已被拒絕，請重新填寫下方表單</p>";
        echo "</div>";
        
        echo "<div class='apply-form'>";
        echo "<form action='product_market.html?tf=".$mypage."&job=uptypedetaillist1&typeid=".$typeid."' method='post'>";
        echo "<input type='hidden' name='version_id' value='" . ($versionData['thisid'] ?? 0) . "'>";
        echo "<input type='hidden' name='provider_org_id' value='" . ($product['owner_id'] ?? 0) . "'>";
        
        echo "<div class='form-group'>";
        echo "<label>📋 可做什麼（請取消您不需要的權限）</label>";
        echo "<div class='checkbox-group'>";
        if (!empty($allowedActions)) {
            foreach ($allowedActions as $actionCode) {
                $actionName = $allowedMap[$actionCode] ?? $actionCode;
                echo "<label class='checkbox-label'>";
                echo "<input type='checkbox' name='applied_actions[]' value='" . htmlspecialchars($actionCode) . "' checked>";
                echo htmlspecialchars($actionName);
                echo "</label>";
            }
        } else {
            echo "<span class='text-muted'>此版本未設定可做什麼</span>";
        }
        echo "</div>";
        echo "</div>";

        if (!empty($constraintsData)) {
            echo "<div class='form-group'>";
            echo "<label>📊 使用限制（請輸入您的需求，不得超過版本限制，超過則需要審核）</label>";
            echo "<div class='constraint-inputs'>";
            
            foreach ($constraintsData as $key => $maxValue) {
                $fieldName = 'constraint_' . $key;
                $displayValue = is_array($maxValue) ? implode(', ', $maxValue) : $maxValue;
                
                echo "<div class='constraint-input-row'>";
                echo "<span class='constraint-label'>" . htmlspecialchars($key) . "：</span>";
                echo "<span class='max-hint'>（最大 " . htmlspecialchars($displayValue) . "）</span>";
                
                if (is_array($maxValue)) {
                    echo "<input type='text' name='" . $fieldName . "[]' placeholder='請輸入值...'>";
                } else {
                    $inputType = is_numeric($maxValue) ? 'number' : 'text';
                //    $maxAttr = is_numeric($maxValue) ? " max='" . htmlspecialchars($maxValue) . "'" : "";
                    echo "<input type='" . $inputType . "' name='" . $fieldName . "' placeholder='請輸入數值...'" . $maxAttr . ">";
                }
                echo "</div>";
            }
            echo "</div>";
            echo "</div>";
        }
        
        echo "<div class='form-actions'>";
        echo "<button type='submit' class='green-btn'>📝 送出申請</button>";
        echo "</div>";
        
        echo "</form>";
        echo "</div>";
    }
    
    echo "</div>"; // detail-apply-section
    
    echo "</div>"; // product-detail
}

function getContact1($mypage, $typetable) {
    global $imgurl;
    
    
    // 1. 取得登入者組織ID
    
    $myOrgId = checkUserOrganization();
    
    
    // 2. 取得搜尋條件
    
    $keyword = trim($_GET['keyword'] ?? $_POST['keyword'] ?? '');
    $industryFilter = $_GET['industry_id'] ?? $_POST['industry_id'] ?? '';

    
    // 3. 取得分類表
    
    $industryList1 = share_gettable("gaia_industry WHERE isopen = 1 ORDER BY thisid");
	$mappingList = share_getfree("
        SELECT gsv.thisid AS l3_id, ind.thisname AS industry_name 
        FROM gaia_schema_version gsv
        JOIN gaia_schema gs ON gsv.schema_id = gs.thisid
        JOIN gaia_industry ind ON gs.industry_id = ind.thisid
        WHERE ind.isopen = 1
    ");
    
    $industryMap = [];
    foreach ($mappingList as $map) {
        $industryMap[$map['l3_id']] = $map['industry_name'];
    }

    
    // 4. 取得提供者對照表
    
    $ownerList = share_gettable("res_entities WHERE isopen = 1 ORDER BY thisname");
    $ownerMap = [];
    foreach ($ownerList as $ind) {
        $ownerMap[$ind['thisid']] = $ind['thisname'];
    }

    
    // 5. 組合查詢條件
    //    p.isopen = 1：發布資料已上架
    //    pv.isopen = 2：版本是「目前版本」
    
    $mycheck = " WHERE p.isopen = 1 AND pv.isopen = 2 ";
    $mycheck .= " AND p.current_version_id > 0 ";

    if (!empty($keyword)) {
        $mycheck .= " AND (pv.display_name LIKE '%" . addslashes($keyword) . "%' OR pv.description LIKE '%" . addslashes($keyword) . "%' OR pv.keywords LIKE '%" . addslashes($keyword) . "%' )";
    }

if (!empty($industryFilter)) {
        // 透過 pv 關聯到第三層版次 (gsv)，再關聯到第二層 Schema (gs)，最後過濾第一層的 industry_id
        $mycheck .= " AND pv.industry_id IN (
            SELECT gsv.thisid 
            FROM gaia_schema_version gsv
            JOIN gaia_schema gs ON gsv.schema_id = gs.thisid
            WHERE gs.industry_id = '" . addslashes($industryFilter) . "'
        )";
    }
    
    // 6. 抓發布資料列表（從版本明細抓 display_name, description, industry_id）
    
$productList = share_getfree("
    SELECT 
        p.thisid, 
        p.resource_id, 
        p.resource_name, 
        p.owner_id, 
        p.current_version_id, 
        p.current_version, 
        p.isopen, 
        p.product_image, 
        p.dateadd, 
        pv.display_name, 
        pv.industry_id, 
        pv.description, 
        pv.version, 
        pv.keywords, 
        pv.data_format, 
        pv.allowed_actions, 
        pv.prohibited_actions, 
        pv.constraints, 
        pv.data_sensitivity_level, 
        pv.update_frequency, 
        pv.availability 
    FROM " . $typetable . " p 
    INNER JOIN gaia_product_versions pv ON p.current_version_id = pv.thisid 
    " . $mycheck . " 
    ORDER BY p.dateadd DESC
");
    // 把分類名稱配上去
    foreach ($productList as &$row) {
        $row['industry_name'] = $industryMap[$row['industry_id']] ?? '';
        // 解析 keywords JSON 為陣列（方便顯示標籤）
        $row['keywords_array'] = json_decode($row['keywords'] ?? '', true) ?: [];
    }
    unset($row);

    
    // 7. 抓這家公司所有的申請（一次抓完）
    
    $applyMap = []; // [product_id => status]
    if (!empty($productList)) {
        $productIds = array_column($productList, 'thisid');
        $idsStr = implode(',', array_map('intval', $productIds));
        
        if (!empty($idsStr)) {
            $appliedList = share_getfree("
                SELECT product_id, status 
                FROM gaia_subscriptions 
                WHERE applicant_org_id = '" . addslashes($myOrgId) . "' 
                  AND product_id IN (" . $idsStr . ")
            ");
            
            foreach ($appliedList as $app) {
                $applyMap[$app['product_id']] = $app['status'];
            }
        }
    }

    
    // 8. 顯示畫面
    
    
    // 搜尋區塊
    echo "<div class='search-area'>";
    echo "<div class='search-left'>";
    echo "<div class='search-item search-keyword'>";
    echo "<label>🔍 關鍵字</label>";
    echo "<input type='text' name='keyword' value='" . htmlspecialchars($keyword) . "' class='input-full' placeholder='請輸入發布資料名稱或描述...' form='searchForm'>";
    echo "</div>";
    echo "<div class='search-item'>";
    echo "<label>📂 目錄分類</label>";
    echo "<select name='industry_id' form='searchForm'>";
    echo "<option value=''>全部目錄</option>";
    foreach ($industryList1 as $ind) {
        $selected = ($ind['thisid'] == $industryFilter) ? "selected" : "";
        echo "<option value='" . $ind['thisid'] . "' $selected>" . $ind['thisname'] . "</option>";
    }
    echo "</select>";
    echo "</div>";
    echo "</div>";
    echo "<div class='search-right'>";
    echo "<form action='product_market.html?tf=".$mypage."&job=getContact1' method='post' id='searchForm'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 搜尋</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
    echo "</div>";

    // 發布資料列表
    if (!empty($productList)) {
        echo "<div class='market-grid'>";
        foreach ($productList as $row) {
            $sensitivityLabels = [
                0 => '公開',
                1 => '內部',
                2 => '機密',
                3 => '高度機密',
                4 => '需人工審核'
            ];
            $sensitivity = $sensitivityLabels[$row['data_sensitivity_level']] ?? '未設定';
            $ownerName = $ownerMap[$row['owner_id']] ?? '未知';
            
            // 申請狀態
            $applyStatus = $applyMap[$row['thisid']] ?? null;
            
            // 狀態標籤
            $statusLabel = '';
            $statusClass = '';
            if ($applyStatus === null) {
                $statusLabel = '📝 未申請';
                $statusClass = 'status-none';
            } elseif ($applyStatus == 0) {
                $statusLabel = '⏳ 審核中';
                $statusClass = 'status-pending';
            } elseif ($applyStatus == 1) {
                $statusLabel = '✅ 已核准';
                $statusClass = 'status-approved';
            } elseif ($applyStatus == 2) {
                $statusLabel = '❌ 已拒絕';
                $statusClass = 'status-rejected';
            }
            
            // 顯示名稱：優先使用 display_name（對外名稱），如果沒有則用 resource_name
            $displayName = !empty($row['display_name']) ? $row['display_name'] : $row['resource_name'];
            
            echo "<div class='market-card'>";
            
            // 圖片
            echo "<div class='market-card-image'>";
            if (!empty($row['product_image'])) {
                echo "<img src='" . htmlspecialchars($row['product_image']) . "' alt='" . htmlspecialchars($displayName) . "'>";
            } else {
                echo "<div class='market-card-image-placeholder'>📦</div>";
            }
            echo "</div>";
            
            // 內容
            echo "<div class='market-card-body'>";
            echo "<div class='market-card-header'>";
            echo "<h3>" . htmlspecialchars($displayName) . "</h3>";
            echo "<span class='market-card-category'>" . htmlspecialchars($row['industry_name']) . "</span>";
            echo "</div>";
            
            $desc = $row['description'] ?? '';
            if (mb_strlen($desc) > 80) {
                $desc = mb_substr($desc, 0, 80) . '...';
            }
            echo "<p class='market-card-description'>" . htmlspecialchars($desc) . "</p>";
            
            echo "<div class='market-card-meta'>";
            echo "<span class='meta-item'>📌 版本：" . (!empty($row['version']) ? htmlspecialchars($row['version']) : '-') . "</span>";
            echo "<span class='meta-item'>🏢 提供者：" . htmlspecialchars($ownerName) . "</span>";
            echo "<span class='meta-item'>📊 更新：" . (!empty($row['update_frequency']) ? htmlspecialchars($row['update_frequency']) : '-') . "</span>";
            echo "</div>";
            
            // 顯示關鍵字標籤
            if (!empty($row['keywords_array'])) {
                echo "<div class='market-card-tags'>";
                foreach (array_slice($row['keywords_array'], 0, 5) as $kw) {
                    echo "<span class='tag'>#" . htmlspecialchars($kw) . "</span>";
                }
                if (count($row['keywords_array']) > 5) {
                    echo "<span class='tag'>+ " . (count($row['keywords_array']) - 5) . "...</span>";
                }
                echo "</div>";
            }
            
            // 敏感度標籤
            echo "<div class='market-card-tags market-card-tags-top'>";
            echo "<span class='tag tag-sensitivity'>🔒 " . $sensitivity . "</span>";
            if (!empty($row['availability'])) {
                echo "<span class='tag tag-availability'>📊 " . htmlspecialchars($row['availability']) . "</span>";
            }
            if (!empty($row['data_format'])) {
                echo "<span class='tag'>📁 " . htmlspecialchars($row['data_format']) . "</span>";
            }
            echo "</div>";
            
            // 狀態標籤
            echo "<div class='market-card-status'>";
            echo "<span class='status-badge " . $statusClass . "'>" . $statusLabel . "</span>";
            echo "</div>";
            
            echo "</div>"; // market-card-body
            
            // 底部按鈕（查看詳情）
            echo "<div class='market-card-footer'>";
            echo "<a href='product_market.html?tf=".$mypage."&job=getdetaillist1&typeid=" . $row['thisid'] . "' class='blue-btn'>📋 查看詳情</a>";
            echo "</div>";
            
            echo "</div>"; // market-card
        }
        echo "</div>";
        echo "<div class='table-footer'>顯示 " . count($productList) . " 筆</div>";
    } else {
        echo "<div class='empty-state'>";
        echo "<div class='empty-icon'>📭</div>";
        echo "<h3>目前沒有已上架的發布資料</h3>";
        echo "<p>目前還沒有任何已上架的資料發布資料，請稍後再來瀏覽。</p>";
        echo "</div>";
    }
}



function getdetail1($mypage, $typetable) {
    global $imgurl;
    
    $typeid = $GLOBALS['typeid'] ?? 0;
    $action = $_GET['action'] ?? 'detail';
    $myOrgId = checkUserOrganization();
    
    
    // 1. 檢查發布資料是否存在且已上架
    
    $product = checkDataExists($typetable, $typeid);
    if ($product['isopen'] != 1) {
        echo "<div class='error-msg'>❌ 此發布資料尚未上架</div>";
        echo "<a href='product_market.html' class='gray-btn'>← 返回目錄</a>";
        return;
    }
    
    
    // 2. 取得版本詳細資料（目前版本）
    
    $versionData = [];
    if ($product['current_version_id'] > 0) {
        $versionData = share_getinfo('gaia_product_versions', 'thisid', $product['current_version_id']);
    }
    
    
// 3. 取得目錄分類 / Schema 範本 / 版本與 JSON（從版本 ID 反推 3 層資料）

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
    
    // 4. 取得提供者名稱
    
    $ownerList = share_gettable("res_entities WHERE isopen = 1 ORDER BY thisname");
    $ownerMap = [];
    foreach ($ownerList as $ind) {
        $ownerMap[$ind['thisid']] = $ind['thisname'];
    }
    $ownerName = $ownerMap[$product['owner_id']] ?? '未知';
    
    
    // 5. 查詢申請狀態
    
    $applyStatus = null;
    $applyId = 0;
    if ($myOrgId > 0) {
        $applyList = share_gettable("gaia_subscriptions 
            WHERE product_id = '" . addslashes($typeid) . "' 
              AND applicant_org_id = '" . addslashes($myOrgId) . "'
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        if (!empty($applyList)) {
            $applyStatus = $applyList[0]['status'];
            $applyId = $applyList[0]['thisid'];
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
    
    
    // 7. 解析 JSON 欄位
    
    $allowedActions = json_decode($versionData['allowed_actions'] ?? '', true) ?: [];
    $prohibitedActions = json_decode($versionData['prohibited_actions'] ?? '', true) ?: [];
    $constraintsData = json_decode($versionData['constraints'] ?? '', true) ?: [];
    $keywords = json_decode($versionData['keywords'] ?? '', true) ?: [];
    
    // 取得可做什麼/不可做什麼的名稱對照
    $allowedList = share_gettable("gaia_allowed_actions WHERE isopen = 1 ORDER BY sorting");
    $prohibitedList = share_gettable("gaia_prohibited_actions WHERE isopen = 1 ORDER BY sorting");
    
    $allowedMap = [];
    foreach ($allowedList as $row) {
        $allowedMap[$row['thiscode']] = $row['thisname'];
    }
    $prohibitedMap = [];
    foreach ($prohibitedList as $row) {
        $prohibitedMap[$row['thiscode']] = $row['thisname'];
    }
    
    
    // 發布資料詳情
    
    echo "<div class='product-detail'>";
    
    // 主區塊
    echo "<div class='detail-main'>";
    
    // 圖片
    echo "<div class='detail-image'>";
    if (!empty($product['product_image'])) {
        echo "<img src='" . htmlspecialchars($product['product_image']) . "' alt='" . htmlspecialchars($product['resource_name']) . "'>";
    } else {
        echo "<div class='detail-image-placeholder'>📦</div>";
    }
    echo "</div>";
    
    // 資訊
    $displayName = !empty($versionData['display_name']) ? $versionData['display_name'] : $product['resource_name'];
    
    echo "<div class='detail-info'>";
    echo "<h2>" . htmlspecialchars($displayName) . "</h2>";
    echo "<div class='detail-meta'>";
    echo "<span class='detail-category'>📂 " . htmlspecialchars($industryName) . "</span>";
    echo "<span class='detail-provider'>🏢 " . htmlspecialchars($ownerName) . "</span>";
    echo "<span class='detail-version'>📌 版本：" . (!empty($versionData['version']) ? htmlspecialchars($versionData['version']) : '-') . "</span>";
    echo "<span class='detail-sensitivity'>🔒 " . $sensitivity . "</span>";
    echo "</div>";
    
    echo "<div class='detail-description'>";
    echo "<h4>📝 發布資料描述</h4>";
    echo "<p>" . nl2br(htmlspecialchars($versionData['description'] ?? '')) . "</p>";
    echo "</div>";
    
    // 顯示關鍵字
    if (!empty($keywords)) {
        echo "<div class='detail-keywords'>";
        echo "<h4>🏷️ 關鍵字</h4>";
        echo "<div class='action-tags'>";
        foreach ($keywords as $kw) {
            echo "<span class='tag'>#" . htmlspecialchars($kw) . "</span>";
        }
        echo "</div>";
        echo "</div>";
    }
    
    // 顯示資料格式 + 連接器
    echo "<div class='detail-meta-info'>";
    echo "<div class='detail-meta-grid'>";
    if (!empty($versionData['data_format'])) {
        echo "<div><strong>📁 資料格式：</strong>" . htmlspecialchars($versionData['data_format']) . "</div>";
    }
    if (!empty($versionData['update_frequency'])) {
        echo "<div><strong>🔄 更新頻率：</strong>" . htmlspecialchars($versionData['update_frequency']) . "</div>";
    }
    if (!empty($versionData['availability'])) {
        echo "<div><strong>📊 可用性：</strong>" . htmlspecialchars($versionData['availability']) . "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    echo "</div>"; // detail-info
    echo "</div>"; // detail-main
    
    
    // 版本詳細資訊（完整）
    
    if (!empty($versionData)) {
        echo "<div class='detail-version-info'>";
        echo "<h3>📋 版本詳細資訊</h3>";
        
        echo "<TABLE class='formtable'>";
        
        
        // 版本資訊 (Version Information)
        
echo "<tr><th colspan='2' class='section-title'>版本資訊 (Version Information)</th></tr>";

echo "<tr>";
echo "<th class='label-col'>對外顯示名稱<br><span class='field-en'>Display Name</span></th>";
echo "<td class='input-col'>" . htmlspecialchars($versionData['display_name'] ?? '') . "</td>";
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

// 呈現 2：資料綱要(Data Schema) 預覽 (直接渲染 gaia_schema_version 裡的 schema_json)
echo "<tr>";
echo "<th class='label-col'>資料綱要(Data Schema)<br><span class='field-en'>Industry Schema JSON</span></th>";
echo "<td class='input-col'>";

$jsonDisplay = $schemaJson;
if (is_string($schemaJson)) {
    $decoded = json_decode($schemaJson, true);
    if ($decoded !== null) {
        $jsonDisplay = json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
} elseif (is_array($schemaJson)) {
    $jsonDisplay = json_encode($schemaJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

echo "<div class='schema-json-preview'>";
echo "<pre style='max-height: 300px; overflow-y: auto; background-color: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 4px;'>" . htmlspecialchars($jsonDisplay) . "</pre>";
echo "</div>";
echo "</td>";
echo "</tr>";
	
	

echo "<tr>";
echo "<th class='label-col'>發布資料描述<br><span class='field-en'>Description</span></th>";
echo "<td class='input-col'>" . nl2br(htmlspecialchars($versionData['description'] ?? '')) . "</td>";
echo "</tr>";

echo "<tr>";
echo "<th class='label-col'>版本號<br><span class='field-en'>Version</span></th>";
echo "<td class='input-col'>" . htmlspecialchars($versionData['version'] ?? '') . "</td>";
echo "</tr>";

echo "<tr>";
echo "<th class='label-col'>關鍵字/標籤<br><span class='field-en'>Keywords</span></th>";
echo "<td class='input-col'>";
if (!empty($keywords)) {
    echo "<div class='action-tags'>";
    foreach ($keywords as $kw) {
        echo "<span class='tag'>#" . htmlspecialchars($kw) . "</span>";
    }
    echo "</div>";
} else {
    echo "<span class='text-muted'>未設定</span>";
}
echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<th class='label-col'>資料格式<br><span class='field-en'>Data Format</span></th>";
echo "<td class='input-col'>" . htmlspecialchars($versionData['data_format'] ?? '-') . "</td>";
echo "</tr>";


// 契約 (Contract)

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


// 使用政策 (Usage Policy)

echo "<tr><th colspan='2' class='section-title'>使用政策 (Usage Policy)</th></tr>";

// 可做什麼
echo "<tr>";
echo "<th class='label-col'>可做什麼<br><span class='field-en'>Allowed Actions</span></th>";
echo "<td class='input-col'>";
if (!empty($allowedActions)) {
    $selectedNames = [];
    foreach ($allowedActions as $code) {
        $selectedNames[] = $allowedMap[$code] ?? $code;
    }
    echo "<div class='action-tags'>";
    foreach ($selectedNames as $name) {
        echo "<span class='action-tag allowed'>" . htmlspecialchars($name) . "</span>";
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
    $selectedNames = [];
    foreach ($prohibitedActions as $code) {
        $selectedNames[] = $prohibitedMap[$code] ?? $code;
    }
    echo "<div class='action-tags'>";
    foreach ($selectedNames as $name) {
        echo "<span class='action-tag prohibited'>" . htmlspecialchars($name) . "</span>";
    }
    echo "</div>";
} else {
    echo "<span class='text-muted'>未設定</span>";
}
echo "</td>";
echo "</tr>";

// 限制 - constraints
echo "<tr>";
echo "<th class='label-col'>限制<br><span class='field-en'>Constraints</span></th>";
echo "<td class='input-col'>";
if (!empty($constraintsData)) {
    echo "<div class='constraint-tags-display'>";
    foreach ($constraintsData as $key => $value) {
        $displayValue = is_array($value) ? implode(', ', $value) : 
                       (is_bool($value) ? ($value ? '✅ 啟用' : '❌ 停用') : $value);
        echo "<span class='constraint-tag'>";
        echo "<span class='tag-key'>" . htmlspecialchars($key) . "：</span>";
        echo "<span class='tag-value'>" . htmlspecialchars($displayValue) . "</span>";
        echo "</span>";
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


// 技術 Metadata (Technical Metadata)

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
        echo "</div>";

    }
    
    
    // 申請區塊
    

// 檢查是否為自己的發布資料

$myOrgId = checkUserOrganization();
$isOwnProduct = ($product['owner_id'] == $myOrgId);


// 申請區塊

echo "<div class='detail-apply-section'>";

if ($isOwnProduct) {
    // 自己的發布資料 → 不顯示申請按鈕
    echo "<div class='apply-box apply-box-own'>";
    echo "<p>ⓘ 這是您自己提供的發布資料</p>";
    echo "</div>";
} elseif ($applyStatus === null) {
    // 尚未申請 → 顯示申請表單
    echo "<div class='apply-box apply-box-empty'>";
    echo "<p>📝 您尚未申請此發布資料</p>";
    echo "<a href='product_market.html?tf=".$mypage."&job=apply_form&typeid=" . $typeid . "' class='green-btn'>📝 我要使用</a>";
    echo "</div>";
} elseif ($applyStatus == 0) {
    // 待審核
    echo "<div class='apply-box apply-box-pending'>";
    echo "<p>⏳ 您的申請正在審核中，請耐心等待</p>";
    echo "<span class='btn-disabled'>⏳ 審核中</span>";
    echo "</div>";
} elseif ($applyStatus == 1) {
    // 已核准
    echo "<div class='apply-box apply-box-approved'>";
    echo "<p>✅ 您的申請已核准！</p>";
    echo "<span class='btn-disabled'>✅ 已核准</span>";
    echo "</div>";
} elseif ($applyStatus == 2) {
    // 已拒絕 → 可重新申請
    echo "<div class='apply-box apply-box-rejected'>";
    echo "<p>❌ 您的申請已被拒絕，可重新申請</p>";
    echo "<a href='product_market.html?tf=".$mypage."&job=apply_form&typeid=" . $typeid . "' class='green-btn'>📝 重新申請</a>";
    echo "</div>";
}

echo "</div>";
    
    echo "</div>"; // product-detail
}



function uptypedetaillist1($mytable, $mypage) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    
    
    // 1. 取得表單資料
    
    $version_id = $GLOBALS['version_id'] ?? 0;
    $provider_org_id = $GLOBALS['provider_org_id'] ?? 0;
    $applied_actions = $_POST['applied_actions'] ?? [];
    
    
    // 2. 檢查發布資料是否存在且已上架
    
    $product = checkDataExists($mytable, $typeid);
    if ($product['isopen'] != 1) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 此發布資料尚未上架'); history.back();</script>";
        exit;
    }
    
    
    // 3. 取得版本資料（用於檢查敏感等級 + constraints）
    
    $versionData = share_getinfo('gaia_product_versions', 'thisid', $version_id);
    if (empty($versionData)) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 版本資料不存在'); history.back();</script>";
        exit;
    }
    
    
    // 4. 組裝 applied_constraints（從 constraint_* 前綴的欄位收集）
    
    $appliedConstraints = [];
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'constraint_') === 0) {
            $constraintKey = substr($key, 11);
            if (is_array($value)) {
                $filtered = array_filter($value, function($v) {
                    return $v !== '' && $v !== null;
                });
                if (!empty($filtered)) {
                    $appliedConstraints[$constraintKey] = $filtered;
                }
            } else {
                if ($value !== '' && $value !== null) {
                    $appliedConstraints[$constraintKey] = $value;
                }
            }
        }
    }
    
    
    // 5. 驗證：限制值不能超過版本上限 + 時間格式檢查
    
    $versionConstraints = json_decode($versionData['constraints'] ?? '', true) ?: [];
    
    if (!empty($versionConstraints)) {
        $errors = [];
        $errors1 = [];
        foreach ($versionConstraints as $key => $maxValue) {
            if (is_bool($maxValue) || is_array($maxValue)) {
                continue;
            }
            
            if (!isset($appliedConstraints[$key]) || $appliedConstraints[$key] === '' || $appliedConstraints[$key] === null) {
                $errors1[] = $key . '（請填寫數值）';
                continue;
            }
            
            $userValue = $appliedConstraints[$key];
            
            if (is_numeric($maxValue) && is_numeric($userValue)) {
                if ($userValue > $maxValue) {
                    $errors[] = $key . '（上限 ' . $maxValue . '，您填了 ' . $userValue . '）';
                }
            }
            
            if ($key === 'time_window' && !empty($userValue)) {
                if (!preg_match('/^\d{2}:\d{2}-\d{2}:\d{2}$/', $userValue)) {
                    $errors[] = $key . '（格式請用 HH:MM-HH:MM，例如 09:00-17:00）';
                } else {
                    list($userStart, $userEnd) = explode('-', $userValue);
                    
                    if ($userStart >= $userEnd) {
                        $errors[] = $key . '（開始時間必須早於結束時間）';
                    }
                    
                    if (strpos($maxValue, '-') !== false) {
                        list($maxStart, $maxEnd) = explode('-', $maxValue);
                        
                        if ($userStart < $maxStart) {
                            $errors[] = $key . '（開始時間不得早於 ' . $maxStart . '，您填了 ' . $userStart . '）';
                        }
                        
                        if ($userEnd > $maxEnd) {
                            $errors[] = $key . '（結束時間不得晚於 ' . $maxEnd . '，您填了 ' . $userEnd . '）';
                        }
                    }
                }
            }
        }
        
        if (!empty($errors1)) {
            $msg = "❌ 申請資料有誤：\n• " . implode("\n• ", $errors1);
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert(" . json_encode($msg) . "); history.back();</script>";
            exit;
       }
    }
    
    
    // 6. 驗證：至少要勾選一個權限
    
    if (empty($applied_actions)) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 請至少勾選一個「可做什麼」權限'); history.back();</script>";
        exit;
    }
    
    
    // 7. 檢查是否已申請（防重複）
    
    $myOrgId = checkUserOrganization();
    $appliedList = share_getfree("
        SELECT thisid, status 
        FROM gaia_subscriptions 
        WHERE product_id = '" . addslashes($typeid) . "' 
          AND applicant_org_id = '" . addslashes($myOrgId) . "'
          AND status IN (0, 1)
    ");
    
    if (!empty($appliedList)) {
        $status = $appliedList[0]['status'];
        if ($status == 0) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⏳ 您已申請此發布資料，正在審核中'); history.back();</script>";
            exit;
        } elseif ($status == 1) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 您已核准使用此發布資料'); history.back();</script>";
            exit;
        }
    }
    
    
    // 8. 組裝 JSON 欄位
    
    $appliedActionsJson = json_encode($applied_actions);
    
    $appliedConstraintsJson = 'NULL';
    if (!empty($appliedConstraints)) {
        $appliedConstraintsJson = "'" . addslashes(json_encode($appliedConstraints)) . "'";
    }
    
    
    // 9. 判斷敏感等級，決定是否自動核准
    
    $sensitivityLevel = $versionData['data_sensitivity_level'] ?? 0;
	
	if (!empty($errors)) {
		$status =0;
	}else{
		$status = ($sensitivityLevel >= 4) ? 0 : 1;
    }
	$approvedAt = ($status == 1) ? 'NOW()' : 'NULL';
    
    
    // 10. 寫入申請紀錄
    
    $result = share_insert(
        'gaia_subscriptions',
        'product_id, version_id, applicant_org_id, provider_org_id, applied_actions, applied_constraints, status, approved_at, created_at',
        "'" . addslashes($typeid) . "',
         '" . addslashes($version_id) . "',
         '" . addslashes($myOrgId) . "',
         '" . addslashes($provider_org_id) . "',
         '" . addslashes($appliedActionsJson) . "',
         " . $appliedConstraintsJson . ",
         '" . addslashes($status) . "',
         " . $approvedAt . ",
         NOW()
        "
    );
    
    if ($result) {
        $temp = share_getfree("SELECT LAST_INSERT_ID() as myid");
        $applyId = $temp[0]['myid'];
        
        
        // 自動核准後 → 產生契約
        
        if ($status == 1) {

			// 產生契約資料
			$contractData = buildContractData(
				$typeid,
				$applyId,
				$versionData,
				$applied_actions,
				$appliedConstraints,
				$provider_org_id,
				$myOrgId,
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
    "'" . addslashes($applyId) . "',
     '" . addslashes($typeid) . "',
     '" . addslashes($version_id) . "',
     '" . addslashes($provider_org_id) . "',
     '" . addslashes($myOrgId) . "',
     '" . addslashes($versionData['contract_duration_days']) . "',
     '" . addslashes($contractData['effective_at']) . "',
     '" . addslashes($contractData['expired_at']) . "',
     '" . addslashes(json_encode($applied_actions)) . "',
     " . $appliedConstraintsJson . ",
     '" . addslashes($versionData['prohibited_actions']) . "',
     '" . addslashes(json_encode($contractData['version_snapshot'])) . "',
     '" . addslashes(json_encode($contractData['application_snapshot'])) . "',
     '" . addslashes(json_encode($contractData['odrl_agreement_json'])) . "',
     1,
     NOW()
    "
);
        }
        
        
        // 11. 寫入操作紀錄
        
        if (function_exists('writeGaiaOperationLog')) {
            $actionText = ($status == 1) ? '自動核准' : '待審核';
            writeGaiaOperationLog(
                'subscription',
                $applyId,
                $product['owner_id'],
                'CREATE_SUBSCRIPTION',
                '申請使用發布資料：' . $product['resource_name'] . ' (' . $actionText . ')',
                [
                    'product_id' => $typeid,
                    'version_id' => $version_id,
                    'applied_actions' => $applied_actions,
                    'applied_constraints' => $appliedConstraints,
                    'sensitivity_level' => $sensitivityLevel,
                    'status' => $status
                ],
                '送出申請 - ' . $actionText,
                $userId
            );
        }
        
        
        // 12. 回傳成功訊息
        
        if ($status == 1) {
            $msg = "✅ 申請已自動核准！您現在可以使用此發布資料";
            $redirect = 'product_market.html?tf=' . $mypage . '&job=getdetail2&typeid=' . $typeid;
        } else {
            $msg = "✅ 申請已送出！請等待提供者審核";
            $redirect = 'my_applications.html';
        }
        
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('" . $msg . "'); location.href='" . $redirect . "';</script>";
        exit;
    } else {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 申請失敗，請稍後再試'); history.back();</script>";
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
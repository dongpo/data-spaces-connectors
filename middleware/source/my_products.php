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
$isviewAudit=1;
$currentPage = 'my_products';
require_once 'seonew.php';
require_once 'headerlogin.php';
require_once 'adm/config/product.php';
checkPagePermission('my_products');
$responz="";
global $imgurl;
$mypage="ProductController";
$typetable="gaia_data_products";     //資料庫主table
$listtable="gaia_product_versions";    //資料庫明細table
 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;
 }elseif($job=="deleteProduct"){	   
   $tagon=1;
   $tagshow=1;   
   deleteProduct($mypage, $typetable);
 }elseif($job=="toggleProductStatus"){	   
   $tagon=1;
   $tagshow=1;   
   toggleProductStatus($mypage, $typetable);
 }elseif($job=="getdetaillist1"){	
   $tagon=2;
   $tagshow=2;
 }else if($job=="uptypedetaillist1" ){
   $tagon=2;
   $tagshow=2;  
   $responz=uptypedetaillist1($typetable,$mypage);
 }elseif($job=="getContact2"){   
   $tagon=3;
   $tagshow=3;
 }elseif($job=="deleteVersion"){	   
   $tagon=1;
   $tagshow=1;   
   deleteVersion($mypage, $listtable); 
 }elseif($job=="getdetaillist2"){	
   $tagon=4;
   $tagshow=4; 
 }else if($job=="updadetaillist2" ){
   $tagon=4;
   $tagshow=4;  
   $responz=updadetaillist2($mypage,$listtable,$typetable);

 }else if($job=="toggleVersionStatus" ){
   $tagon=3;
   $tagshow=3;  
   $responz=toggleVersionStatus($mypage,$listtable,$typetable); 
 }else if($job=="switchVersion" ){
   $tagon=3;
   $tagshow=3;  
   $responz=switchVersion($mypage, $listtable, $typetable);    
 }else if($job=="viewAudit" ){
   $tagon=5;
   $tagshow=5;  
 }else if($job=="viewProductAudit" ){
   $tagon=5;
   $tagshow=5; 
 }else{
   $tagon=1;
   $tagshow=1;	 
 }
     echo "<script nonce='" . $_SESSION['nonce'] . "'>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var url = this.getAttribute('data-url');
            var msg = this.getAttribute('data-confirm');
            if (confirm(msg)) {
                location.href = url;
            }
        });
    });
});
</script>";	
?>
<main id="main" role="main">
<section aria-label="我的發布資料 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="我的發布資料 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">我的發布資料</h1>
</div>
</section>
<section aria-label="我的發布資料" class="detail-page-hero detail-intro-after-query">
<div class="member-container">
    <?php include 'sidebar.php'; ?>
    <div class="content-area">
<div class='contentin'>

  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>
		<a href='my_products.html?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>發布資料列表</div>
        </a>
<?php 
if 	($tagon>1 ){
?>	
		<a href='my_products.html?tf=<?=$mypage?>&job=getdetaillist1&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'>發布資料主檔明細</div>
        </a>
<?php 
}
if 	($tagon>2 ) {
?>	
		<a href='my_products.html?tf=<?=$mypage?>&job=getContact2&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==3?"on":"")?> ' >
          <div class='tagin'>發布資料版本列表</div>
        </a>
<?php 
}
if 	($tagon>3 && $thisid >0) {
?>	
		<a href='my_products.html?tf=<?=$mypage?>&job=getdetaillist2&thisid=<?=$thisid?>&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==4?"on":"")?> ' >
          <div class='tagin'>版本明細</div>
        </a>	
<?php 
}
if 	($tagon==5){
?>	
		<span class='tagitem  <?=($tagon==5?"on":"")?> ' >
          <div class='tagin'>歷程紀錄</div>
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
			getContact1($mypage,$typetable,$isviewAudit);
		}else if($tagshow==2){
			getdetail1($mypage,$typetable);
		}elseif($tagshow==3){		  
			getContact2($mypage,$listtable,$typetable,$isviewAudit);
		}elseif($tagshow==4){
			getdetail2($mypage,$listtable,$typetable);		
		}elseif($tagshow==5){
			if($job=="viewAudit" ){					
					$responz=showOperationLogList('version', $thisid, 20);
			 }else if($job=="viewProductAudit" ){	
					$responz=showOperationLogList('product', $typeid, 20);
			 }
			echo 	$responz;
		}
?>		
        </div>
      </div>
   </div>
 <?php
function getContact1($mypage, $typetable, $isviewAudit) { 
    // 1. 取得使用者的組織ID    
    $myOrgId = checkUserOrganization();
    // 2. 組合查詢條件    
    $mycheck = " WHERE thisid >= 0 ";    
    // 搜尋：關鍵字（發布資料名稱）
    if (!empty($GLOBALS['keyword'])) {
        $mycheck .= " AND resource_name LIKE '%" . addslashes($GLOBALS['keyword']) . "%'";
    }
	// 搜尋：狀態
	if (isset($GLOBALS['status']) && $GLOBALS['status'] !== '') {
		$mycheck .= " AND isopen = '" . addslashes($GLOBALS['status']) . "'";
	}	
    // 只顯示自己的發布資料
    $mycheck .= " AND owner_id = '" . addslashes($myOrgId) . "'";
    // 3. 抓發布資料列表（不再需要 JOIN 分類表） 
    $productList = share_gettable( $typetable . $mycheck . " ORDER BY dateadd DESC");
    // 4. 顯示畫面
    echo "<span class='redtext'>提示：點選「編輯」可修改發布資料資料，點選「版本」可管理該發布資料的版本</span><br><br>";
    // 搜尋區塊    
    echo "<div class='search-area'>";
    echo "<div class='search-left'>";    
    // 關鍵字搜尋
    echo "<div class='search-item search-keyword'>";
    echo "<label>🔍 關鍵字</label>";
    echo "<input type='text' name='keyword' value='" . ($GLOBALS['keyword'] ?? '') . "' class='input-full' placeholder='搜尋發布資料名稱...' form='searchForm'>";
    echo "</div>";    
    // 狀態篩選
    echo "<div class='search-item'>";
    echo "<label>📊 狀態</label>";
    echo "<select name='status' form='searchForm'>";
    echo "<option value=''>全部</option>";
    echo "<option value='1' ".((($GLOBALS['status'] ?? '') === '1') ? 'selected' : '').">🟢 上架</option>";
    echo "<option value='0' ".((($GLOBALS['status'] ?? '') === '0') ? 'selected' : '').">🔴 下架</option>";
    echo "</select>";
    echo "</div>";    
    echo "</div>";    
    echo "<div class='search-right'>";
    echo "<form action='my_products.html?tf=".$mypage."&job=getContact1' method='post' id='searchForm'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 搜尋</button>";
    echo "</div>";
    echo "</form>";
    echo "<a href='my_products.html?tf=".$mypage."&job=getdetaillist1' class='green-btn'>＋ 新增發布資料</a>";
    echo "</div>";
    echo "</div>";  
    // 發布資料列表
    echo "<div class='table-wrapper'>";
    echo "<TABLE class='formtable'>";
    echo "<tr>";
    echo "<th width='25%'>發布資料名稱</th>";
    echo "<th width='12%'>當前版本</th>";
    echo "<th width='10%'>狀態</th>";
    echo "<th width='15%'>建立日期</th>";
    echo "<th width='38%'>操作</th>";
    echo "</tr>";
    
    if (!empty($productList)) {
        foreach ($productList as $row) {
            $statusText = ($row['isopen'] == 1) ? "🟢 上架" : "🔴 下架";
            $versionDisplay = !empty($row['current_version']) ? $row['current_version'] : '-';
            
            echo "<tr>";
            echo "<td class='tleft'>".$row['resource_name']."</td>";
            echo "<td>".$versionDisplay."</td>";
            echo "<td>".$statusText."</td>";
            echo "<td>".$row['dateadd']."</td>";
            echo "<td>";
            echo "<div class='btn-group'>";
            
            // 編輯（所有狀態都有） 
            echo "<a href='my_products.html?tf=".$mypage."&job=getdetaillist1&typeid=".$row['thisid']."' class='blue-btn btn-sm'>編輯</a> ";
            
            // 版本（所有狀態都有）
            echo "<a href='my_products.html?tf=".$mypage."&job=getContact2&typeid=".$row['thisid']."' class='green-btn btn-sm'>版本</a> ";
            
            // 上架（只有下架狀態才顯示）
            if ($row['isopen'] == 0) {
                echo "<a href='my_products.html?tf=".$mypage."&job=toggleProductStatus&typeid=".$row['thisid']."&status=1' class='green-btn btn-sm'>上架</a> ";
            }else{
                echo "<a href='my_products.html?tf=".$mypage."&job=toggleProductStatus&typeid=".$row['thisid']."&status=0' class='green-btn btn-sm'>下架</a> ";	 			
			}
            // 上架後無下架 → 不顯示下架按鈕
            
            // 刪除（只有沒有版本時才顯示）
            if ($row['current_version_id'] == 0) {
                echo "<a href='#' data-url='my_products.html?tf=".$mypage."&job=deleteProduct&typeid=".$row['thisid']."' data-confirm='確定要刪除此發布資料嗎？' class='red-btn'>刪除</a> ";
            }
            
            // 歷程記錄（所有狀態都有）
            echo "<a href='my_products.html?tf=".$mypage."&job=viewProductAudit&typeid=".$row['thisid']."' class='orange-btn'>歷程記錄</a>";
            
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='tcenter' >目前還沒有發布資料，點擊「＋ 新增發布資料」開始建立</td></tr>";
    }
    
    echo "</TABLE>";
    echo "</div>";
    echo "<div class='table-footer'>顯示 " . count($productList) . " 筆</div>";
}
function getdetail1($mypage, $typetable) {
    global $imgurl;
    $isEdit = ($GLOBALS['typeid'] ?? 0) > 0;
    $title = $isEdit ? "編輯資料發布資料" : "新增資料發布資料";
    $btnText = $isEdit ? "儲存修改" : "建立發布資料";
    $me = [];    
    if ($isEdit) {
        $me = checkDataExists($typetable, $GLOBALS['typeid']);
        $myOrgId = checkUserOrganization();
        if ($me['owner_id'] != $myOrgId) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
            exit;
        }
    }    
    $me['resource_name'] = checkval($GLOBALS['resource_name'], $me['resource_name'] ?? '');
  
    //  輸出 HTML（移除所有 inline style） 
    echo "<span class='fR'><span class='redtext'>＊</span>必填</span>";
    echo "<h2>" . $title . "</h2>";
    echo "<hr>";
    echo "<form action='my_products.html?tf=".$mypage."&job=uptypedetaillist1&typeid=".$GLOBALS['typeid']."' method='post' enctype='multipart/form-data' id='productForm'>";
    
    echo "<TABLE class='formtable' width='100%'>";
    
    //  編輯模式顯示的資訊 
    if ($isEdit) {
        $versionDisplay = !empty($row['current_version']) ? $row['current_version'] : '-';
        $statusText = ($me['isopen'] == 1) ? "🟢 上架" : "🔴 下架";
        $statusClass = ($me['isopen'] == 1) ? "online" : "offline";
        
        echo "<tr>";
        echo "  <th class='label-col'>發布資料 ID<br><span class='field-hint'>resource_id</span></th>";
        echo "  <td class='tleft'><span class='info-display'>" . $me['resource_id'] . "</span></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "  <th class='label-col'>當前版本<br><span class='field-hint'>current_version</span></th>";
        echo "  <td class='tleft'><span class='info-display'>" . $versionDisplay . "</span></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "  <th class='label-col'>狀態<br><span class='field-hint'>isopen</span></th>";
        echo "  <td class='tleft'><span class='status-badge " . $statusClass . "'>" . $statusText . "</span></td>";
        echo "</tr>";
    }
    
    //  發布資料名稱 
    echo "<tr>";
    echo "  <th class='label-col'><span class='redtext'>＊</span>發布資料名稱<br><span class='field-hint'>resource_name</span></th>";
    echo "  <td class='tleft'><input type='text' name='resource_name' value='" . htmlspecialchars($me['resource_name'], ENT_QUOTES) . "' form='productForm' required></td>";
    echo "</tr>";
    
 
    //  提示訊息 
    echo "<tr>";
    echo "  <td colspan='2' class='tleft tip-text'>";
    echo "    ⓘ 發布資料建立後狀態為「下架」，需建立版本並手動上架方可使用";
    echo "  </td>";
    echo "</tr>";
    
    //  按鈕 
    echo "<tr>";
    echo "  <td colspan='2' class='tcenter form-actions'>";
    echo "    <a href='my_products.html?tf=".$mypage."&job=getContact1' class='cancel-btn'>取消</a> ";
    echo "    <input type='submit' value='" . $btnText . "' class='green-btn' form='productForm'>";
    echo "  </td>";
    echo "</tr>";
    
    echo "</TABLE>";
    echo "</form>";
}
function uptypedetaillist1($mytable, $mypage) {
    $myerr = "";
    $userId = $_SESSION['companyid'] ?? 0;
    $thisid = $GLOBALS['typeid'] ?? 0;
    $userOrgId = checkUserOrganization();    
    //  驗證必填欄位（只剩 resource_name） 
    if (empty($GLOBALS["resource_name"])) {
        $myerr .= "<br>發布資料名稱為必填項目";
    }    
    if ($myerr) {
        $myerr = "請確認以下內容：" . $myerr;
        return $myerr;
    }    
    $temp = share_getinfo($mytable, "thisid", $thisid);
    
    if ($temp) {
        //  編輯模式 
        $temp = checkDataExists($mytable, $thisid);
        
        if ($temp['owner_id'] != $userOrgId) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限修改此發布資料'); history.back();</script>";
            exit;
        }        
        // 記錄修改前的資料
        $oldData = [
            'resource_name' => $temp['resource_name']
        ];
        
        //只更新 resource_name
        $result = share_update(
            $mytable,
            "resource_name = '" . addslashes($GLOBALS["resource_name"]) . "',
             lastadmin = '" . addslashes($userId) . "',
             dateupdate = NOW()",
            "thisid='" . $GLOBALS['typeid'] . "'"
        );
        
        if ($result) {
            // 取得更新後的完整資料
            $newRecord = share_getinfo($mytable, "thisid", $thisid);
            
            // 產生變更摘要
            $changeSummary = [];
            if ($oldData['resource_name'] != $GLOBALS["resource_name"]) {
                $changeSummary[] = "發布資料名稱：『" . $oldData['resource_name'] . "』→『" . $GLOBALS["resource_name"] . "』";
            }
            $changeSummaryText = !empty($changeSummary) ? implode('；', $changeSummary) : '無變更';
            
            // 寫入操作紀錄
            if (function_exists('writeGaiaOperationLog')) {
                writeGaiaOperationLog(
                    'product',
                    $thisid,
                    $userOrgId,
                    'UPDATE_PRODUCT',
                    '更新發布資料名稱',
                    $newRecord,
                    $changeSummaryText,
                    $userId
                );
            }
            
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 更新成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getdetaillist1&typeid=" . $GLOBALS['typeid'] . "';</script>";
            exit;
        } else {
            return "更新失敗，請重試";
        }
        
    } else {
        //  新增模式 
        $resource_id = generateUUID();        
        // 只新增 resource_name
		$result = share_insert($mytable, "resource_id, resource_name, owner_id, current_version_id, isopen, lastadmin, dateadd", "'" . addslashes($resource_id) . "', '" . addslashes($GLOBALS["resource_name"]) . "', '" . addslashes($userOrgId) . "', 0, 0, '" . addslashes($userId) . "', NOW()");
        if ($result) {
            $temp = share_getfree("SELECT LAST_INSERT_ID() as myid");
            $news_id = $temp[0]['myid'];            
            // 取得完整的新增資料
            $newRecord = share_getinfo($mytable, "thisid", $news_id);            
            // 寫入操作紀錄
            if (function_exists('writeGaiaOperationLog')) {
                writeGaiaOperationLog(
                    'product',
                    $news_id,
                    $userOrgId,
                    'CREATE_PRODUCT',
                    '新增發布資料：' . $GLOBALS["resource_name"],
                    $newRecord,
                    '新增發布資料：' . $GLOBALS["resource_name"],
                    $userId
                );
            }            
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 新增成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getdetaillist1&typeid=" . $news_id . "';</script>";
            exit;
        } else {
            return "新增失敗，請重試";
        }
    }
}
function getContact2($mypage, $listtable, $typetable, $isviewAudit) {
    $typeid = $GLOBALS['typeid'] ?? 0;    
    $product = checkDataExists($typetable, $typeid);   
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }  
    // 1. 取得分類名稱（從目前版本抓）    
    $industryName = '-';
    if ($product['current_version_id'] > 0) {
        $currentVersion = share_getinfo($listtable, "thisid", $product['current_version_id']);
        if ($currentVersion && !empty($currentVersion['industry_id'])) {
            $industry = share_getinfo("gaia_industry", "thisid", $currentVersion['industry_id']);
            $industryName = $industry['thisname'] ?? '-';
        }
    }    
    $statusText = ($product['isopen'] == 1) ? "🟢 上架" : "🔴 下架";
    $versionDisplay = !empty($product['current_version']) ? $product['current_version'] : '-';   
    // 2. 取得版本列表  
    $versionList = share_gettable($listtable . " WHERE product_id = '" . addslashes($typeid) . "' ORDER BY published_at DESC");   
    // 3. 顯示頁面
    echo "<h2>" . $product['resource_name'] . " - 版本管理</h2>";
    echo "<hr>";    
    // 發布資料資訊區塊
    echo "<div>";
    echo "<TABLE class='formtable'>";
    echo "<tr>";
    echo "<td width='15%'><strong>發布資料名稱</strong></td>";
    echo "<td width='35%'>" . $product['resource_name'] . "</td>";
    echo "<td width='15%'><strong>狀態</strong></td>";
    echo "<td width='35%'>" . $statusText . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><strong>目錄分類</strong></td>";
    echo "<td>" . $industryName . "</td>";
    echo "<td><strong>當前版本</strong></td>";
    echo "<td>" . $versionDisplay . "</td>";
    echo "</tr>";
    echo "</TABLE>";
    echo "</div>";    
    // 新增按鈕
    echo "<div >";
    echo "<a href='my_products.html?tf=".$mypage."&job=getdetaillist2&typeid=".$typeid."' class='green-btn'>＋ 新增版本</a>";
    echo "</div>";  
    // 版本列表    
    echo "<div class='table-wrapper'>";
    echo "<TABLE class='formtable'>";
    echo "<tr>";
    echo "<th width='25%'>版本號</th>";
    echo "<th width='20%'>發布日期</th>";
    echo "<th width='15%'>狀態</th>";
    echo "<th width='40%'>操作</th>";
    echo "</tr>";    
    if (!empty($versionList)) {
        foreach ($versionList as $row) {            
            // 判斷版本狀態            
            $isCurrent = ($row['thisid'] == $product['current_version_id']);            
            // 版本顯示名稱（目前版本加 ★）
            $versionDisplay = $isCurrent ? "★ " . $row['version'] : $row['version'];
            // 版本狀態標籤
            if ($row['isopen'] == 2){
                // 目前版本（isopen = 2）
                $statusLabel = '⭐ 目前版本';
                $statusClass = 'status-current';
            } elseif ($row['isopen'] == 1) {
                // 確認版本（isopen = 1）
                $statusLabel = '🟢 確認版本';
                $statusClass = 'status-approved';
            } else {
                // 草稿（isopen = 0）
                $statusLabel = '🔵 草稿';
                $statusClass = 'status-draft';
            } 
            // 操作按鈕（根據版本狀態）            
            $actions = '';    
// 編輯 / 檢視（草稿顯示「編輯」，已確認顯示「檢視」）
if ($row['isopen'] == 0) {
    $actions .= "<a href='my_products.html?tf=".$mypage."&job=getdetaillist2&typeid=".$typeid."&thisid=".$row['thisid']."' class='blue-btn btn-sm'>編輯</a> ";
} else {
    $actions .= "<a href='my_products.html?tf=".$mypage."&job=getdetaillist2&typeid=".$typeid."&thisid=".$row['thisid']."' class='gray-btn btn-sm'>檢視</a> ";
}            
			// 確認（只有草稿可確認）
			if ($row['isopen'] == 0) {
				$actions .= "<a href='#' data-url='my_products.html?tf=".$mypage."&job=toggleVersionStatus&typeid=".$typeid."&thisid=".$row['thisid']."&status=1' data-confirm='確定要確認此版本嗎？' class='green-btn btn-sm'>確認</a> ";
			}
			// 切換（確認版本且非目前版本可切換）
			if ($row['isopen'] == 1) {
				$actions .= "<a href='#' data-url='my_products.html?tf=".$mypage."&job=switchVersion&typeid=".$typeid."&thisid=".$row['thisid']."' data-confirm='確定要切換到 " . $row['version'] . " 嗎？' class='orange-btn '>切換</a> ";
			}
			// 刪除（只有草稿可刪除）
			if ($row['isopen'] == 0) {
				$actions .= "<a href='#' data-url='my_products.html?tf=".$mypage."&job=deleteVersion&typeid=".$typeid."&thisid=".$row['thisid']."' data-confirm='確定要刪除此版本嗎？' class='red-btn '>刪除</a> ";
			}
            // 歷程記錄（所有狀態都有）
            if ($isviewAudit == 1) {
                $actions .= "<a href='my_products.html?tf=".$mypage."&job=viewAudit&typeid=".$typeid."&thisid=".$row['thisid']."' class='orange-btn '>歷程</a> ";
            }            
            // 如果沒有任何操作按鈕，顯示「-」
            if (empty($actions)) {
                $actions = '-';
            }            
            echo "<tr>";
            echo "<td>" . $versionDisplay . "</td>";
            echo "<td>" . $row['published_at'] . "</td>";
            echo "<td><span class='status-badge " . $statusClass . "'>" . $statusLabel . "</span></td>";
            echo "<td><div class='btn-group'>" . $actions . "</div></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4' class='tcenter' >目前還沒有版本，點擊「＋ 新增版本」開始建立</td></tr>";
    }    
    echo "</TABLE>";
    echo "</div>";    
    // 筆數
    echo "<div >顯示 " . count($versionList) . " 筆</div>";
}
function getdetail2($mypage, $listtable, $typetable) {
    global $imgurl;    
    $typeid = $GLOBALS['typeid'] ?? 0;
    $thisid = $GLOBALS['thisid'] ?? 0;
    $isEdit = ($thisid > 0);  
    // 編輯模式：載入資料 + 檢查權限
    $me = [];
    if ($isEdit) {
        $me = checkDataExists($listtable, $thisid);
    }
    // 判斷是否為唯讀模式（已上架的版本只能看不能改）    
    $isViewOnly = false;
	if ($isEdit && isset($me['isopen']) && $me['isopen'] != 0) {
		$isViewOnly = true;
	}    
    // 標題和按鈕文字
    if ($isViewOnly) {
        $title = "檢視版本";
        $btnText = "返回";
    } elseif ($isEdit) {
        $title = "編輯版本";
        $btnText = "儲存修改";
    } else {
        $title = "新增版本";
        $btnText = "建立版本";
    }   
    // 權限檢查：只能操作自己公司的發布資料    
    $myOrgId = checkUserOrganization();
    $product = checkDataExists($typetable, $typeid);
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    // 處理 POST 回填（新版欄位）    
    $me['display_name'] = checkval($GLOBALS['display_name'], $me['display_name'] ?? '');
    $me['industry_id'] = checkval($GLOBALS['industry_id'], $me['industry_id'] ?? '');
    $me['description'] = checkval($GLOBALS['description'], $me['description'] ?? '');
    $me['version'] = checkval($GLOBALS['version'], $me['version'] ?? '');
    $me['keywords'] = checkval($GLOBALS['keywords'], $me['keywords'] ?? '');
    $me['data_format'] = checkval($GLOBALS['data_format'], $me['data_format'] ?? '');
    $me['contract_duration_days'] = checkval($GLOBALS['contract_duration_days'], $me['contract_duration_days'] ?? 30);
    $me['connector_id'] = checkval($GLOBALS['connector_id'], $me['connector_id'] ?? '');
    $me['allowed_actions'] = checkval($GLOBALS['allowed_actions'], $me['allowed_actions'] ?? '');
    $me['prohibited_actions'] = checkval($GLOBALS['prohibited_actions'], $me['prohibited_actions'] ?? '');
    $me['constraints'] = checkval($GLOBALS['constraints'], $me['constraints'] ?? '');
    $me['data_sensitivity_level'] = checkval($GLOBALS['data_sensitivity_level'], $me['data_sensitivity_level'] ?? 0);
    $me['update_frequency'] = checkval($GLOBALS['update_frequency'], $me['update_frequency'] ?? '');
    $me['availability'] = checkval($GLOBALS['availability'], $me['availability'] ?? '');
    // 解析 JSON（用於顯示已選項目）
    $allowedActions = json_decode($me['allowed_actions'] ?? '', true) ?: [];
    $prohibitedActions = json_decode($me['prohibited_actions'] ?? '', true) ?: [];
    $constraintsData = json_decode($me['constraints'] ?? '', true) ?: [];
    // 取得字典表資料    
    $allowedList = share_gettable("gaia_allowed_actions WHERE isopen = 1 ORDER BY sorting");
    $prohibitedList = share_gettable("gaia_prohibited_actions WHERE isopen = 1 ORDER BY sorting");
    $constraintData = share_gettable("gaia_constraints_type WHERE isopen = 1 ORDER BY sorting");
    $constraintOptions = [];
    if ($constraintData) {
        foreach ($constraintData as $row) {
            $constraintOptions[] = [
                'key' => $row['thiscode'],
                'label' => $row['thisname'],
                'type' => $row['valtype'],
                'placeholder' => $row['placeholder'] ?? ''
            ];
        }
    }
    $labelMapJson = [];
    foreach ($constraintOptions as $opt) {
        $labelMapJson[$opt['key']] = $opt['label'];
    }
    $labelMapJson = json_encode($labelMapJson);
    $currentConstraints = $me['constraints'] ?? '';
    // 取得該公司的連接器（用於 connector_id 下拉選單）    
    $machineList = share_gettable("machine_accounts WHERE owner_id = '" . addslashes($myOrgId) . "' AND  LENGTH(machine_url) > 0 AND isopen = 1 ORDER BY thisname");    
    // 敏感等級選項    
  /*  $sensitivityOptions = [
        0 => '0 - 公開 (Public)',
        1 => '1 - 內部 (Internal)',
        2 => '2 - 機密 (Confidential)',
        3 => '3 - 高度機密 (Highly Confidential)',
        4 => '4 - 需人工審核 (Restricted)'
    ];*/
    $sensitivityOptions = [
        0 => '公開 (Public)',
        4 => '需人工審核 (Restricted)'
    ];	
	
    // 顯示頁面    
    if ($isViewOnly) {
        echo "<span class='fR'><span class='redtext'>🔒</span> 唯讀模式 (唯讀)</span>";
    } else {
        echo "<span class='fR'><span class='redtext'>＊</span>必填欄位 (Required Field)</span>";
    }
    echo "<h2 class='form-title'>" . $title . "</h2>";
    echo "<hr class='form-hr'>";
    
    if ($isViewOnly) {
        echo "<div class='view-only-container'>";
    } else {
        echo "<form action='my_products.html?tf=".$mypage."&job=updadetaillist2' method='post' enctype='multipart/form-data' id='versionForm'>";
        echo "<input type='hidden' name='typeid' value='" . $typeid . "'>";
        if ($isEdit) {
            echo "<input type='hidden' name='thisid' value='" . $thisid . "'>";
        }
    }    
    echo "<TABLE class='formtable'>";   
    // 版本資訊 (Version Information)    
    echo "<tr><th colspan='2' class='section-title'>版本資訊 (Version Information)</th></tr>";
    $disabled = $isViewOnly ? 'disabled' : '';
    // 對外顯示名稱 - display_name（必填）
    echo "<tr>";
    echo "<th class='label-col'><span class='redtext'>＊</span>對外顯示名稱<br><span class='field-en'>Display Name</span></th>";
    echo "<td class='input-col'><input type='text' name='display_name' value='" . htmlspecialchars($me['display_name'] ?? '', ENT_QUOTES) . "' class='input-full' form='versionForm' placeholder='請輸入對外顯示名稱' $disabled required></td>";
    echo "</tr>";

// 1. 若為修改模式，拿原本 $me['industry_id'] 裡面存的版本 ID 反推 schema_id

$saved_version_id = $me['industry_id'] ?? 0; // 欄位沿用原名稱，但值是 gaia_schema_version.thisid
$current_schema_id = 0;

if ($saved_version_id > 0) {
    // 透過版本 ID 查出對應的 schema_id
    $verRow = share_gettable(" gaia_schema_version WHERE thisid = " . intval($saved_version_id));
    if (!empty($verRow)) {
        $current_schema_id = $verRow[0]['schema_id'];
    }
}

// 目錄分類 / 產業標準範本 (Schema & Version)
echo "<tr>";
echo "<th class='label-col'><span class='redtext'>＊</span>目錄分類 / 產業標準範本<br><span class='field-en'>Industry Schema & Version</span></th>";
echo "<td class='input-col'>";


// 2. 取得所有上架中的產業與其屬下的 Schema

$sql = "SELECT s.thisid AS schema_id, s.schemaname, s.schemacode, 
               i.thisid AS industry_id, i.thisname AS ind_name, i.thiscode AS ind_code
        FROM gaia_schema s
        INNER JOIN gaia_industry i ON s.industry_id = i.thisid
        WHERE s.isopen = 1 AND i.isopen = 1
        ORDER BY i.thisid ASC, s.thisid ASC";

$schemaList = share_getfree($sql);

$groupedSchema = [];
foreach ($schemaList as $item) {
    $indKey = $item['ind_name'] . " (" . $item['ind_code'] . ")";
    $groupedSchema[$indKey][] = $item;
}


// 3. 第一階：選擇 Schema 選單（輔助選單，不需存檔）

echo "<div style='margin-bottom: 8px;'>";
echo "<select id='schema_id_select' class='select-full' $disabled required>";
echo "<option value=''>請選擇目錄分類 / Schema 範本</option>";

foreach ($groupedSchema as $groupLabel => $schemas) {
    echo "<optgroup label='" . htmlspecialchars($groupLabel) . "'>";
    foreach ($schemas as $s) {
        $selected = ($s['schema_id'] == $current_schema_id) ? "selected" : "";
        echo "<option value='" . $s['schema_id'] . "' $selected>" 
             . htmlspecialchars($s['schemaname']) . " (" . htmlspecialchars($s['schemacode']) . ")"
             . "</option>";
    }
    echo "</optgroup>";
}
echo "</select>";
echo "</div>";


// 4. 第二階：選擇 Schema 版本選單（名為 industry_id，直接送出版本ID）

$verDisabled = ($current_schema_id > 0 && empty($disabled)) ? "" : "disabled";

echo "<div>";
// 這裡的 name='industry_id' 保持與原來一樣，表單 POST 送出時就能直接存回舊欄位
echo "<select id='schema_version_id_select' name='industry_id' class='select-full' form='versionForm' $disabled $verDisabled required>";

if ($current_schema_id > 0) {
    echo "<option value=''>請選擇 Schema 版本</option>";
    $vSql = "SELECT thisid, version FROM gaia_schema_version WHERE schema_id = " . intval($current_schema_id) . " AND isopen = 1 ORDER BY thisid DESC";
    $versionList = share_getfree($vSql);

    foreach ($versionList as $v) {
        $vSelected = ($v['thisid'] == $saved_version_id) ? "selected" : "";
        echo "<option value='" . $v['thisid'] . "' $vSelected>" . htmlspecialchars($v['version']) . "</option>";
    }
} else {
    echo "<option value=''>請先選擇 Schema 範本</option>";
}

echo "</select>";
echo "</div>";

echo "<span class='hint-text'>💡 先選擇產業 Schema 範本後，再選擇對應生效的版本號</span>";
echo "</td>";
echo "</tr>";
    
    // 發布資料描述 - description（必填）
    echo "<tr>";
    echo "<th class='label-col'><span class='redtext'>＊</span>發布資料描述<br><span class='field-en'>Description</span></th>";
    echo "<td class='input-col'><textarea name='description' rows='5' class='textarea-full' form='versionForm' placeholder='請輸入發布資料描述' $disabled required>" . htmlspecialchars($me['description'] ?? '', ENT_QUOTES) . "</textarea></td>";
    echo "</tr>";
    
    // 版本號 - version（必填）
    echo "<tr>";
    echo "<th class='label-col'><span class='redtext'>＊</span>版本號<br><span class='field-en'>version</span></th>";
    echo "<td class='input-col'><input type='text' name='version' value='" . htmlspecialchars($me['version'] ?? '', ENT_QUOTES) . "' class='input-full' form='versionForm' placeholder='例如：v1.0.0' $disabled required>";
	
	echo "<span class='hint-text'>命名無規範, 建議以版本發布順序命名, 例: v.1.0; v.1.1</span>";
	echo "</td>";

    echo "</tr>";
    
    // 關鍵字/標籤 - keywords (Tag Input)
    echo "<tr>";
    echo "<th class='label-col'>關鍵字/標籤<br><span class='field-en'>keywords</span></th>";
    echo "<td class='input-col'>";
    echo "<div class='tag-input-wrapper'>";
    echo "<div class='tag-input-row'>";
    echo "<input type='text' id='keywordInput' class='input-full tag-input-field' placeholder='輸入關鍵字後按 Enter 新增' $disabled>";
    if (!$isViewOnly) {
        echo "<button type='button' id='addKeywordBtn' class='btn-add-tag'>➕ 新增</button>";
    }
    echo "</div>";
    echo "<div id='keywordTags' class='tag-container'>";
    echo "</div>";
    echo "<input type='hidden' name='keywords' id='keywordsHidden' value='" . htmlspecialchars($me['keywords'] ?? '', ENT_QUOTES) . "'>";
    echo "<span class='hint-text'>💡 建議 3~5 個關鍵字，用於搜尋與分類 <span id='keywordCount' class='keyword-count'></span></span>";
    echo "</div>";
    echo "</td>";
    echo "</tr>";
    
    // 資料格式 - data_format（下拉）
    echo "<tr>";
    echo "<th class='label-col'>資料格式<br><span class='field-en'>Data Format</span></th>";
    echo "<td class='input-col'>";
    echo "<select name='data_format' class='select-full' form='versionForm' $disabled>";
    echo "<option value=''>請選擇資料格式</option>";
    $formatOptions = ['CSV', 'JSON'];
    foreach ($formatOptions as $fmt) {
        $sel = ($fmt == ($me['data_format'] ?? '')) ? "selected" : "";
        echo "<option value='" . $fmt . "' $sel>" . $fmt . "</option>";
    }
    echo "</select>";
    echo "</td>";
    echo "</tr>";
    
    
    // 契約 (Contract)
    
    echo "<tr><th colspan='2' class='section-title'>契約 (Contract)</th></tr>";
    
    // 契約有效天數 - contract_duration_days（必填）
    echo "<tr>";
    echo "<th class='label-col'><span class='redtext'>＊</span>契約有效天數<br><span class='field-en'>Contract Duration</span></th>";
    echo "<td class='input-col'>";
    echo "<input type='number' name='contract_duration_days' value='" . ($me['contract_duration_days'] ?? 30) . "' class='input-small' form='versionForm' min='1' max='365' $disabled required>";
    echo "<span class='unit-text'>天</span>";
    echo "<span class='hint-text'>📌 預設 30 天，範圍 1~365 天</span>";
    echo "</td>";
    echo "</tr>";
    
    
    // 連接器 
    
    echo "<tr><th colspan='2' class='section-title'><span class='redtext'>＊</span>連接器 </th></tr>";
    
    // 連接器 - connector_id
    echo "<tr>";
    echo "<th class='label-col'>連接器<br><span class='field-en'>Connector</span></th>";
    echo "<td class='input-col'>";
    echo "<select name='connector_id' class='select-full' form='versionForm' $disabled>";
    echo "<option value=''>請選擇連接器</option>";
    if (!empty($machineList)) {
        foreach ($machineList as $mac) {
            $sel = ($mac['thisid'] == ($me['connector_id'] ?? '')) ? "selected" : "";
            $display = htmlspecialchars($mac['thisname'] . ' (' . $mac['machine_url'] . ')', ENT_QUOTES);
            echo "<option value='" . $mac['thisid'] . "' $sel>" . $display . "</option>";
        }
    } else {
        echo "<option value=''>尚無可用連接器，請先建立連接器</option>";
    }
    echo "</select>";
    echo "<span class='hint-text'>💡 選擇此版本要使用的連接器</span>";
    echo "</td>";
    echo "</tr>";
    
    
    // 使用政策 (Usage Policy)
    
    echo "<tr><th colspan='2' class='section-title'>使用政策 (Usage Policy)</th></tr>";
    
    if (!$isViewOnly) {
        echo "<tr>";
        echo "<th class='label-col'>政策範本<br><span class='field-en'>Policy Template</span></th>";
        echo "<td class='input-col'>";
        echo "<select name='policy_template_id' id='policyTemplateSelect' class='select-full' form='versionForm'>";
        echo "<option value=''>請選擇政策範本（選填）</option>";
        $policyList = share_gettable("gaia_policy_template WHERE isopen = 1 ORDER BY thisid");
        foreach ($policyList as $pl) {
            echo "<option value='" . $pl['thisid'] . "' data-content='" . htmlspecialchars($pl['policy_json'], ENT_QUOTES) . "'>" . htmlspecialchars($pl['thisname']) . "</option>";
        }
        echo "</select>";
        echo "<span class='hint-text'>💡 選取後會自動帶入「限制」、「可做什麼」、「不可做什麼」、「敏感等級」欄位，可手動修改</span>";
        echo "</td>";
        echo "</tr>";
    }
    
    // 可做什麼 - allowed_actions (Checkbox)
    echo "<tr>";
    echo "<th class='label-col'>可做什麼<br><span class='field-en'>Allowed Actions</span></th>";
    echo "<td class='input-col'>";
    echo "<div class='checkbox-group'>";
    if (!empty($allowedList)) {
        foreach ($allowedList as $row) {
            $checked = in_array($row['thiscode'], $allowedActions) ? "checked" : "";
            echo "<label class='checkbox-label'>";
            echo "<input type='checkbox' name='allowed_actions[]' value='" . $row['thiscode'] . "' $checked form='versionForm' $disabled> ";
            echo $row['thisname'] . " (" . $row['thiscode'] . ")";
            echo "</label>";
        }
    } else {
        echo "<span>暫無選項</span>";
    }
    echo "</div>";
    echo "<span class='hint-text'>📌 對應 Gaia-X 標準：可複選，定義請查詢<a href='https://www.w3.org/ns/odrl/2' target='_blank'>w3.org</a></span>";
    echo "</td>";
    echo "</tr>";
    
    // 不可做什麼 - prohibited_actions (Checkbox)
    echo "<tr>";
    echo "<th class='label-col'>不可做什麼<br><span class='field-en'>Prohibited Actions</span></th>";
    echo "<td class='input-col'>";
    echo "<div class='checkbox-group'>";
    if (!empty($prohibitedList)) {
        foreach ($prohibitedList as $row) {
            $checked = in_array($row['thiscode'], $prohibitedActions) ? "checked" : "";
            echo "<label class='checkbox-label'>";
            echo "<input type='checkbox' name='prohibited_actions[]' value='" . $row['thiscode'] . "' $checked form='versionForm' $disabled> ";
            echo $row['thisname'] . " (" . $row['thiscode'] . ")";
            echo "</label>";
        }
    } else {
        echo "<span>暫無選項</span>";
    }
    echo "</div>";
    echo "<span class='hint-text'>📌 對應 Gaia-X 標準：可複選，定義請查詢<a href='https://www.w3.org/ns/odrl/2' target='_blank'>w3.org</a></span>";
    echo "</td>";
    echo "</tr>";
    
    
    // 限制 - constraints
    
    ?>
    <tr>
        <th class='label-col'>
            限制<br>
            <span class='field-en'>Constraints</span>
        </th>
        <td class='input-col'>
            <input type="hidden" name="constraints" id="constraintsInput" 
                   value='<?php echo htmlspecialchars($currentConstraints, ENT_QUOTES); ?>'>
            <div class="constraints-selector">
                <select id="constraintSelect" class="select-constraint" <?php echo $disabled; ?>>
                    <option value="">-- 請選擇限制條件 --</option>
                    <?php foreach ($constraintOptions as $option): ?>
                    <option value="<?php echo htmlspecialchars($option['key']); ?>" 
                            data-type="<?php echo htmlspecialchars($option['type']); ?>"
                            data-placeholder="<?php echo htmlspecialchars($option['placeholder']); ?>">
                        <?php echo htmlspecialchars($option['label']); ?> 
                        <span class="type-tag"><?php echo htmlspecialchars($option['type']); ?></span>
                    </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" id="constraintValue" class="input-value" placeholder="請輸入數值..." <?php echo $disabled; ?>>
                <?php if (!$isViewOnly): ?>
                <button type="button" class="btn-add" id="btnAdd">➕ 加入</button>
                <?php endif; ?>
            </div>
            <div id="constraintTags" class="constraint-tags">
                <?php if (!empty($currentConstraints)): 
                    $currentArray = json_decode($currentConstraints, true);
                    if ($currentArray && is_array($currentArray)):
                        foreach ($currentArray as $key => $value):
                            $label = $key;
                            foreach ($constraintOptions as $opt) {
                                if ($opt['key'] == $key) {
                                    $label = $opt['label'];
                                    break;
                                }
                            }
                            $displayValue = is_array($value) ? implode(', ', $value) : 
                                           (is_bool($value) ? ($value ? '✅ 啟用' : '❌ 停用') : $value);
                ?>
                    <span class="tag" data-key="<?php echo htmlspecialchars($key); ?>">
                        <span class="tag-label"><?php echo htmlspecialchars($label); ?></span>
                        <span class="tag-value"><?php echo htmlspecialchars($displayValue); ?></span>
                        <?php if (!$isViewOnly): ?>
                        <button type="button" class="tag-remove" data-key="<?php echo htmlspecialchars($key); ?>">✕</button>
                        <?php endif; ?>
                    </span>
                <?php 
                        endforeach;
                    endif;
                else: ?>
                    <span class="empty-hint">📭 尚未設定任何限制條件</span>
                <?php endif; ?>
            </div>
            <div id="constraintStatus" class="constraint-status"></div>
            <span class="hint-text">
                💡 選擇限制條件 → 輸入數值 → 點擊「加入」或按 Enter 
                <span class="key-hint">已存在時會自動變成「更新」</span>
            </span>
            <br>
            <span class="hint-text" id="jsonPreview">
                📋 目前設定：<span id="jsonDisplay"><?php echo !empty($currentConstraints) ? htmlspecialchars($currentConstraints) : '(空)'; ?></span>
            </span>
        </td>
    </tr>
    
    <?php
    // 敏感等級 - data_sensitivity_level（必填）
    echo "<tr>";
    echo "<th class='label-col'><span class='redtext'>＊</span>敏感等級<br><span class='field-en'>Sensitivity Level</span></th>";
    echo "<td class='input-col'>";
    echo "<select name='data_sensitivity_level' class='select-full' form='versionForm' $disabled required>";
    foreach ($sensitivityOptions as $key => $label) {
        $sel = ($key == ($me['data_sensitivity_level'] ?? 0)) ? "selected" : "";
        echo "<option value='" . $key . "' $sel>" . $label . "</option>";
    }
    echo "</select>";
    echo "<span class='hint-text'>📌 對應 Gaia-X 標準：公開、需人工審核</span>";
    echo "</td>";
    echo "</tr>";
    
    
    // 技術 Metadata (Technical Metadata)
    
    echo "<tr><th colspan='2' class='section-title'>技術 Metadata (Technical Metadata)</th></tr>";
    
    // 更新頻率 - update_frequency（必填）
    echo "<tr>";
    echo "<th class='label-col'><span class='redtext'>＊</span>更新頻率<br><span class='field-en'>Update Frequency</span></th>";
    echo "<td class='input-col'>";
echo "<select name='update_frequency' class='select-full' form='versionForm' required $disabled>";
echo "<option value=''>請選擇更新頻率</option>";

$frequencyOptions = [
    'PT1S' => '即時',
    'P1D' => '每日',
    'P1W' => '每週',
    'P1M' => '每月',
    'P3M' => '每季',
    'P1Y' => '每年'
];

foreach ($frequencyOptions as $value => $label) {
    $sel = ($value == ($me['update_frequency'] ?? '')) ? "selected" : "";
    echo "<option value='$value' $sel>$label</option>";
}
echo "</select>";
    echo "</td>";
    echo "</tr>";
    
    // 可用性 - availability
    echo "<tr>";
    echo "<th class='label-col'>可用性<br><span class='field-en'>Availability</span></th>";
    echo "<td class='input-col'>";
    echo "<input type='text' name='availability' value='" . htmlspecialchars($me['availability'] ?? '', ENT_QUOTES) . "' class='input-small' form='versionForm' placeholder='例如：99.9%' $disabled>";
    echo "<span class='unit-text'>%</span>";
    echo "<span class='hint-text'>📌選填；請依資料預估連線可用率與穩定度填入；範例：99.9%、90%。</span>";
    echo "</td>";
    echo "</tr>";
    
    
    // 按鈕
    
    echo "<tr>";
    echo "<td colspan='2' class='form-actions'>";
    if ($isViewOnly) {
        echo "<a href='my_products.html?tf=".$mypage."&job=getContact2&typeid=".$typeid."' class='cancel-btn'>返回</a>";
    } else {
        echo "<a href='my_products.html?tf=".$mypage."&job=getContact2&typeid=".$typeid."' class='cancel-btn'>取消 (Cancel)</a> ";
        echo "<input type='submit' value='" . $btnText . "' class='green-btn' form='versionForm'>";
    }
    echo "</td>";
    echo "</tr>";
    
    echo "</TABLE>";
    
    if ($isViewOnly) {
        echo "</div>";
    } else {
        echo "</form>";
    }
    
    
    // JavaScript 區塊
    
    ?>
    <script nonce='<?php echo $_SESSION['nonce'];?>'>
 $(document).ready(function() {
    $('#schema_id_select').on('change', function() {
        var schema_id = $(this).val();
        var $verSelect = $('#schema_version_id_select');

        // 重置版本選單
        $verSelect.empty();

        if (!schema_id) {
            $verSelect.append("<option value=''>請先選擇 Schema 範本</option>");
            $verSelect.prop('disabled', true);
            return;
        }

        $verSelect.append("<option value=''>載入中...</option>");
        $verSelect.prop('disabled', true);
        var tempvals = Array(schema_id);
        var tempitem = ajaxarr("aj_get_schema_versions", tempvals, "ajax.php");

        tempitem.done(function(data) {
            $verSelect.empty();

            if (data[0] == "OK") {
                $verSelect.append("<option value=''>請選擇 Schema 版本</option>");
                
                // data[1] 為版本列表陣列: [{thisid: 10001, version: '1.0.0'}, ...]
                var versionList = data[1];
                if (versionList && versionList.length > 0) {
                    $.each(versionList, function(i, v) {
                        $verSelect.append("<option value='" + v.thisid + "'>v" + v.version + "</option>");
                    });
                    $verSelect.prop('disabled', false);
                } else {
                    $verSelect.append("<option value=''>此 Schema 尚無可用版本</option>");
                }
            } else {
                $verSelect.append("<option value=''>載入版本失敗，請稍後再試</option>");
                alert(data[1] || "無法取得版本資訊，請稍後再試");
            }
        });
    });
});   
    // 1. 關鍵字/標籤 Tag Input
    
    (function() {
        const input = document.getElementById('keywordInput');
        const container = document.getElementById('keywordTags');
        const hidden = document.getElementById('keywordsHidden');
        const addBtn = document.getElementById('addKeywordBtn');
        const countDisplay = document.getElementById('keywordCount');
        
        <?php if (!$isViewOnly): ?>
        function getKeywords() {
            const tags = container.querySelectorAll('.tag-chip');
            return Array.from(tags).map(tag => tag.dataset.keyword);
        }

        function updateHidden() {
            const keywords = getKeywords();
            hidden.value = JSON.stringify(keywords);
            if (countDisplay) {
                countDisplay.textContent = '已新增 ' + keywords.length + ' 個關鍵字';
            }
        }

        function addKeyword(keyword) {
            keyword = keyword.trim();
            if (!keyword) return false;
            const existing = getKeywords();
            if (existing.includes(keyword)) {
                showStatus('info', '⚠️ 關鍵字「' + keyword + '」已存在');
                setTimeout(hideStatus, 2000);
                return false;
            }
            const tag = document.createElement('span');
            tag.className = 'tag-chip';
            tag.dataset.keyword = keyword;
            tag.innerHTML = keyword + ' <span class="tag-remove">&times;</span>';
            tag.querySelector('.tag-remove').addEventListener('click', function(e) {
                e.stopPropagation();
                tag.remove();
                updateHidden();
            });
            container.appendChild(tag);
            updateHidden();
            return true;
        }

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const val = this.value;
                addKeyword(val);
                this.value = '';
            }
        });

        if (addBtn) {
            addBtn.addEventListener('click', function() {
                const val = input.value;
                addKeyword(val);
                input.value = '';
                input.focus();
            });
        }

        input.addEventListener('blur', function() {
            const val = this.value.trim();
            if (val) {
                addKeyword(val);
                this.value = '';
            }
        });
        <?php endif; ?>

        // 初始化載入既有關鍵字
        try {
            const initialKeywords = JSON.parse(hidden.value || '[]');
            if (Array.isArray(initialKeywords) && initialKeywords.length > 0) {
                <?php if (!$isViewOnly): ?>
                initialKeywords.forEach(function(kw) {
                    if (!kw) return;
                    addKeyword(kw);
                });
                updateHidden();
                <?php else: ?>
                initialKeywords.forEach(function(kw) {
                    if (!kw) return;
                    const tag = document.createElement('span');
                    tag.className = 'tag-chip';
                    tag.dataset.keyword = kw;
                    tag.textContent = kw;
                    container.appendChild(tag);
                });
                if (countDisplay) {
                    const tags = container.querySelectorAll('.tag-chip');
                    countDisplay.textContent = '共 ' + tags.length + ' 個關鍵字';
                }
                <?php endif; ?>
            }
        } catch (e) {}
        
        <?php if (!$isViewOnly): ?>
        updateHidden();
        <?php else: ?>
        if (countDisplay) {
            const tags = container.querySelectorAll('.tag-chip');
            countDisplay.textContent = '共 ' + tags.length + ' 個關鍵字';
        }
        <?php endif; ?>
    })();

    <?php if (!$isViewOnly): ?>
    
    // 2. 政策範本 → 自動帶入
    
    document.getElementById('policyTemplateSelect')?.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const content = selected.dataset.content;
        
        if (!content) return;
        
        try {
            const policy = JSON.parse(content);
            
            if (policy.constraints) {
                const hiddenInput = document.getElementById('constraintsInput');
                hiddenInput.value = JSON.stringify(policy.constraints);
                if (typeof setConstraints === 'function') {
                    setConstraints(policy.constraints);
                }
                if (typeof updateJsonPreview === 'function') {
                    updateJsonPreview();
                }
            }
            
            if (policy.allowed_actions && Array.isArray(policy.allowed_actions)) {
                document.querySelectorAll('input[name="allowed_actions[]"]').forEach(function(cb) {
                    cb.checked = policy.allowed_actions.includes(cb.value);
                });
            }
            
            if (policy.prohibited_actions && Array.isArray(policy.prohibited_actions)) {
                document.querySelectorAll('input[name="prohibited_actions[]"]').forEach(function(cb) {
                    cb.checked = policy.prohibited_actions.includes(cb.value);
                });
            }
            
            if (policy.data_sensitivity_level !== undefined && policy.data_sensitivity_level !== null) {
                const sensitivitySelect = document.querySelector('select[name="data_sensitivity_level"]');
                if (sensitivitySelect) {
                    sensitivitySelect.value = policy.data_sensitivity_level;
                }
            }
            
            showStatus('success', '✅ 已套用政策範本：' + selected.text);
            setTimeout(hideStatus, 3000);
            
        } catch (e) {
            console.error('解析政策範本失敗:', e);
            showStatus('error', '❌ 政策範本格式錯誤');
            setTimeout(hideStatus, 3000);
        }
    });
    <?php endif; ?>

    
    // 3. Constraints
    
    const LABEL_MAP = <?php echo $labelMapJson; ?>;
    const CURRENT_CONSTRAINTS = <?php echo !empty($currentConstraints) ? json_encode($currentConstraints) : 'null'; ?>;

    const select = document.getElementById('constraintSelect');
    const valueInput = document.getElementById('constraintValue');
    const hiddenInput = document.getElementById('constraintsInput');
    const tagsContainer = document.getElementById('constraintTags');
    const jsonDisplay = document.getElementById('jsonDisplay');
    const btnAdd = document.getElementById('btnAdd');
    const statusBar = document.getElementById('constraintStatus');
    
    <?php if (!$isViewOnly): ?>
    function getConstraints() {
        if (!hiddenInput.value) return {};
        try {
            const obj = JSON.parse(hiddenInput.value);
            return typeof obj === 'object' && obj !== null ? obj : {};
        } catch (e) {
            return {};
        }
    }

    function setConstraints(constraints) {
        hiddenInput.value = JSON.stringify(constraints);
        updateTags(constraints);
        updateJsonPreview();
    }

    function formatDisplayValue(value) {
        if (value === null || value === undefined) return '';
        if (typeof value === 'boolean') {
            return value ? '✅ 啟用' : '❌ 停用';
        }
        if (Array.isArray(value)) {
            return value.join(', ');
        }
        return String(value);
    }

    function formatInputValue(value) {
        if (value === null || value === undefined) return '';
        if (typeof value === 'boolean') {
            return value ? 'true' : 'false';
        }
        if (Array.isArray(value)) {
            return value.join(', ');
        }
        return String(value);
    }

    function parseValue(value, type) {
        if (type === 'boolean') {
            const lower = value.toLowerCase().trim();
            if (lower === 'true' || lower === '1' || lower === '是' || lower === 'yes') return true;
            if (lower === 'false' || lower === '0' || lower === '否' || lower === 'no') return false;
            throw new Error('請輸入 true 或 false');
        }
        if (type === 'integer') {
            const num = parseInt(value);
            if (isNaN(num)) throw new Error('請輸入有效的數字');
            return num;
        }
        if (type === 'array') {
            const arr = value.split(',').map(s => s.trim()).filter(s => s);
            if (arr.length === 0) throw new Error('請輸入至少一個值，用逗號分隔');
            return arr;
        }
        return value;
    }

    function updateTags(constraints) {
        const keys = Object.keys(constraints);
        if (keys.length === 0) {
            tagsContainer.innerHTML = '<span class="empty-hint">📭 尚未設定任何限制條件</span>';
            return;
        }
        let html = '';
        keys.forEach(key => {
            const label = LABEL_MAP[key] || key;
            const value = constraints[key];
            const displayValue = formatDisplayValue(value);
            html += `<span class="tag" data-key="${key}">
                <span class="tag-label">${label}</span>
                <span class="tag-value">${displayValue}</span>
                <button type="button" class="tag-remove" data-key="${key}">✕</button>
            </span>`;
        });
        tagsContainer.innerHTML = html;
        bindTagEvents();
    }

    function updateJsonPreview() {
        const value = hiddenInput.value;
        if (value && value !== '{}') {
            try {
                const obj = JSON.parse(value);
                jsonDisplay.textContent = JSON.stringify(obj);
            } catch (e) {
                jsonDisplay.textContent = value;
            }
        } else {
            jsonDisplay.textContent = '(空)';
        }
    }

    function showStatus(type, message) {
        if (!statusBar) return;
        statusBar.className = 'constraint-status show ' + type;
        statusBar.textContent = message;
    }

    function hideStatus() {
        if (!statusBar) return;
        statusBar.className = 'constraint-status';
    }

    function handleAddConstraint() {
        const key = select.value;
        const value = valueInput.value.trim();
        if (!key) {
            showStatus('error', '❌ 請先選擇限制條件！');
            select.focus();
            return;
        }
        if (!value) {
            showStatus('error', '❌ 請輸入數值！');
            valueInput.focus();
            return;
        }
        const selectedOption = select.options[select.selectedIndex];
        const type = selectedOption.dataset.type || 'string';
        let processedValue;
        let errorMsg = '';
        try {
            processedValue = parseValue(value, type);
        } catch (e) {
            errorMsg = e.message;
        }
        if (errorMsg) {
            showStatus('error', '❌ ' + errorMsg);
            valueInput.focus();
            return;
        }
        const constraints = getConstraints();
        const isUpdate = constraints.hasOwnProperty(key);
        const label = LABEL_MAP[key] || key;
        if (isUpdate) {
            const currentValue = constraints[key];
            const currentStr = formatInputValue(currentValue);
            if (currentStr === value) {
                showStatus('info', 'ℹ️ 數值與目前相同，無需更新');
                return;
            }
            if (!confirm('「' + label + '」已存在\n目前值：' + formatDisplayValue(currentValue) + '\n確定要更新為：' + formatDisplayValue(processedValue) + ' 嗎？')) {
                return;
            }
        }
        constraints[key] = processedValue;
        setConstraints(constraints);
        valueInput.value = '';
        select.value = '';
        valueInput.placeholder = '請輸入數值...';
        btnAdd.textContent = '➕ 加入';
        btnAdd.className = 'btn-add';
        showStatus('success', '✅ ' + (isUpdate ? '更新' : '新增') + '成功！「' + label + '」');
        setTimeout(hideStatus, 3000);
    }

    function removeConstraint(key) {
        const label = LABEL_MAP[key] || key;
        if (!confirm('確定要移除「' + label + '」嗎？')) return;
        const constraints = getConstraints();
        delete constraints[key];
        setConstraints(constraints);
        if (select.value === key) {
            select.value = '';
            valueInput.value = '';
            btnAdd.textContent = '➕ 加入';
            btnAdd.className = 'btn-add';
        }
        showStatus('info', '🗑️ 已移除「' + label + '」');
        setTimeout(hideStatus, 2000);
    }

    function editConstraint(key) {
        const constraints = getConstraints();
        if (!constraints.hasOwnProperty(key)) return;
        const value = constraints[key];
        const formattedValue = formatInputValue(value);
        select.value = key;
        const event = new Event('change');
        select.dispatchEvent(event);
        valueInput.value = formattedValue;
        valueInput.focus();
        valueInput.select();
        showStatus('info', '✏️ 正在編輯「' + (LABEL_MAP[key] || key) + '」，修改後點擊「更新」');
    }

    function bindTagEvents() {
        document.querySelectorAll('.constraint-tags .tag').forEach(function(tag) {
            tag.addEventListener('click', function(e) {
                if (e.target.classList.contains('tag-remove')) return;
                const key = this.dataset.key;
                if (key) {
                    editConstraint(key);
                }
            });
        });
        document.querySelectorAll('.constraint-tags .tag-remove').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const key = this.dataset.key;
                if (key) {
                    removeConstraint(key);
                }
            });
        });
    }

    if (btnAdd) {
        btnAdd.addEventListener('click', handleAddConstraint);
    }
    
    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const placeholder = selectedOption.dataset.placeholder || '請輸入數值...';
            valueInput.placeholder = placeholder;
            const key = selectedOption.value;
            const constraints = getConstraints();
            if (constraints.hasOwnProperty(key)) {
                const value = constraints[key];
                valueInput.value = formatInputValue(value);
                btnAdd.textContent = '🔄 更新';
                btnAdd.className = 'btn-add btn-update';
                showStatus('info', '💡 此條件已存在，點擊「更新」將覆蓋舊值');
            } else {
                valueInput.value = '';
                btnAdd.textContent = '➕ 加入';
                btnAdd.className = 'btn-add';
                hideStatus();
            }
            valueInput.focus();
        } else {
            valueInput.placeholder = '請輸入數值...';
            valueInput.value = '';
            btnAdd.textContent = '➕ 加入';
            btnAdd.className = 'btn-add';
            hideStatus();
        }
    });

    valueInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleAddConstraint();
        }
    });
    <?php else: ?>
    function updateTags(constraints) {
        const keys = Object.keys(constraints);
        if (keys.length === 0) {
            tagsContainer.innerHTML = '<span class="empty-hint">📭 尚未設定任何限制條件</span>';
            return;
        }
        let html = '';
        keys.forEach(key => {
            const label = LABEL_MAP[key] || key;
            const value = constraints[key];
            const displayValue = typeof value === 'boolean' ? (value ? '✅ 啟用' : '❌ 停用') : String(value);
            html += `<span class="tag" data-key="${key}">
                <span class="tag-label">${label}</span>
                <span class="tag-value">${displayValue}</span>
            </span>`;
        });
        tagsContainer.innerHTML = html;
    }

    function updateJsonPreview() {
        const value = hiddenInput.value;
        if (value && value !== '{}') {
            try {
                const obj = JSON.parse(value);
                jsonDisplay.textContent = JSON.stringify(obj);
            } catch (e) {
                jsonDisplay.textContent = value;
            }
        } else {
            jsonDisplay.textContent = '(空)';
        }
    }
    <?php endif; ?>

    // 初始化
    if (CURRENT_CONSTRAINTS) {
        try {
            const initial = JSON.parse(CURRENT_CONSTRAINTS);
            if (typeof initial === 'object' && initial !== null) {
                updateTags(initial);
            }
        } catch (e) {}
    }
    if (hiddenInput.value) {
        try {
            const initial = JSON.parse(hiddenInput.value);
            if (typeof initial === 'object' && initial !== null && Object.keys(initial).length > 0) {
                updateTags(initial);
            }
        } catch (e) {}
    }
    updateJsonPreview();

    console.log(' 版本編輯 UI 已載入' + (<?php echo $isViewOnly ? 'true' : 'false'; ?> ? ' (唯讀模式)' : ''));
    </script>
    <?php
}
function updadetaillist2($mypage, $listtable, $typetable) {
    $myerr = "";
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    $thisid = $GLOBALS['thisid'] ?? 0;
    $isEdit = ($thisid > 0);
    
    
    // 1. 欄位驗證（必填欄位）
    
    if (empty($GLOBALS["display_name"])) {
        $myerr .= "<br>對外顯示名稱 (Display Name) 為必填項目";
    }
    if (empty($GLOBALS["industry_id"])) {
        $myerr .= "<br>目錄分類 / 產業標準範本 (Industry / Schema) 為必填項目";
    }
    if (empty($GLOBALS["description"])) {
        $myerr .= "<br>發布資料描述 (Description) 為必填項目";
    }
    if (empty($GLOBALS["version"])) {
        $myerr .= "<br>版本號 (Version) 為必填項目";
    }
    if (empty($GLOBALS["contract_duration_days"])) {
        $myerr .= "<br>契約有效天數 (Contract Duration) 為必填項目";
    }
    if (!isset($GLOBALS["data_sensitivity_level"]) || $GLOBALS["data_sensitivity_level"] === '') {
        $myerr .= "<br>敏感等級 (Sensitivity Level) 為必填項目";
    }
    if (empty($GLOBALS["update_frequency"])) {
        $myerr .= "<br>更新頻率 (Update Frequency) 為必填項目";
    }
    if (empty($GLOBALS["connector_id"])) {
        $myerr .= "<br>連接器  為必填項目";
    }




    
    // 2. JSON 欄位格式驗證
    
    // keywords：驗證是否為有效 JSON（來自 Tag Input）
    if (!empty($GLOBALS["keywords"])) {
        $testDecode = json_decode($GLOBALS["keywords"], true);
        if ($testDecode === null) {
            $myerr .= "<br>關鍵字 (Keywords) 格式錯誤，請重新輸入";
        }
    }
    
    // allowed_actions：驗證是否為有效 JSON 陣列
    if (!empty($GLOBALS["allowed_actions"]) && is_array($GLOBALS["allowed_actions"])) {
        $testDecode = json_encode($GLOBALS["allowed_actions"]);
        if ($testDecode === false) {
            $myerr .= "<br>可做什麼 (Allowed Actions) 格式錯誤";
        }
    }
    
    // prohibited_actions：驗證是否為有效 JSON 陣列
    if (!empty($GLOBALS["prohibited_actions"]) && is_array($GLOBALS["prohibited_actions"])) {
        $testDecode = json_encode($GLOBALS["prohibited_actions"]);
        if ($testDecode === false) {
            $myerr .= "<br>不可做什麼 (Prohibited Actions) 格式錯誤";
        }
    }
    
    // constraints：驗證是否為有效 JSON
    if (!empty($GLOBALS["constraints"])) {
        $testDecode = json_decode($GLOBALS["constraints"], true);
        if ($testDecode === null) {
            $myerr .= "<br>限制 (Constraints) 格式錯誤，請輸入有效的 JSON 格式";
        }
    }
    
    
    // 3. 檢查權限（發布資料是否存在 + 是否自己的發布資料）
    
    $product = checkDataExists($typetable, $typeid);
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        $myerr .= "<br>無權限操作此發布資料";
    }
    
    if ($myerr) {
        $myerr = "請確認以下內容：" . $myerr;
        return $myerr;
    }
    
    
    // 4. 修改模式：檢查版本是否屬於這個發布資料
    
    if ($isEdit) {
        $versionData = checkDataExists($listtable, $thisid);
        if ($versionData['product_id'] != $typeid) {
            $myerr = "版本不屬於此發布資料";
            return $myerr;
        }
        // 如果已上架（isopen=1），禁止編輯
        if ($versionData['isopen'] == 1) {
            $myerr = "此版本已上架，無法編輯";
            return $myerr;
        }
    }
    
    
    // 5. 組裝 JSON 欄位
    
    // keywords：來自 Tag Input，已經是 JSON 陣列字串
    $keywords = "NULL";
    if (!empty($GLOBALS["keywords"])) {
        $keywords = "'" . addslashes($GLOBALS["keywords"]) . "'";
    }
    
    // allowed_actions：從 checkbox 陣列轉 JSON
    $allowed_actions = "NULL";
    if (isset($GLOBALS['allowed_actions']) && is_array($GLOBALS['allowed_actions']) && !empty($GLOBALS['allowed_actions'])) {
        $allowed_actions = "'" . addslashes(json_encode($GLOBALS['allowed_actions'])) . "'";
    }
    
    // prohibited_actions：從 checkbox 陣列轉 JSON
    $prohibited_actions = "NULL";
    if (isset($GLOBALS['prohibited_actions']) && is_array($GLOBALS['prohibited_actions']) && !empty($GLOBALS['prohibited_actions'])) {
        $prohibited_actions = "'" . addslashes(json_encode($GLOBALS['prohibited_actions'])) . "'";
    }
    
    // constraints：JSON 字串
    $constraints = "NULL";
    if (!empty($GLOBALS['constraints'])) {
        $constraints = "'" . addslashes($GLOBALS['constraints']) . "'";
    }
    
    // connector_id：可為 NULL
    $connector_id = "NULL";
    if (!empty($GLOBALS["connector_id"])) {
        $connector_id = "'" . addslashes($GLOBALS["connector_id"]) . "'";
    }
    
    
    // 6. 組裝快照資料（用於 writeGaiaOperationLog）
    
    $newRecord = [
        'product_id' => $typeid,
        'owner_id' => $myOrgId,
        'display_name' => $GLOBALS['display_name'] ?? '',
        'industry_id' => $GLOBALS['industry_id'] ?? '',
        'description' => $GLOBALS['description'] ?? '',
        'version' => $GLOBALS['version'] ?? '',
        'keywords' => $GLOBALS['keywords'] ?? '',
        'data_format' => $GLOBALS['data_format'] ?? '',
        'contract_duration_days' => $GLOBALS['contract_duration_days'] ?? 30,
        'connector_id' => $GLOBALS['connector_id'] ?? '',
        'allowed_actions' => $allowed_actions,
        'prohibited_actions' => $prohibited_actions,
        'constraints' => $constraints,
        'data_sensitivity_level' => $GLOBALS['data_sensitivity_level'] ?? 0,
        'update_frequency' => $GLOBALS['update_frequency'] ?? '',
        'availability' => $GLOBALS['availability'] ?? ''
    ];
    
    
    // 7. 新增或更新
    
    if ($isEdit) {
        
        // 修改模式
        
        $result = share_update(
            $listtable,
            "display_name = '" . addslashes($GLOBALS['display_name']) . "',
             industry_id = '" . addslashes($GLOBALS['industry_id']) . "',
             description = '" . addslashes($GLOBALS['description']) . "',
             version = '" . addslashes($GLOBALS['version']) . "',
             keywords = " . $keywords . ",
             data_format = '" . addslashes($GLOBALS['data_format'] ?? '') . "',
             contract_duration_days = '" . addslashes($GLOBALS['contract_duration_days'] ?? 30) . "',
             connector_id = " . $connector_id . ",
             allowed_actions = " . $allowed_actions . ",
             prohibited_actions = " . $prohibited_actions . ",
             constraints = " . $constraints . ",
             data_sensitivity_level = '" . addslashes($GLOBALS['data_sensitivity_level']) . "',
             update_frequency = '" . addslashes($GLOBALS['update_frequency']) . "',
             availability = '" . addslashes($GLOBALS['availability'] ?? '') . "',
             lastadmin = '" . addslashes($userId) . "',
             dateupdate = NOW()
             ",
            "thisid = '" . addslashes($thisid) . "'"
        );
        
        if ($result) {
            // 寫入操作紀錄
            if (function_exists('writeGaiaOperationLog')) {
                writeGaiaOperationLog(
                    'version',
                    $thisid,
                    $myOrgId,
                    'UPDATE_VERSION',
                    '更新版本 ' . $GLOBALS['version'],
                    $newRecord,
                    '更新版本：' . $GLOBALS['version'],
                    $userId
                );
            }
            
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 版本修改成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getdetaillist2&typeid=" . $typeid . "&thisid=" . $thisid . "';</script>";
            exit;
        } else {
            return "修改失敗，請重試";
        }
    } else {
        
        // 新增模式
        
        $result = share_insert(
            $listtable,
            "product_id, owner_id, display_name, industry_id, description, version, keywords, data_format, contract_duration_days, connector_id, allowed_actions, prohibited_actions, constraints, data_sensitivity_level, update_frequency, availability, published_at, published_by, isopen, lastadmin, dateadd",
            "'" . addslashes($typeid) . "',
             '" . addslashes($myOrgId) . "',
             '" . addslashes($GLOBALS['display_name']) . "',
             '" . addslashes($GLOBALS['industry_id']) . "',
             '" . addslashes($GLOBALS['description']) . "',
             '" . addslashes($GLOBALS['version']) . "',
             " . $keywords . ",
             '" . addslashes($GLOBALS['data_format'] ?? '') . "',
             '" . addslashes($GLOBALS['contract_duration_days'] ?? 30) . "',
             " . $connector_id . ",
             " . $allowed_actions . ",
             " . $prohibited_actions . ",
             " . $constraints . ",
             '" . addslashes($GLOBALS['data_sensitivity_level']) . "',
             '" . addslashes($GLOBALS['update_frequency']) . "',
             '" . addslashes($GLOBALS['availability'] ?? '') . "',
             NULL,
             '" . addslashes($userId) . "',
             0,
             '" . addslashes($userId) . "',
             NOW()
             "
        );
        
        if ($result) {
            // 取得新增的版本ID
            $temp = share_getfree("SELECT LAST_INSERT_ID() as myid");
            $newVersionId = $temp[0]['myid'];
            
            // 寫入操作紀錄
            if (function_exists('writeGaiaOperationLog')) {
                writeGaiaOperationLog(
                    'version',
                    $newVersionId,
                    $myOrgId,
                    'CREATE_VERSION',
                    '新增版本 ' . $GLOBALS['version'],
                    $newRecord,
                    '新增版本：' . $GLOBALS['version'],
                    $userId
                );
            }
            
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 版本新增成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getdetaillist2&typeid=" . $typeid . "&thisid=" . $newVersionId . "';</script>";
            exit;
        } else {
            return "新增失敗，請重試";
        }
    }
}
function toggleVersionStatus($mypage, $listtable, $typetable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    $thisid = $GLOBALS['thisid'] ?? 0;
    $status = $GLOBALS['status'] ?? 0;
    
    
    // 1. 檢查權限
    
    $product = checkDataExists($typetable, $typeid);
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 2. 檢查版本是否存在且屬於此發布資料
    
    $versionData = checkDataExists($listtable, $thisid);
    if ($versionData['product_id'] != $typeid) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('版本不屬於此發布資料'); history.back();</script>";
        exit;
    }
    
    
    // 3. 如果是「確認版本」($status == 1)，檢查必填欄位
    
    if ($status == 1) {
        // 只有草稿 (isopen=0) 才能確認
        if ($versionData['isopen'] != 0) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 只有草稿狀態的版本才能確認'); history.back();</script>";
            exit;
        }
        
        $missingFields = [];
        
        if (empty($versionData['display_name'])) {
            $missingFields[] = '對外顯示名稱 (display_name)';
        }
        if (empty($versionData['industry_id'])) {
            $missingFields[] = '目錄分類 (industry_id)';
        }
        if (empty($versionData['description'])) {
            $missingFields[] = '發布資料描述 (description)';
        }
        if (empty($versionData['version'])) {
            $missingFields[] = '版本號 (version)';
        }
        if (empty($versionData['contract_duration_days'])) {
            $missingFields[] = '契約有效天數 (contract_duration_days)';
        }
        if ($versionData['data_sensitivity_level'] === '' || $versionData['data_sensitivity_level'] === null) {
            $missingFields[] = '敏感等級 (data_sensitivity_level)';
        }
        if (empty($versionData['update_frequency'])) {
            $missingFields[] = '更新頻率 (update_frequency)';
        }
        
        // JSON 欄位格式檢查
        if (!empty($versionData['keywords'])) {
            $testDecode = json_decode($versionData['keywords'], true);
            if ($testDecode === null) {
                $missingFields[] = '關鍵字/標籤 (keywords) 格式錯誤';
            }
        }
        if (!empty($versionData['allowed_actions'])) {
            $testDecode = json_decode($versionData['allowed_actions'], true);
            if ($testDecode === null) {
                $missingFields[] = '可做什麼 (allowed_actions) 格式錯誤';
            }
        }
        if (!empty($versionData['prohibited_actions'])) {
            $testDecode = json_decode($versionData['prohibited_actions'], true);
            if ($testDecode === null) {
                $missingFields[] = '不可做什麼 (prohibited_actions) 格式錯誤';
            }
        }
        if (!empty($versionData['constraints'])) {
            $testDecode = json_decode($versionData['constraints'], true);
            if ($testDecode === null) {
                $missingFields[] = '限制 (constraints) 格式錯誤';
            }
        }
        
        if (!empty($missingFields)) {
            $msg = "⚠️ 版本資料不完整，無法確認：\n• " . implode("\n• ", $missingFields);
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert(" . json_encode($msg) . "); history.back();</script>";
            exit;
        }
    }  
    // 4. 更新版本狀態（只改版本本身）    
    $result = share_update(
        $listtable,
        "isopen = '" . addslashes($status) . "',
         lastadmin = '" . addslashes($userId) . "',
         dateupdate = NOW()
         ",
        "thisid = '" . addslashes($thisid) . "'"
    );
    
    if ($result) {
        
        // 確認版本後：產生 Policy（不更新主檔）
        
        if ($status == 1) {
            // 更新版本的 published_at
            share_update(
                $listtable,
                "published_at = NOW()",
                "thisid = '" . addslashes($thisid) . "'"
            );
            
            // 產生 ODRL Policy
            $domain = "ttcdb.demosite.com.tw";
            
            $industry = share_getinfo("gaia_industry", "thisid", $versionData['industry_id']);
            $connector = share_getinfo("machine_accounts", "thisid", $versionData['connector_id']);
            
            $policySimple = buildSimplePolicy($typeid, $myOrgId, $versionData, $product, $domain);
            $policyFull = buildFullPolicy($typeid, $myOrgId, $versionData, $product, $industry, $connector, $domain);
            
            share_update(
                $listtable,
                "odrl_policy_simple_json = '" . addslashes(json_encode($policySimple)) . "',
                 odrl_policy_json = '" . addslashes(json_encode($policyFull)) . "'
                 ",
                "thisid = '" . addslashes($thisid) . "'"
            );
        }
        
        $actionText = ($status == 1) ? "確認" : "下架";
        $action = ($status == 1) ? "CONFIRM_VERSION" : "UNPUBLISH_VERSION";
        
        if (function_exists('writeGaiaOperationLog')) {
            writeGaiaOperationLog(
                'version',
                $thisid,
                $product['owner_id'],
                $action,
                '版本 ' . $versionData['version'] . ' ' . $actionText,
                ['isopen' => $status],
                '版本 ' . $actionText,
                $userId
            );
        }
        
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 版本" . $actionText . "成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getContact2&typeid=" . $typeid . "';</script>";
        exit;
    } else {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('操作失敗，請重試'); history.back();</script>";
        exit;
    }
}
function switchVersion($mypage, $listtable, $typetable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    $thisid = $GLOBALS['thisid'] ?? 0;
    
    
    // 1. 檢查權限（發布資料是否存在 + 是否自己的發布資料）
    
    $product = checkDataExists($typetable, $typeid);
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 2. 檢查版本是否存在且屬於此發布資料
    
    $versionData = checkDataExists($listtable, $thisid);
    if ($versionData['product_id'] != $typeid) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('版本不屬於此發布資料'); history.back();</script>";
        exit;
    }
    
    
    // 3. 檢查版本是否為「確認版本」（只有確認版本才能切換）
    //    isopen: 0=草稿, 1=確認版本, 2=目前版本
    
    if ($versionData['isopen'] != 1) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 只有「確認版本」才能切換為當前版本'); history.back();</script>";
        exit;
    }
    
    
    // 4. 檢查是否已經是當前版本
    
    if ($versionData['thisid'] == $product['current_version_id']) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('此版本已經是當前版本'); history.back();</script>";
        exit;
    }
    
    
    // 5. 取得舊的目前版本 ID（切換前先記錄）
    
    $oldVersionId = $product['current_version_id'];
    $oldVersion = $product['current_version'];
    
    
    // 6. 更新版本表
    
    // 6a. 先把該發布資料所有版本設為「確認版本」（isopen = 1）
    share_update(
        $listtable,
        "isopen = 1",
        "product_id = '" . addslashes($typeid) . "'"
    );
    
    // 6b. 把要切換的版本設為「目前版本」（isopen = 2）
    share_update(
        $listtable,
        "isopen = 2",
        "thisid = '" . addslashes($thisid) . "'"
    );
    
    
    // 7. 更新主檔的當前版本
    
    $result = share_update(
        $typetable,
        "current_version_id = '" . addslashes($thisid) . "',
         current_version = '" . addslashes($versionData['version']) . "',
         lastadmin = '" . addslashes($userId) . "',
         dateupdate = NOW()
         ",
        "thisid = '" . addslashes($typeid) . "'"
    );
    
    if ($result) {
        
        // 8. 寫入操作紀錄
        
        if (function_exists('writeGaiaOperationLog')) {
            writeGaiaOperationLog(
                'version',
                $thisid,
                $product['owner_id'],
                'SWITCH_VERSION',
                '切換到版本 ' . $versionData['version'],
                [
                    'old_version_id' => $oldVersionId,
                    'old_version' => $oldVersion,
                    'new_version_id' => $thisid,
                    'new_version' => $versionData['version']
                ],
                '切換當前版本',
                $userId
            );
        }
        
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 已切換到版本 " . $versionData['version'] . "！'); location.href='my_products.html?tf=" . $mypage . "&job=getContact2&typeid=" . $typeid . "';</script>";
        exit;
    } else {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('切換失敗，請重試'); history.back();</script>";
        exit;
    }
}
function viewAudit($mypage, $listtable, $typetable) {
    $typeid = $GLOBALS['typeid'] ?? 0;
    $thisid = $GLOBALS['thisid'] ?? 0;
    
    
    // 1. 檢查權限（發布資料是否存在 + 是否自己的發布資料）
    
    $product = checkDataExists($typetable, $typeid);
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 2. 檢查版本是否存在且屬於此發布資料
    
    $versionData = checkDataExists($listtable, $thisid);
    if ($versionData['product_id'] != $typeid) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('版本不屬於此發布資料'); history.back();</script>";
        exit;
    }
    
    
    // 3. 取得歷程紀錄（不 JOIN fun_user）
    
    $auditList = share_gettable("gaia_version_history  WHERE product_id = '" . addslashes($typeid) . "' AND version_id = '" . addslashes($thisid) . "' ORDER BY changed_at DESC");
    
    echo "<script nonce='" . $_SESSION['nonce'] . "'>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-snapshot').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var content = this.nextElementSibling;
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                this.textContent = '📋 收起快照';
            } else {
                content.style.display = 'none';
                this.textContent = '📋 檢視快照';
            }
        });
    });
});
</script>";	
	
	
    
    // 5. 顯示頁面
    
    echo "<h2>版本 " . $versionData['version'] . " - 歷程記錄</h2>";
    echo "<hr>";
    
    echo "<div >";
    echo "<a href='my_products.html?tf=".$mypage."&job=getContact2&typeid=".$typeid."' class='cancel-btn-sm'>← 返回版本列表</a>";
    echo "</div>";
    
    // 版本資訊區塊
    echo "<div >";
    echo "<TABLE class='formtable'>";
    echo "<tr>";
    echo "<td width='15%'><strong>版本號</strong></td>";
    echo "<td width='35%'>" . $versionData['version'] . "</td>";
    echo "<td width='15%'><strong>發布日期</strong></td>";
    echo "<td width='35%'>" . $versionData['published_at'] . "</td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td><strong>狀態</strong></td>";
    echo "<td>" . ($versionData['isopen'] == 1 ? "🟢 上架" : "🔴 下架") . "</td>";
    echo "<td></td>";
    echo "<td></td>";
    echo "</tr>";
    echo "</TABLE>";
    echo "</div>";
    
    
    // 稽核列表
    
    echo "<div class='table-wrapper'>";
    echo "<TABLE class='formtable'>";
    echo "<tr>";
    echo "<th width='20%'>時間</th>";
    echo "<th width='15%'>動作</th>";
    echo "<th width='25%'>摘要</th>";
    echo "<th width='40%'>快照資料</th>";
    echo "</tr>";
    
if (!empty($auditList)) {
    foreach ($auditList as $row) {
        // 動作標籤顏色
        switch ($row['action']) {
            case 'CREATE':
                $actionLabel = '🟢 建立';
                break;
            case 'UPDATE':
                $actionLabel = '🔵 修改';
                break;
            case 'SWITCH':
                $actionLabel = '🟠 切換';
                break;
            case 'PUBLISH':
                $actionLabel = '🟢 上架';
                break;
            case 'UNPUBLISH':
                $actionLabel = '🔴 下架';
                break;
            default:
                $actionLabel = $row['action'];
        }
        
        // 只有 CREATE 和 UPDATE 才顯示快照按鈕
        $snapshotHtml = '';
        if (!empty($row['snapshot_data']) && ($row['action'] == 'CREATE' || $row['action'] == 'UPDATE')) {
            $snapshotJson = json_encode(json_decode($row['snapshot_data'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            $snapshotHtml = "<a href='#' class='toggle-snapshot' class='toggle-snapshot'>📋 檢視快照</a>";
            $snapshotHtml .= "<div class='snapshot-content'>";
            $snapshotHtml .= "<pre class='snapshot-pre'>" . $snapshotJson . "</pre>";
            $snapshotHtml .= "</div>";
        } else {
            $snapshotHtml = "<span class='snapshot-empty'>-</span>";
        }
        
        echo "<tr>";
        echo "<td>" . $row['changed_at'] . "</td>";
        echo "<td>" . $actionLabel . "</td>";
        echo "<td class='change-summary'>" . ($row['change_summary'] ?? '') . "</td>";
        echo "<td>" . $snapshotHtml . "</td>";
        echo "</tr>";
    }
}
    echo "</TABLE>";
    echo "</div>";
    
    echo "<div >顯示 " . count($auditList) . " 筆</div>";

}
function deleteProduct($mypage, $typetable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    
    
    // 1. 檢查權限
    
    $product = checkDataExists($typetable, $typeid);
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 2. 檢查發布資料是否有版本（有版本不允許刪除）
    
    if ($product['current_version_id'] > 0) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 此發布資料已有版本，無法刪除'); history.back();</script>";
        exit;
    }
    
    
    // 3. 取得要刪除的相關資料（用於 Log 快照）
    
    $versions = share_gettable("gaia_product_versions WHERE product_id = '" . addslashes($typeid) . "'");
    
    $snapshotData = [
        'product' => $product,
        'versions' => $versions
    ];
    
    
    // 4. 執行刪除
    
    // 刪除版本
    share_del("gaia_product_versions WHERE product_id = '" . addslashes($typeid) . "'");
    // 刪除發布資料主檔
    share_del($typetable . " WHERE thisid = '" . addslashes($typeid) . "'");
    
    
    // 5. 寫入 Gaia-X 操作紀錄
    
    $changeSummary = '刪除發布資料：' . $product['resource_name'] . '（ID：' . $typeid . '）';
    if (!empty($versions)) {
        $changeSummary .= '，包含 ' . count($versions) . ' 個版本';
    }
    
    writeGaiaOperationLog(
        'product',                    // target_type
        $typeid,                      // target_id
        $product['owner_id'],         // owner_id（公司ID）
        'DELETE_PRODUCT',             // action
        '刪除發布資料：' . $product['resource_name'],  // action_description
        $snapshotData,                // snapshot_data（刪除前的資料）
        '刪除發布資料（含版本）',           // change_summary
        $userId                       // changed_by
    );
    
    
    // 6. 回傳成功訊息
    
    echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 發布資料刪除成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getContact1';</script>";
    exit;
}
function toggleProductStatus($mypage, $typetable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    $status = $GLOBALS['status'] ?? 0;
    
    
    // 1. 檢查權限
    
    $product = checkDataExists($typetable, $typeid);
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 2. 如果是「上架」($status == 1)，檢查條件
    
    if ($status == 1) {
        // 2.1 檢查是否有版本
        if ($product['current_version_id'] == 0 || empty($product['current_version'])) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 此發布資料尚未建立版本，請先建立版本後再上架'); history.back();</script>";
            exit;
        }
        
        // 2.2 檢查當前版本是否為「目前版本」(isopen = 2)
        $currentVersion = share_getinfo('gaia_product_versions', 'thisid', $product['current_version_id']);
        if (!$currentVersion || $currentVersion['isopen'] != 2) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 當前版本尚未設為「目前版本」，請先確認版本後再上架'); history.back();</script>";
            exit;
        }
    }
    
    
    // 3. 如果是「下架」($status == 0)，檢查是否可下架
    //    （上架後無下架功能，所以不允許下架）
    
 //   if ($status == 0) {
 //       echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 發布資料上架後無法下架'); history.back();</script>";
 //       exit;
 //   }
    
    
    // 4. 更新發布資料狀態
    
    $result = share_update(
        $typetable,
        "isopen = '" . addslashes($status) . "',
         lastadmin = '" . addslashes($userId) . "',
         dateupdate = NOW()
         ",
        "thisid = '" . addslashes($typeid) . "'"
    );
    
    if ($result) {
        $actionText = ($status == 1) ? "上架" : "下架";
        $action = ($status == 1) ? "PUBLISH_PRODUCT" : "UNPUBLISH_PRODUCT";
        
        // 用共用函式寫入操作紀錄
        writeGaiaOperationLog(
            'product',
            $typeid,
            $product['owner_id'],
            $action,
            '發布資料 ' . $actionText,
            ['isopen' => $status],
            '發布資料 ' . $actionText,
            $userId
        );
        
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 發布資料" . $actionText . "成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getContact1';</script>";
        exit;
    } else {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('操作失敗，請重試'); history.back();</script>";
        exit;
    }
}

function deleteVersion($mypage, $listtable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    $thisid = $GLOBALS['thisid'] ?? 0;
    
    
    // 1. 檢查權限（發布資料是否存在 + 是否自己的發布資料）
    
    $product = checkDataExists('gaia_data_products', $typeid);
    $myOrgId = checkUserOrganization();
    if ($product['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 2. 檢查版本是否存在且屬於此發布資料
    
    $versionData = checkDataExists($listtable, $thisid);
    if ($versionData['product_id'] != $typeid) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('版本不屬於此發布資料'); history.back();</script>";
        exit;
    }
    
    
    // 3. 檢查是否為草稿（只有草稿可刪除）
    
    // isopen: 0=草稿, 1=確認版本, 2=目前版本
    if ($versionData['isopen'] != 0) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 只有草稿狀態的版本才能刪除'); history.back();</script>";
        exit;
    }
    
    
    // 4. 檢查是否為當前版本（草稿不可能是當前版本，但還是檢查）
    
    if ($versionData['thisid'] == $product['current_version_id']) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 無法刪除當前使用的版本'); history.back();</script>";
        exit;
    }
    
    
    // 5. 準備快照資料（刪除前記錄）
    
    $snapshotData = [
        'version_id' => $versionData['thisid'],
        'version' => $versionData['version'],
        'version_note' => $versionData['version_note'] ?? '',
        'product_id' => $versionData['product_id'],
        'isopen' => $versionData['isopen'],
        'published_at' => $versionData['published_at']
    ];
    
    
    // 6. 刪除版本
    
    share_del($listtable . " WHERE thisid = '" . addslashes($thisid) . "'");
    
    
    // 7. 寫入操作紀錄
    
    if (function_exists('writeGaiaOperationLog')) {
        writeGaiaOperationLog(
            'version',                        // target_type
            $thisid,                          // target_id
            $product['owner_id'],             // owner_id（公司ID）
            'DELETE_VERSION',                 // action
            '刪除版本：' . $versionData['version'],  // action_description
            $snapshotData,                    // snapshot_data（刪除前的資料）
            '刪除版本：' . $versionData['version'],  // change_summary
            $userId                           // changed_by
        );
    }
    
    
    // 8. 回傳成功訊息
    
    echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 版本刪除成功！'); location.href='my_products.html?tf=" . $mypage . "&job=getContact2&typeid=" . $typeid . "';</script>";
    exit;
}

// 產生簡單 Policy（odrl_policy_simple_json）

function buildSimplePolicy($productId, $companyId, $versionData, $product, $domain) {
    // 轉換 allowed_actions
    $allowedActions = [];
    if (!empty($versionData['allowed_actions'])) {
        $actions = json_decode($versionData['allowed_actions'], true);
        if (is_array($actions)) {
            foreach ($actions as $action) {
                $allowedActions[] = 'odrl:' . strtolower($action);
            }
        }
    }
    
    // 轉換 prohibited_actions
    $prohibitedActions = [];
    if (!empty($versionData['prohibited_actions'])) {
        $actions = json_decode($versionData['prohibited_actions'], true);
        if (is_array($actions)) {
            foreach ($actions as $action) {
                $prohibitedActions[] = 'odrl:' . strtolower($action);
            }
        }
    }
    
    $policy = [
        '@id' => "did:web:{$domain}:policy:{$productId}",
        '@type' => 'odrl:Offer',
        'assigner' => "did:web:{$domain}:participant:{$companyId}",
        'permission' => [],
        'prohibition' => []
    ];
    
    if (!empty($allowedActions)) {
        $policy['permission'][] = [
            'action' => $allowedActions
        ];
    }
    
    if (!empty($prohibitedActions)) {
        $policy['prohibition'][] = [
            'action' => $prohibitedActions
        ];
    }
    
    return $policy;
}


// 產生完整 Policy（odrl_policy_json）

function buildFullPolicy($productId, $companyId, $versionData, $product, $industry, $connector, $domain) {
    // 先產生簡單 Policy
    $policy = buildSimplePolicy($productId, $companyId, $versionData, $product, $domain);
    
    // 組裝 Dataset
    $keywords = [];
    if (!empty($versionData['keywords'])) {
        $keywords = json_decode($versionData['keywords'], true);
        if (!is_array($keywords)) {
            $keywords = [];
        }
    }
    
    $dataset = [
        '@id' => "did:web:{$domain}:dataset:{$productId}",
        'dct:title' => $versionData['display_name'],
        'dct:description' => $versionData['description'],
        'dcat:version' => $versionData['version'],
        'dct:issued' => date('Y-m-d\TH:i:s\Z'),
        'dcat:theme' => $industry['thisname'] . ' (' . $industry['thiscode'] . ')',
        'dcat:keyword' => $keywords,
        'dcat:format' => $versionData['data_format'] ?? 'JSON',
        'dct:accrualPeriodicity' => $versionData['update_frequency'],
        'dcat:availability' => $versionData['availability'] ?? '',
        'dcat:endpointURL' => "did:web:{$domain}:connector:" . ($connector['thisid'] ?? ''),
        'dcat:conformsTo' => [
            '@id' => "did:web:{$domain}:schema:" . ($industry['thisid'] ?? '')
        ]
    ];
    
    // 過濾空值
    $dataset = array_filter($dataset, function($value) {
        return $value !== '' && $value !== null;
    });
    
    // 組裝完整 Policy
    return [
        'policy' => $policy,
        'dataset' => $dataset
    ];
}
?>

    </div>
    <!-- end content-area -->

</div>
<!-- end member-container -->

</div>
</section>
</main>
<?php 
$pdo = null;
require_once 'footer.php';?>  
	
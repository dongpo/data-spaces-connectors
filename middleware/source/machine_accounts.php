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
$currentPage = 'machine_accounts';
require_once 'seonew.php';
require_once 'headerlogin.php';
require_once 'adm/config/product.php';
checkPagePermission('machine_accounts');
$responz="";
global $imgurl;
$mypage="machine_accounts";
$typetable="machine_accounts";     //資料庫主table

 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;
 }elseif($job=="deleteMachine"){	   
   $tagon=1;
   $tagshow=1;   
   deleteMachine($mypage, $typetable);
 }elseif($job=="toggleMachineStatus"){	   
   $tagon=1;
   $tagshow=1;   
   toggleMachineStatus($mypage, $typetable);  
   
   
   
 }elseif($job=="getdetaillist1"){	
   $tagon=2;
   $tagshow=2;   
 }else if($job=="uptypedetaillist1" ){
   $tagon=2;
   $tagshow=2;  
   $responz=uptypedetaillist1($typetable,$mypage);   
 }else{
   $tagon=1;
   $tagshow=1;	 
 }
?>
<main id="main" role="main">
<section aria-label="連接器管理 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="連接器管理 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">連接器管理</h1>
</div>
</section>
<section aria-label="連接器管理" class="detail-page-hero detail-intro-after-query">
<div class="member-container">

    <?php include 'sidebar.php'; ?>
<div class="content-area">
	<div class='contentin'>
  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>
		<a href='machine_accounts.html?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>列表</div>
        </a>
<?php 
if 	($tagon>1 ){
?>	
		<a href='machine_accounts.html?tf=<?=$mypage?>&job=getdetaillist1&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'>明細</div>
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
function toggleMachineStatus($mypage, $typetable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    $status = $GLOBALS['status'] ?? 0;
    
    
    // 1. 檢查是否存在
    
    $machineList = share_gettable($typetable . " WHERE thisid = '" . addslashes($typeid) . "'");
    if (empty($machineList)) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('資料不存在'); history.back();</script>";
        exit;
    }
    $machine = $machineList[0];
    
    
    // 2. 檢查權限
    
    $myOrgId = checkUserOrganization();
    if ($machine['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 3. 檢查是否已啟用（已啟用不能重複啟用）
    
    if ($status == 1 && $machine['isopen'] == 1) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 此連接器已是「啟用」狀態'); history.back();</script>";
        exit;
    }
    
    
    // 4. 執行啟用/停用
    
    // 只有「不啟用 → 啟用」這個方向
    if ($status == 1 && $machine['isopen'] == 0) {
        $result = share_update(
            $typetable,
            "isopen = 1,
             lastadmin = '" . addslashes($userId) . "',
             dateupdate = NOW()",
            "thisid = '" . addslashes($typeid) . "'"
        );
        
        if ($result) {
            // 寫入操作紀錄
            if (function_exists('writeGaiaOperationLog')) {
                $newRecord = share_getinfo($typetable, "thisid", $typeid);
                writeGaiaOperationLog(
                    'machine_account',
                    $typeid,
                    $machine['owner_id'],
                    'ENABLE_MACHINE',
                    '啟用連接器：' . $machine['thisname'],
                    $newRecord,
                    '狀態變更為：啟用',
                    $userId
                );
            }
            
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 已啟用！'); location.href='machine_accounts.html?tf=" . $mypage . "&job=getContact1';</script>";
            exit;
        } else {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 啟用失敗，請重試'); history.back();</script>";
            exit;
        }
    }
    
    
    // 其他情況（不處理）
    
    echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 無效的操作'); history.back();</script>";
    exit;
} 
 function deleteMachine($mypage, $typetable) {
    $userId = $_SESSION['companyid'] ?? 0;
    $typeid = $GLOBALS['typeid'] ?? 0;
    
    
    // 1. 檢查是否存在
    
    $machineList = share_gettable($typetable . " WHERE thisid = '" . addslashes($typeid) . "'");
    if (empty($machineList)) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('資料不存在'); history.back();</script>";
        exit;
    }
    $machine = $machineList[0];
    
    
    // 2. 檢查權限
    
    $myOrgId = checkUserOrganization();
    if ($machine['owner_id'] != $myOrgId) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
        exit;
    }
    
    
    // 3. 檢查是否已啟用（啟用中不允許刪除）
    
    if ($machine['isopen'] == 1) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('⚠️ 此連接器已「啟用」，無法刪除'); history.back();</script>";
        exit;
    }
    
    
    // 4. 取得要刪除的資料（用於 Log 快照）
    
    $snapshotData = [
        'machine' => $machine
    ];
    
    
    // 5. 執行刪除
    
    share_del($typetable . " WHERE thisid = '" . addslashes($typeid) . "'");
    
    
    // 6. 寫入操作紀錄
    
    $changeSummary = '刪除連接器：' . $machine['thisname'] . '（ID：' . $typeid . '）';
    
    if (function_exists('writeGaiaOperationLog')) {
        writeGaiaOperationLog(
            'machine_account',              // target_type
            $typeid,                        // target_id
            $machine['owner_id'],           // owner_id（公司ID）
            'DELETE_MACHINE',               // action
            '刪除連接器：' . $machine['thisname'],  // action_description
            $snapshotData,                  // snapshot_data（刪除前的資料）
            $changeSummary,                 // change_summary
            $userId                         // changed_by
        );
    }
    
    
    // 7. 回傳成功訊息
    
    echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 連接器刪除成功！'); location.href='machine_accounts.html?tf=" . $mypage . "&job=getContact1';</script>";
    exit;
}
function uptypedetaillist1($mytable, $mypage) {
    $myerr = "";
    $userId = $_SESSION['companyid'] ?? 0;
    $thisid = $GLOBALS['typeid'] ?? 0;
$myOrgType = $_SESSION['org_type'] ?? '';
$showMachineUrl = ($myOrgType == '資料提供者' || $myOrgType == '兩者皆有');
    $userOrgId = checkUserOrganization();
    
    //  取得並清理輸入資料（只去前後空白）
    
    $thisname = trim($GLOBALS["thisname"]);
    $machine_url = trim($GLOBALS["machine_url"]);
    $thispk = trim($GLOBALS["thispk"]);    
    
    // 驗證必填欄位
    
    if (empty($GLOBALS["thisname"])) {
        $myerr .= "<br>連接器名稱為必填項目";
    }
	
	
	if ($showMachineUrl) {
		if (empty($GLOBALS["machine_url"])) {
			$myerr .= "<br>連接器網址為必填項目";
		}
	}
	
	
    if (empty($GLOBALS["thispk"])) {
        $myerr .= "<br>公鑰為必填項目";
    }
    
    if ($myerr) {
        $myerr = "請確認以下內容：" . $myerr;
        return $myerr;
    }
    
    
    // 檢查同公司內連接器名稱是否重複
    
    $checkSql = "SELECT thisid FROM machine_accounts 
                 WHERE owner_id = '" . addslashes($userOrgId) . "' 
                 AND thisname = '" . addslashes($GLOBALS["thisname"]) . "'";
    
    // 編輯模式：排除自己
    if ($thisid > 0) {
        $checkSql .= " AND thisid != '" . addslashes($thisid) . "'";
    }
    
    $checkExist = share_getfree($checkSql);
    if (!empty($checkExist)) {
        return "請確認以下內容：<br>此連接器名稱已存在，請使用不同的名稱";
    }
if ($showMachineUrl) {    
    
    // 檢查同公司內連接器網址是否重複
    
    $checkUrlSql = "SELECT thisid FROM machine_accounts 
                    WHERE owner_id = '" . addslashes($userOrgId) . "' 
                    AND machine_url = '" . addslashes($GLOBALS["machine_url"]) . "'";
    
    if ($thisid > 0) {
        $checkUrlSql .= " AND thisid != '" . addslashes($thisid) . "'";
    }
    
    $checkUrlExist = share_getfree($checkUrlSql);
    if (!empty($checkUrlExist)) {
        return "請確認以下內容：<br>此連接器網址已存在，請使用不同的網址";
    }
}   
    
    // 判斷新增或編輯
    
    $temp = share_getinfo("machine_accounts", "thisid", $thisid);
    
    if ($temp) {
        
        // 編輯模式
        
        if ($temp['owner_id'] != $userOrgId) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限修改此連接器'); history.back();</script>";
            exit;
        }
        
        // 如果已啟用，禁止編輯
        if ($temp['isopen'] == 1) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('❌ 此連接器已「啟用」，無法編輯'); history.back();</script>";
            exit;
        }
        
        // 記錄修改前的資料
        $oldData = [
            'thisname' => $temp['thisname'],
            'machine_url' => $temp['machine_url'],
            'thispk' => $temp['thispk']
        ];
        
        // 更新
        $result = share_update(
            "machine_accounts",
            "thisname = '" . addslashes($GLOBALS["thisname"]) . "',
             machine_url = '" . addslashes($GLOBALS["machine_url"]) . "',
             thispk = '" . addslashes($GLOBALS["thispk"]) . "',
             lastadmin = '" . addslashes($userId) . "',
             dateupdate = NOW()",
            "thisid='" . $GLOBALS['typeid'] . "'"
        );
        
        if ($result) {
            // 取得更新後的完整資料
            $newRecord = share_getinfo("machine_accounts", "thisid", $thisid);
            
            // 產生變更摘要
            $changeSummary = [];
            if ($oldData['thisname'] != $GLOBALS["thisname"]) {
                $changeSummary[] = "連接器名稱：『" . $oldData['thisname'] . "』→『" . $GLOBALS["thisname"] . "』";
            }
            if ($oldData['machine_url'] != $GLOBALS["machine_url"]) {
                $changeSummary[] = "連接器網址：『" . $oldData['machine_url'] . "』→『" . $GLOBALS["machine_url"] . "』";
            }
            if ($oldData['thispk'] != $GLOBALS["thispk"]) {
                $changeSummary[] = "公鑰已更新";
            }
            $changeSummaryText = !empty($changeSummary) ? implode('；', $changeSummary) : '無變更';
            
            // 寫入操作紀錄
            if (function_exists('writeGaiaOperationLog')) {
                writeGaiaOperationLog(
                    'machine_account',
                    $thisid,
                    $userOrgId,
                    'UPDATE_MACHINE',
                    '更新連接器',
                    $newRecord,
                    $changeSummaryText,
                    $userId
                );
            }
            
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 更新成功！'); location.href='machine_accounts.html?tf=" . $mypage . "&job=getContact1';</script>";
            exit;
        } else {
            return "更新失敗，請重試";
        }
        
    } else {
        
        // 新增模式（預設不啟用 isopen = 0）
        
        $result = share_insert(
            "machine_accounts",
            "thisname, machine_url, thispk, owner_id, isopen, lastadmin, dateadd",
            "'" . addslashes($GLOBALS["thisname"]) . "',
             '" . addslashes($GLOBALS["machine_url"]) . "',
             '" . addslashes($GLOBALS["thispk"]) . "',
             '" . addslashes($userOrgId) . "',
             0,
             '" . addslashes($userId) . "',
             NOW()"
        );
        
        if ($result) {
            $temp = share_getfree("SELECT LAST_INSERT_ID() as myid");
            $news_id = $temp[0]['myid'];
            
            // 取得完整的新增資料
            $newRecord = share_getinfo("machine_accounts", "thisid", $news_id);
            
            // 寫入操作紀錄
            if (function_exists('writeGaiaOperationLog')) {
                writeGaiaOperationLog(
                    'machine_account',
                    $news_id,
                    $userOrgId,
                    'CREATE_MACHINE',
                    '新增連接器：' . $GLOBALS["thisname"],
                    $newRecord,
                    '新增連接器：' . $GLOBALS["thisname"] . '，網址：' . $GLOBALS["machine_url"],
                    $userId
                );
            }
            
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('✅ 新增成功！'); location.href='machine_accounts.html?tf=" . $mypage . "&job=getContact1';</script>";
            exit;
        } else {
            return "新增失敗，請重試";
        }
    }
}

function getdetail1($mypage, $typetable) {
    global $imgurl;
    
    $isEdit = ($GLOBALS['typeid'] ?? 0) > 0;
    $isReadonly = ($GLOBALS['mode'] ?? '') == 'view';
    $me = [];
    
    
    // 取得登入者身份
    
    $myOrgType = $_SESSION['org_type'] ?? '';
    
    
    // 編輯/查看模式：載入資料 + 權限檢查
    
    if ($isEdit) {
        $machineList = share_gettable("machine_accounts WHERE thisid = '" . addslashes($GLOBALS['typeid']) . "'");
        if (empty($machineList)) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('資料不存在'); history.back();</script>";
            exit;
        }
        $me = $machineList[0];
        
        $myOrgId = checkUserOrganization();
        if ($me['owner_id'] != $myOrgId) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
            exit;
        }
        
        if ($me['isopen'] == 1) {
            $isReadonly = true;
        }
    }
    
    // 從 POST 取值（儲存失敗時保留）
    $me['thisname'] = checkval($GLOBALS['thisname'], $me['thisname'] ?? '');
    $me['machine_url'] = checkval($GLOBALS['machine_url'], $me['machine_url'] ?? '');
    $me['thispk'] = checkval($GLOBALS['thispk'], $me['thispk'] ?? '');
    
    
    // 設定標題和按鈕文字
    
    if ($isReadonly) {
        $title = "🔍 檢視連接器";
        $btnText = "";
        $formAction = "";
    } elseif ($isEdit) {
        $title = "✏️ 編輯連接器";
        $btnText = "儲存修改";
        $formAction = "machine_accounts.html?tf=".$mypage."&job=uptypedetaillist1&typeid=".($GLOBALS['typeid'] ?? 0);
    } else {
        $title = "➕ 新增連接器";
        $btnText = "建立連接器";
        $formAction = "machine_accounts.html?tf=".$mypage."&job=uptypedetaillist1&typeid=0";
    }
    
    
    // 判斷是否要顯示連接器網址（只有資料提供者/兩者皆有才顯示） 
    
    $showMachineUrl = ($myOrgType == '資料提供者' || $myOrgType == '兩者皆有');
    
    
    // 輸出 HTML
    
    echo "<span class='fR'><span class='redtext'>＊</span>必填</span>";
    echo "<h2>" . $title . "</h2>";
    echo "<hr>";
    
    // 唯讀模式
    if ($isReadonly) {
        echo "<TABLE class='formtable' width='100%'>";
        
        echo "<tr>";
        echo "  <th class='label-col'>連接器 ID<br><span class='field-hint'>thisid</span></th>";
        echo "  <td class='tleft'><span class='info-display'>" . $me['thisid'] . "</span></td>";
        echo "</tr>";
        
        $statusText = ($me['isopen'] == 1) ? "🟢 啟用" : "⚪ 不啟用";
        $statusClass = ($me['isopen'] == 1) ? "online" : "offline";
        echo "<tr>";
        echo "  <th class='label-col'>狀態<br><span class='field-hint'>isopen</span></th>";
        echo "  <td class='tleft'><span class='status-badge " . $statusClass . "'>" . $statusText . "</span></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "  <th class='label-col'>連接器名稱<br><span class='field-hint'>thisname</span></th>";
        echo "  <td class='tleft'><span class='info-display'>" . htmlspecialchars($me['thisname'], ENT_QUOTES) . "</span></td>";
        echo "</tr>";
        
        // 依據是否有填寫網址來顯示連接器或客戶端
        if (!empty($me['machine_url'])) {
            echo "<tr>";
            echo "  <th class='label-col'>連接器網址<br><span class='field-hint'>machine_url</span></th>";
            echo "  <td class='tleft'><span class='info-display'>" . htmlspecialchars($me['machine_url'], ENT_QUOTES) . "</span></td>";
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "  <th class='label-col'>帳號類型<br><span class='field-hint'>type</span></th>";
            echo "  <td class='tleft'><span class='info-display'>💻 客戶端 (Client)</span></td>";
            echo "</tr>";
        }
        
        echo "<tr>";
        echo "  <th class='label-col'>公鑰<br><span class='field-hint'>thispk</span></th>";
        echo "  <td class='tleft'><span class='info-display' style='word-break:break-all;'>" . nl2br(htmlspecialchars($me['thispk'], ENT_QUOTES)) . "</span></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "  <th class='label-col'>建立時間<br><span class='field-hint'>dateadd</span></th>";
        echo "  <td class='tleft'><span class='info-display'>" . $me['dateadd'] . "</span></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "  <td colspan='2' class='tcenter form-actions'>";
        echo "    <a href='machine_accounts.html?tf=".$mypage."&job=getContact1' class='gray-btn'>返回列表</a>";
        echo "  </td>";
        echo "</tr>";
        
        echo "</TABLE>";
        echo "</form>";
        return;
    }
    
    
    // 新增/編輯模式：顯示表單
    
    echo "<form action='" . $formAction . "' method='post' enctype='multipart/form-data' id='machineForm'>";
    echo "<TABLE class='formtable' width='100%'>";
    
    if ($isEdit) {
        $statusText = ($me['isopen'] == 1) ? "🟢 啟用" : "⚪ 不啟用";
        $statusClass = ($me['isopen'] == 1) ? "online" : "offline";
        
        echo "<tr>";
        echo "  <th class='label-col'>連接器 ID<br><span class='field-hint'>thisid</span></th>";
        echo "  <td class='tleft'><span class='info-display'>" . $me['thisid'] . "</span></td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "  <th class='label-col'>狀態<br><span class='field-hint'>isopen</span></th>";
        echo "  <td class='tleft'><span class='status-badge " . $statusClass . "'>" . $statusText . "</span></td>";
        echo "</tr>";
    }
    

    if ($myOrgType == '兩者皆有') {
        $isClient = ($isEdit && empty($me['machine_url'])); 
        echo "<tr>";
        echo "  <th class='label-col'><span class='redtext'>＊</span>帳號類型</th>";
        echo "  <td class='tleft'>";
        echo "    <select id='agentTypeSelect' name='agent_type_ui'>";
        echo "      <option value='server' " . (!$isClient ? 'selected' : '') . ">🖥️ 連接器 (需填寫網址)</option>";
        echo "      <option value='client' " . ($isClient ? 'selected' : '') . ">💻 客戶端 (無需填寫網址)</option>";
        echo "    </select>";
        echo "  </td>";
        echo "</tr>";
    }
    
    // 連接器名稱
    echo "<tr>";
    echo "  <th class='label-col'><span class='redtext'>＊</span>連接器名稱<br><span class='field-hint'>thisname</span></th>";
    echo "  <td class='tleft'><input type='text' name='thisname' value='" . htmlspecialchars($me['thisname'], ENT_QUOTES) . "' form='machineForm' required></td>";
    echo "</tr>";
    
    
    // 連接器網址
    if ($showMachineUrl) {
        $urlDisplayStyle = ($myOrgType == '兩者皆有' && empty($me['machine_url']) && $isEdit) ? "style='display:none;'" : "";
        echo "<tr id='urlRow' " . $urlDisplayStyle . ">";
        echo "  <th class='label-col'><span class='redtext' id='urlAsterisk'>＊</span>連接器網址<br><span class='field-hint'>machine_url</span></th>";
        echo "  <td class='tleft'><input type='text' name='machine_url' id='machine_url_input' value='" . htmlspecialchars($me['machine_url'], ENT_QUOTES) . "' form='machineForm' " . ($myOrgType == '資料提供者' ? 'required' : '') . "></td>";
        echo "</tr>";
    } else {
        echo "<input type='hidden' name='machine_url' value=''>";
    }
    
    // 公鑰
    echo "<tr>";
    echo "  <th class='label-col'><span class='redtext'>＊</span>公鑰<br><span class='field-hint'>thispk</span></th>";
    echo "  <td class='tleft'><textarea name='thispk' rows='3' form='machineForm' required>" . nl2br(htmlspecialchars($me['thispk'], ENT_QUOTES)) . "</textarea></td>";
    echo "</tr>";
    
    // 提示訊息
    if ($isEdit) {
        echo "<tr>";
        echo "  <td colspan='2' class='tleft tip-text'>";
        echo "    ⓘ 狀態請在列表頁面操作（啟用後將無法編輯）";
        echo "  </td>";
        echo "</tr>";
    } else {
        echo "<tr>";
        echo "  <td colspan='2' class='tleft tip-text'>";
        echo "    ⓘ 新增後預設為「不啟用」，請編輯完成後再手動「啟用」";
        echo "  </td>";
        echo "</tr>";
    }
    
    // 按鈕
    echo "<tr>";
    echo "  <td colspan='2' class='tcenter form-actions'>";
    echo "    <a href='machine_accounts.html?tf=".$mypage."&job=getContact1' class='cancel-btn'>取消</a> ";
    echo "    <input type='submit' value='" . $btnText . "' class='green-btn' form='machineForm'>";
    echo "  </td>";
    echo "</tr>";
    
    echo "</TABLE>";
    echo "</form>";


    if ($myOrgType == '兩者皆有') {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>
        document.addEventListener('DOMContentLoaded', function() {
            var selectEl = document.getElementById('agentTypeSelect');
            var urlRow = document.getElementById('urlRow');
            var urlInput = document.getElementById('machine_url_input');
            
            function toggleMachineType() {
                if (!selectEl) return;
                var selectVal = selectEl.value;
                if (selectVal === 'client') {
                    if(urlRow) urlRow.style.display = 'none';
                    if(urlInput) {
                        urlInput.required = false;
                        urlInput.value = ''; 
                    }
                } else {
                    if(urlRow) urlRow.style.display = '';
                    if(urlInput) urlInput.required = true;
                }
            }

            if(selectEl) {
                selectEl.addEventListener('change', toggleMachineType);
                toggleMachineType(); // 初始化
            }
        });
        </script>";
    }
}
function getContact1($mypage, $typetable) {
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
    
    // 1. 取得使用者的組織ID
    
    $myOrgId = checkUserOrganization();
    
    
    // 2. 組合查詢條件
    
    $mycheck = " WHERE owner_id = '" . addslashes($myOrgId) . "'";
    
    // 搜尋：關鍵字（連接器名稱）
    if (!empty($GLOBALS['keyword'])) {
        $mycheck .= " AND thisname LIKE '%" . addslashes($GLOBALS['keyword']) . "%'";
    }
    
    // 搜尋：狀態
    if (isset($GLOBALS['status']) && $GLOBALS['status'] !== '') {
        $mycheck .= " AND isopen = '" . addslashes($GLOBALS['status']) . "'";
    }
    
    
    // 3. 抓連接器列表
    
    $machineList = share_gettable($typetable . $mycheck . " ORDER BY dateadd DESC");
    
    
    // 4. 顯示畫面
    
    echo "<span class='redtext'>提示：管理您公司底下的連接器</span><br><br>";
    
    
    // 搜尋區塊
    
    echo "<div class='search-area'>";
    echo "<div class='search-left'>";
    
    // 關鍵字搜尋
    echo "<div class='search-item search-keyword'>";
    echo "<label>🔍 關鍵字</label>";
    echo "<input type='text' name='keyword' value='" . ($GLOBALS['keyword'] ?? '') . "' class='input-full' placeholder='搜尋連接器名稱...' form='searchForm'>";
    echo "</div>";
    
    // 狀態篩選
    echo "<div class='search-item'>";
    echo "<label>📊 狀態</label>";
    echo "<select name='status' form='searchForm'>";
    echo "<option value=''>全部</option>";
    echo "<option value='1' ".((($GLOBALS['status'] ?? '') === '1') ? 'selected' : '').">🟢 啟用</option>";
    echo "<option value='0' ".((($GLOBALS['status'] ?? '') === '0') ? 'selected' : '').">⚪ 不啟用</option>";
    echo "</select>";
    echo "</div>";
    
    echo "</div>";
    
    echo "<div class='search-right'>";
    echo "<form action='machine_accounts.html?tf=".$mypage."&job=getContact1' method='post' id='searchForm'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 搜尋</button>";
    echo "</div>";
    echo "</form>";
    echo "<a href='machine_accounts.html?tf=".$mypage."&job=getdetaillist1&typeid=0' class='green-btn'>＋ 新增連接器</a>";
    echo "</div>";
    echo "</div>";
    
    
    // 連接器列表
    
    echo "<div class='table-wrapper'>";
    echo "<TABLE class='formtable'>";
    echo "<tr>";
    echo "<th width='25%'>連接器名稱</th>";
    echo "<th width='15%'>狀態</th>";
    echo "<th width='25%'>建立時間</th>";
    echo "<th width='35%'>操作</th>";
    echo "</tr>";
    
    if (!empty($machineList)) {
        foreach ($machineList as $row) {
            // 狀態顯示
            if ($row['isopen'] == 0) {
                $statusText = "⚪ 不啟用";
            } else {
                $statusText = "🟢 啟用";
            }
            
            echo "<tr>";
            echo "<td class='tleft'>".$row['thisname']."</td>";
            echo "<td>".$statusText."</td>";
            echo "<td>".$row['dateadd']."</td>";
            echo "<td>";
            echo "<div class='btn-group'>";
            
            
            // 不啟用 (isopen = 0)：顯示 編輯、啟用、刪除
            
            if ($row['isopen'] == 0) {
                // 編輯
                echo "<a href='machine_accounts.html?tf=".$mypage."&job=getdetaillist1&typeid=".$row['thisid']."' class='blue-btn btn-sm'>編輯</a> ";
                
                // 啟用
                echo "<a href='machine_accounts.html?tf=".$mypage."&job=toggleMachineStatus&typeid=".$row['thisid']."&status=1' class='green-btn btn-sm'>啟用</a> ";
                
                // 刪除（含確認）
                echo "<a href='#' data-url='machine_accounts.html?tf=".$mypage."&job=deleteMachine&typeid=".$row['thisid']."' data-confirm='確定要刪除此連接器嗎？' class='red-btn'>刪除</a>";
            } else {
                
                // 啟用 (isopen = 1)：顯示 查看（唯讀）
                
                echo "<a href='machine_accounts.html?tf=".$mypage."&job=getdetaillist1&typeid=".$row['thisid']."&mode=view' class='gray-btn btn-sm'>查看</a>";
            }
            
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4' class='tcenter'>目前還沒有連接器，點擊「＋ 新增連接器」開始建立</td></tr>";
    }
    
    echo "</TABLE>";
    echo "</div>";
    echo "<div class='table-footer'>顯示 " . count($machineList) . " 筆</div>";
}
?>
    </div>   
</div>
</div>
</section>
</main>
<?php 
$pdo = null;
require_once 'footer.php';?>  
	 
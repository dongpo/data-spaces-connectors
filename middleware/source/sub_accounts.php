<?php 
session_start();
require_once 'adm/config/config.php';
require_once 'adm/config/getformin.php';
require_once 'adm/config/createimg.php';
require_once 'adm/config/share.php';
require_once 'phpmailer/mailer/sendmail.php'; 
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
$currentPage = 'sub_accounts';
$mypage="sub_accounts";
require_once 'seonew.php';
require_once 'headerlogin.php';
require_once 'adm/config/product.php';
checkPagePermission('sub_accounts');
$responz="";
$typetable="fun_user";

global $imgurl;
 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;	
 }elseif($job=="toggleStatus"){	   
   $tagon=1;
   $tagshow=1;   
   toggleStatus($mypage, $typetable);
 }elseif($job=="getdetaillist1"){	
   $tagon=2;
   $tagshow=2;
 }elseif($job=="uptypedetaillist1"){	
   $tagon=2;
   $tagshow=2;
   uptypedetaillist1($typetable, $mypage);
 }else if($job=="resend_activation" ){
   $tagon=1;
   $tagshow=1;  
   $meid = isset($_GET['meid']) ? intval($_GET['meid']) : 0;
   $thisid = isset($_GET['thisid']) ? intval($_GET['thisid']) : 0;
	if ($meid > 0 && $thisid > 0) {
		$user = share_getinfo("fun_user", "thisid", $meid);
		if (!$user) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('使用者不存在'); history.back();</script>";
            exit;
		
		} elseif ($user['isopen'] == 1) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('該帳號已啟用，無需重發'); history.back();</script>";
            exit;
		
		} else {
			$new_token = bin2hex(random_bytes(32));
			$token_expire = date('Y-m-d H:i:s', strtotime('+7 days'));			
			share_update("fun_user","activation_token='".$new_token."', token_expire='".$token_expire."'","thisid='".$meid."'");			
			//$org_info = share_getinfo($listtable, "thisid", $thisid);
			//$org_name = $org_info ? $org_info['thisname'] : "平台組織";	
			$org_name ="平台組織";
			sendmail("10", $user['email'], array($new_token, $org_name, $user['thisname']));			
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('啟用信已重新寄送'); history.back();</script>";
            exit;
		}
	} else {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('參數錯誤'); history.back();</script>";
        exit;
	}
   
 }else{
   $tagon=1;
   $tagshow=1;	 
 }
 ?>
<main id="main" role="main">
<section aria-label="自然人帳號管理 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="自然人帳號管理 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">自然人帳號管理</h1>
</div>
</section>
<section aria-label="自然人帳號管理" class="detail-page-hero detail-intro-after-query">
<div class="member-container">

    <?php include 'sidebar.php'; ?>
    <div class="content-area">
<div class='contentin'>
  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>
		<a href='sub_accounts.html?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>帳號列表</div>
        </a>
<?php 
if 	($tagon>1 ){
?>	
		<a href='sub_accounts.html?tf=<?=$mypage?>&job=getdetaillist1&thisid=<?=$thisid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'>帳號明細</div>
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
		
		
function uptypedetaillist1($mytable, $mypage) {
    $myerr = "";
    $userId = $_SESSION['companyid'] ?? 0;
    $thisid = $GLOBALS['thisid'] ?? 0;
    $userOrgId = checkUserOrganization();
    $form_type = $GLOBALS['form_type'] ?? 'add';
 /**   
    // 先看接收到的資料
    echo "<script>console.log('=== uptypedetaillist1 開始 ===');</script>";
    echo "<script>console.log('thisname: " . ($GLOBALS["thisname"] ?? '空') . "');</script>";
    echo "<script>console.log('phone: " . ($GLOBALS["phone"] ?? '空') . "');</script>";
    echo "<script>console.log('email: " . ($GLOBALS["email"] ?? '空') . "');</script>";
    echo "<script>console.log('form_type: " . $form_type . "');</script>";
    echo "<script>console.log('userOrgId: " . $userOrgId . "');</script>";
   */ 
   if (empty($thisid)) {
    if (empty($GLOBALS["thisname"])) {
        $myerr .= "<br>帳號為必填項目";
    }
    if (!chkAccount($GLOBALS["thisname"])) {
        $myerr .= "<br>帳號需為8~20碼英文與數字混合，不可使用中文或特殊符號";
    }
   }	
	
    if (empty($GLOBALS["phone"])) {
        $myerr .= "<br>手機號碼為必填項目";
    }
    if (empty($GLOBALS["email"])) {
        $myerr .= "<br>email為必填項目";
    }
    

    if (!chkEmail($GLOBALS["email"])) {
        $myerr .= "<br>Email 格式不正確";
    }
    
    $checkSql = "SELECT thisid FROM " . $mytable . " 
                 WHERE thisname = '" . addslashes($GLOBALS["thisname"]) . "'";
    if ($form_type == 'edit') {
        $checkSql .= " AND thisid != '" . addslashes($thisid) . "'";
    }
    $checkExist = share_getfree($checkSql);
    if (!empty($checkExist)) {
        $myerr .= "<br>此帳號已存在，請使用不同的帳號";
    }
    
    if ($myerr) {
        $myerr = "請確認以下內容：" . $myerr;
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('" . addslashes($myerr) . "'); history.back();</script>";
        return $myerr;
    }
    
    $temp = false;
    if (!empty($thisid)) {
        $temp = share_getinfo($mytable, "thisid", $thisid);
    }
    
    if ($temp) {		
        //  編輯 
        if ($temp['thisentid'] != $userOrgId) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限修改此資料'); history.back();</script>";
            exit;
        }
        
		if ($temp['isopen'] == 1) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('管理者已啟用，不可修改'); history.back();</script>";
            exit;
		} else {
			$sql = "email='".$GLOBALS['email']."',phone='".$GLOBALS['phone']."'";
			share_update("fun_user",$sql,"thisid='".$GLOBALS['thisid']."'");
			
			if ($GLOBALS['email'] != $temp['email']) {
				$new_token = bin2hex(random_bytes(32));
				$token_expire = date('Y-m-d H:i:s', strtotime('+7 days'));
				
				share_update("fun_user","activation_token='".$new_token."',token_expire='".$token_expire."'","thisid='".$GLOBALS['thisid']."'");
			//	$org_info = share_getinfo($mytable, "thisid", $userId);
			//	$org_name = $org_info ? $org_info['thisname'] : "平台組織";
				$org_name ="平台組織"; 
				sendmail("10", $GLOBALS['email'], array($new_token, $org_name, $GLOBALS['uname']));
				echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('更新成功，啟用信已重新寄送至新信箱'); history.back();</script>";
				exit;	
			} else {
				echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('更新成功'); history.back();</script>";
				exit;
			}
		}
        
    } else {
		
		
		$new_token = bin2hex(random_bytes(32));
		$token_expire = date('Y-m-d H:i:s', strtotime('+7 days'));
		
		share_insert("fun_user","thisname,thisentid,pword,email,phone,isopen,creator,issuper,pdate,dateadd,thisenttype,activation_token,token_expire","'".$GLOBALS["thisname"]."','".$userOrgId."','','".$GLOBALS["email"]."','".$GLOBALS["phone"]."',0,'".$userId."',2,now(),now(),1,'".$new_token."','".$token_expire."'"		);
	//	$org_info = share_getinfo($mytable, "thisid",$userId);
	//	$org_name = $org_info ? $org_info['thisname'] : "平台組織";
		$org_name ="平台組織"; 
		sendmail("10", $GLOBALS['email'], array($new_token, $org_name, $GLOBALS['uname']));		
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('新增成功，啟用信已寄出！'); location.href='sub_accounts.html?tf=" . $mypage . "&job=getContact1';</script>";
        exit;
    }
}

function getdetail1($mypage, $typetable) {
    global $imgurl;
    
    $isEdit = ($GLOBALS['thisid'] ?? 0) > 0;
    $title = $isEdit ? "編輯帳號" : "新增帳號";
    $btnText = $isEdit ? "更新" : "建立";
    $me = array(); 
    $myOrgId = checkUserOrganization();
    
    if ($isEdit) {
        $me = checkDataExists($typetable, $GLOBALS['thisid']);
        if (empty($me)) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('資料不存在'); history.back();</script>";
            exit;
        }
        
        if ($me['thisentid'] != $myOrgId) {
            echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('您沒有權限操作此資料'); history.back();</script>";
            exit;
        }
    }
    
    $me['thisname'] = checkval($GLOBALS['thisname'], $me['thisname'] ?? '');
    $me['phone'] = checkval($GLOBALS['phone'], $me['phone'] ?? '');
    $me['email'] = checkval($GLOBALS['email'], $me['email'] ?? '');
    $me['isopen'] = checkval($GLOBALS['isopen'], $me['isopen'] ?? 0);
    
    echo "<span class='fR'><span class='redtext'>＊</span>必填</span>";
    echo "<h2>" . $title . "</h2>";
    echo "<hr>";
    echo "<form action='sub_accounts.html?tf=".$mypage."&job=uptypedetaillist1' method='post' id='subAccountForm'>";
    
    echo "<TABLE class='formtable' width='100%'>";
    
    if ($isEdit) {
        $statusText = ($me['isopen'] == 1) ? "已啟用" : "待啟用";
        
        echo "<tr>";
        echo "  <th class='label-col'>帳號</th>";
        echo "  <td class='tleft'>" . htmlspecialchars($me['thisname']) . "</td>";
        echo "</tr>";
        
        echo "<tr>";
        echo "  <th class='label-col'>狀態</th>";
        echo "  <td class='tleft'>" . $statusText . "</td>";
        echo "</tr>";
    }
    
    if ($isEdit) {

    } else {
        echo "<tr>";
        echo "  <th class='label-col'><span class='redtext'>＊</span>帳號</th>";
        echo "  <td class='tleft'><input type='text' name='thisname' value='" . htmlspecialchars($me['thisname'], ENT_QUOTES) . "' form='subAccountForm' required></td>";
        echo "</tr>";
    }
    
    echo "<tr>";
    echo "  <th class='label-col'><span class='redtext'>＊</span>手機號碼</th>";
    echo "  <td class='tleft'><input type='text' name='phone' value='" . htmlspecialchars($me['phone'], ENT_QUOTES) . "' form='subAccountForm' required></td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "  <th class='label-col'><span class='redtext'>＊</span>email</th>";
    echo "  <td class='tleft'><input type='text' name='email' value='" . htmlspecialchars($me['email'], ENT_QUOTES) . "' form='subAccountForm' required></td>";
    echo "</tr>";
    

    echo "<input type='hidden' name='form_type' value='" . ($isEdit ? 'edit' : 'add') . "'>";
    if ($isEdit) {
        echo "<input type='hidden' name='thisid' value='" . $GLOBALS['thisid'] . "'>";
    }
    
    if ($isEdit && $me['isopen'] == 0) {
        echo "<tr>";
        echo "  <td colspan='2' class='tleft' style='padding:10px 0;'>";
        echo "    <a href='sub_accounts.html?tf=".$mypage."&job=resend_activation&meid=".$me['thisid']."&thisid=".$myOrgId."' class='green-btn'>重發啟用信</a>";
        echo "  </td>";
        echo "</tr>";
    }
    
    echo "<tr>";
    echo "  <td colspan='2' class='tcenter form-actions'>";
    echo "    <a href='sub_accounts.html?tf=".$mypage."&job=getContact1' class='cancel-btn'>取消</a> ";
    echo "    <input type='submit' value='" . $btnText . "' class='green-btn' form='subAccountForm'>";
    echo "  </td>";
    echo "</tr>";
    
    echo "</TABLE>";
    echo "</form>";
}


function getContact1($mypage, $typetable) {
    global $imgurl;
    if (!isset($_SESSION['thisid']) || $_SESSION['thisid'] == 0) {
        echo "<script nonce='" . $_SESSION['nonce'] . "'>alert('請先登入'); location.href='login.html';</script>";
        exit;
    }
    $myOrgId = checkUserOrganization();
    $myThisid = $_SESSION['thisid'];
    $where = " thisentid = '" . addslashes($myOrgId) . "' ";
    $subAccountList = share_gettable($typetable . " WHERE " . $where . " ORDER BY dateadd DESC");    
    echo "<span class='redtext'>提示：管理同組織所有自然人帳號</span><br><br>";
    echo "<div class='search-area'>";
    echo "<div class='search-right' style='width:100%;'>";
    echo "<a href='sub_accounts.html?tf=".$mypage."&job=getdetaillist1' class='green-btn'>＋ 新增帳號</a>";
    echo "</div>";
    echo "</div>";
    echo "<div class='table-wrapper'>";
    echo "<TABLE class='formtable'>";
    echo "<tr>";
    echo "<th width='15%'>帳號</th>";
    echo "<th width='15%'>手機號碼</th>";
    echo "<th width='20%'>EMAIL</th>";
    echo "<th width='10%'>狀態</th>";
    echo "<th width='40%'>操作</th>";
    echo "</tr>";    
    if (!empty($subAccountList)) {
        foreach ($subAccountList as $row) {
            $statusText = ($row['isopen'] == 1) ? "已啟用" : "待啟用";            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['thisname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . $statusText . "</td>";
            echo "<td>";
            echo "<div class='btn-group'>";
			if ($row['isopen'] < 1) {
            echo "<a href='sub_accounts.html?tf=".$mypage."&job=getdetaillist1&thisid=".$row['thisid']."' class='blue-btn btn-sm'>編輯</a> ";
			}
            if ($row['isopen'] == 0) {
                echo "<a href='sub_accounts.html?tf=".$mypage."&job=resend_activation&meid=".$row['thisid']."&thisid=".$myOrgId."' class='green-btn btn-sm'>重發啟用信</a> ";
            }
            echo "</div>";
            echo "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='tcenter'>目前還沒有帳號，點擊「＋ 新增帳號」開始建立</td></tr>";
    }
    echo "</TABLE>";
    echo "</div>";
    echo "<div class='table-footer'>顯示 " . count($subAccountList) . " 筆</div>";
}
?>		
        </div>
      </div>
   </div>		
		
		
	</div>
</div>
</section>
</main>
<?php 
$pdo = null;
require_once 'footer.php';?>  		
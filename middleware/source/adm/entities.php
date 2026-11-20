<?php
$responz="";
global $imgurl;
$listtable="res_entities";    //資料庫list table
$memtable="fun_user";    //資料庫list table
$mypage="entities";			//網頁名
$newname="組織";
	function generateVCToken($thisid) {
		$id6 = str_pad($thisid, 6, '0', STR_PAD_LEFT);
		$timestamp = date('YmdHis');
		$random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
		return "VC-{$id6}-{$timestamp}-{$random}";
	}
if($job=="getContact2"){   
   $tagon=3;
   $tagshow=3;  
 }elseif($job=="uptypelist2"){	
   $tagon=3;
   $tagshow=3; 
   $responz=uptypelist2($listtable);
 }elseif($job=="deltype2"){	
    $tagon=3;
    $tagshow=3;
	$funmember=share_gettable($memtable." where thisentid='".$GLOBALS['thisid']."'");
	if (count($funmember)>0){
		$responz="已有帳號無法刪除";
	}else{
		share_del($listtable." WHERE thisid='".$GLOBALS['thisid']."'");		
		$responz="刪除成功";
//		writeGaiaOperationLog($mytable, $typeid, 0, 'DELETE_SUPPLIER', '刪除供應商：' . $supplier['thisname'], $snapshotData, '刪除供應商：' . $supplier['thisname'] . '（ID：' . $typeid . '）', $_SESSION['admuserid']);
	}
 }elseif($job=="getdetaillist2"){	
   $tagon=4;
   $tagshow=4;
 }else if($job=="uptypedetaillist2" ){
   $tagon=4;
   $tagshow=4;  
   $responz=uptypedetaillist2($listtable); 	
 }elseif($job=="getdetaillist3"){	
   $tagon=5;
   $tagshow=5; 
 }else if($job=="uptypedetaillist3" ){
   $tagon=5;
   $tagshow=5;  
   $responz=uptypedetaillist3($memtable); 
}else if($job=="resend_activation" ){
   $tagon=5;
   $tagshow=5; 
   $meid = isset($_GET['meid']) ? intval($_GET['meid']) : 0;
   $thisid = isset($_GET['thisid']) ? intval($_GET['thisid']) : 0;
	if ($meid > 0 && $thisid > 0) {
		$user = share_getinfo("fun_user", "thisid", $meid);
		if (!$user) {
			$responz = "使用者不存在";
		} elseif ($user['isopen'] == 1) {
			$responz = "該帳號已啟用，無需重發";
		} else {
			$new_token = bin2hex(random_bytes(32));
			$token_expire = date('Y-m-d H:i:s', strtotime('+7 days'));			
			share_update("fun_user","activation_token='".$new_token."', token_expire='".$token_expire."'","thisid='".$meid."'");			
			$org_info = share_getinfo($listtable, "thisid", $thisid);
			$org_name = $org_info ? $org_info['thisname'] : "平台組織";			
			sendmail("10", $user['email'], array($new_token, $org_name, $user['thisname']));			
			$responz = "啟用信已重新寄送";
		}
	} else {
		$responz = "參數錯誤";
	}
}else{
   $tagon=3;
   $tagshow=3;
}

function uptypelist2($mytable){
	global $newname;
	$myerr="";
	if(empty($GLOBALS['thisname'])){
		$myerr.="<br>組織名稱為必填專案";
	}	
	if(empty($GLOBALS['thiscode'])){
		$myerr.="<br>代碼為必填專案";
	}	
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid'])){//已經有了
		
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{				
			share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thiscode='".$GLOBALS['thiscode']."',isopen='".$GLOBALS['isopen']."',thisenttype='".$GLOBALS['thisenttype']."',usertype='".$GLOBALS['usertype']."',lastadmin='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");
			$newRecord = share_getinfo($mytable, "thisid", $GLOBALS['thisid']);
			$changeSummary = [];
			if ($oldData['thisname'] != $GLOBALS['thisname']) {
				$changeSummary[] = "名稱：『" . $oldData['thisname'] . "』→『" . $GLOBALS['thisname'] . "』";
			}
			if ($oldData['thiscode'] != $GLOBALS['thiscode']) {
				$changeSummary[] = "代碼：『" . $oldData['thiscode'] . "』→『" . $GLOBALS['thiscode'] . "』";
			}
			if ($oldData['isopen'] != $GLOBALS['isopen']) {
				$statusText = $GLOBALS['isopen'] == 1 ? "上架" : "下架";
				$changeSummary[] = "狀態變更為：" . $statusText;
			}
			if ($oldData['thisenttype'] != $GLOBALS['thisenttype']) {
				$changeSummary[] = "組織類別變更";
			}
			if ($oldData['usertype'] != $GLOBALS['usertype']) {
				$changeSummary[] = "身份變更";
			}
			$changeSummaryText = !empty($changeSummary) ? implode('；', $changeSummary) : '無變更';
			
			// 寫入操作紀錄
//			if (function_exists('writeGaiaOperationLog')) {
//				writeGaiaOperationLog($mytable, $GLOBALS['thisid'], 0, 'UPDATE_SUPPLIER', '更新供應商：' . $GLOBALS['thisname'], $newRecord, $changeSummaryText, $_SESSION['admuserid']);
//			}
    
			$myerr="更新成功";				
		}			
	}else{
			
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{					
		    share_insert($mytable,"`thisname`,thiscode,thisenttype,usertype,lastadmin,dateadd","'".$GLOBALS['thisname']."','".$GLOBALS['thiscode']."','".$GLOBALS['thisenttype']."','".$GLOBALS['usertype']."','".$_SESSION['admuserid']."',now()");
			$temp = share_getfree("SELECT LAST_INSERT_ID() as myid");
			$news_id = $temp[0]['myid'];
			$vc_token = generateVCToken($news_id);
			share_update($mytable, "vc_token='".$vc_token."'", "thisid=".$news_id);
//			writeGaiaOperationLog($mytable, $news_id, 0, 'CREATE_SUPPLIER', '新增供應商：' . $GLOBALS["thisname"], $newRecord, '新增供應商：' . $GLOBALS["thisname"] . '，代碼：' . $GLOBALS["thiscode"], $_SESSION['admuserid']);		
			$myerr="新增成功";	
		}	
	}		 
	return $myerr;
}
function uptypedetaillist2($mytable){
	$myerr="";
	if(empty($GLOBALS['thisname'])){
		$myerr.="<br>組織名稱為必填專案";
	}	
	if(empty($GLOBALS['thiscode'])){
		$myerr.="<br>代碼為必填專案";
	}		
	if($myerr){
		$myerr="請確認以下內容:".$myerr;
	}else{	
        share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thiscode='".$GLOBALS['thiscode']."',contact_name='".$GLOBALS['contact_name']."',contact_phone='".$GLOBALS['contact_phone']."',lastadmin='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");	
		$newRecord = share_getinfo($mytable, "thisid", $GLOBALS['thisid']);
		$changeSummary = [];
		if ($oldData['thisname'] != $GLOBALS['thisname']) {
			$changeSummary[] = "名稱：『" . $oldData['thisname'] . "』→『" . $GLOBALS['thisname'] . "』";
		}
		if ($oldData['thiscode'] != $GLOBALS['thiscode']) {
			$changeSummary[] = "代碼：『" . $oldData['thiscode'] . "』→『" . $GLOBALS['thiscode'] . "』";
		}
		if ($oldData['contact_name'] != $GLOBALS['contact_name']) {
			$changeSummary[] = "聯絡人：『" . $oldData['contact_name'] . "』→『" . $GLOBALS['contact_name'] . "』";
		}
		if ($oldData['contact_phone'] != $GLOBALS['contact_phone']) {
			$changeSummary[] = "聯絡電話：『" . $oldData['contact_phone'] . "』→『" . $GLOBALS['contact_phone'] . "』";
		}
		$changeSummaryText = !empty($changeSummary) ? implode('；', $changeSummary) : '無變更';
		
		// 寫入操作紀錄
//		if (function_exists('writeGaiaOperationLog')) {
//			writeGaiaOperationLog($mytable, $GLOBALS['thisid'], 0, 'UPDATE_SUPPLIER', '更新供應商：' . $GLOBALS['thisname'], $newRecord, $changeSummaryText, $_SESSION['admuserid']);
//		}
		$myerr="更新成功";
		
	}	
	return $myerr;
}
function uptypedetaillist3($mytable){
	global $newname;
	$myerr="";
	if(empty($GLOBALS["uname"]) or empty($GLOBALS["email"]) or empty($GLOBALS["phone"])){
		$myerr.="<br>帳號、email、手機號碼皆為必填";
	}elseif(!chkAccount($GLOBALS["uname"])){
		$myerr.="<br>帳號需為8~20碼英文與數字混合，不可使用中文或特殊符號";
	}elseif (!empty($GLOBALS["email"]) && !chkEmail($GLOBALS["email"])) {
		$myerr.="<br>Email 格式不正確";
	}
	$mylink = "";
	if (!empty($GLOBALS["meid"])){
		$mylink = " and thisid!='".$GLOBALS['meid']."'";
	}
	
	$cct=share_getcount("fun_user where thisname='".$GLOBALS['uname']."'".$mylink);
	if ($cct>0){		
		$myerr.="<br>帳號已存在";
	}
	
	if($myerr){
		return "請確認以下內容:".$myerr;
	}
	$temp = false;
	if (!empty($GLOBALS['meid'])) {
		$temp = share_getinfo("fun_user", "thisid", $GLOBALS['meid']);
	}
	
	if ($temp) {
		if ($temp['isopen'] == 1) {
			$myerr = "管理者已啟用，不可修改";
		} else {
			$sql = "email='".$GLOBALS['email']."',phone='".$GLOBALS['phone']."'";
			share_update("fun_user",$sql,"thisid='".$GLOBALS['meid']."'");
			
			if ($GLOBALS['email'] != $temp['email']) {
				$new_token = bin2hex(random_bytes(32));
				$token_expire = date('Y-m-d H:i:s', strtotime('+7 days'));
				
				share_update("fun_user","activation_token='".$new_token."',token_expire='".$token_expire."'","thisid='".$GLOBALS['meid']."'");
				$org_info = share_getinfo($mytable, "thisid", $GLOBALS['thisid']);
				$org_name = $org_info ? $org_info['thisname'] : "平台組織";
				sendmail("10", $GLOBALS['email'], array($new_token, $org_name, $GLOBALS['uname']));
				
				$myerr = "更新成功，啟用信已重新寄送至新信箱";
			} else {
				$myerr = "更新成功";
			}
		}
		
	} else {
		$new_token = bin2hex(random_bytes(32));
		$token_expire = date('Y-m-d H:i:s', strtotime('+7 days'));
		
		share_insert("fun_user","thisname,thisentid,pword,email,phone,isopen,creator,issuper,pdate,dateadd,thisenttype,activation_token,token_expire","'".$GLOBALS["uname"]."','".$GLOBALS['thisid']."','','".$GLOBALS["email"]."','".$GLOBALS["phone"]."',0,'".$_SESSION['thisid']."',2,now(),now(),1,'".$new_token."','".$token_expire."'"		);
		$org_info = share_getinfo($mytable, "thisid", $GLOBALS['thisid']);
		$org_name = $org_info ? $org_info['thisname'] : "平台組織";
		sendmail("10", $GLOBALS['email'], array($new_token, $org_name, $GLOBALS['uname']));
		
		$myerr = "新增成功，啟用信已寄出";
	}
	
	return $myerr;
}
?>
	<style  nonce="<?=$_SESSION['nonce']?>">
	.td {
	  border: 1px solid black;
	}		
	.modtable,.modtable tr,.modtable tr td{
		border:1px solid #ccc;
		border-collapse: collapse;
		font-size:0.7vw!important;
		line-height:0.9vw!important;
		height:auto!important;
		padding:0.2vw!important;
	}

	</style>
<script  nonce="<?=$_SESSION['nonce']?>">
	function check_all(obj,cName) 
	{ 
		var checkboxs = document.getElementsByName(cName); 
		for(var i=0;i<checkboxs.length;i++){checkboxs[i].checked = obj.checked;} 
	} 	
	
$(document).ready(function() {
	$('body').on('focus',".cleartext", function(){
       $(this).val('');
	});	
	//錯誤顯示
  err='<?=$responz?>';
  if(err){
      $(".error").html(err);
      $(".error").fadeIn();
  }
});
</script>

<div class='bkhead'>
    <h1><?php echo $newname;?>管理</h1>
    <div class='bknotice'>

    </div>
</div>
<div class='contentin'>
  <div class='error'>
  </div>
  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>	
<?php
if 	($tagon>2){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getContact2&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==3?"on":"")?> ' >
          <div class='tagin'><?php echo $newname;?>列表</div>
        </a>
<?php 
}
if 	($tagon>3){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getdetaillist2&thisid=<?=$thisid?>' class='tagitem  <?=($tagon==4?"on":"")?> ' >
          <div class='tagin'><?php echo $newname;?>內容明細</div>
        </a>	
<?php 
}
if 	($tagon>4){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getdetaillist3&thisid=<?=$thisid?>' class='tagitem  <?=($tagon==5?"on":"")?> ' >
          <div class='tagin'>管理者明細</div>
        </a>			
<?php 
}
?>		
     </div>
      <div class='tagbody'>
        <div class='tagbodyin'>
<?php
if($tagshow==3){		  
	getContact2($mypage,$listtable,$memtable);	
}elseif($tagshow==4){
	getdetail2($mypage,$listtable);
}elseif($tagshow==5){
	getdetail3($mypage,$listtable,$memtable);	
}
?>		
        </div>
      </div>
   </div>
<?php
function getContact2($mypage,$mytable,$memtable){
	
	 $myarr = array();
	 $a=0;	 
	 $temp=share_gettable("fun_enttype");
	 foreach($temp as $row){
        $myarr[$a]['id']=$row['thisid'];
        $myarr[$a]['name']=$row['thisname'];	
		$a++;
     }
	 
	 $myarr1 = array();
	 $a=0;	 
	 $temp=share_gettable("gaia_user_roles");
	 foreach($temp as $row){
        $myarr1[$a]['id']=$row['thisid'];
        $myarr1[$a]['name']=$row['thisname'];	
		$a++;
     }	 
	 
	global $newname;
	$mycheck=" where thisid>0";	
	echo "<font color=red>新增資料:<br>1.先新增".$newname."名稱<br>2.點選該筆『內容編輯』，新增相關明細資料<br>3.組織需建立管理者，無管理者則會紅字『新增』鍵，有管理者則會有『管理』鍵<br>";	
	echo "<span class='fR' style='margin:10px 10px 0 0;font-size:15px;'><span class='redtext'>＊</span>必填</span>";	
	echo "<TABLE class=formtable width=100%  style='margin-top: 0px;    margin-bottom: 0px;'>";
echo "<tr><th width='8%'>刪除</th><th width='17%'><span class='redtext'>＊</span>".$newname."名稱</th><th width='15%'><span class='redtext'>＊</span>代碼</th><th width='10%'>類別</th><th width='10%'>身份</th><th width='10%'>上架狀態</th><th width='10%'>更新</th><th width='10%'>內容編輯</th><th width='10%'>管理者</th></tr>";
	echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
	echo "<td>新增</th>";
	echo "<td><input type=text name=thisname value='' form='listform'></TD>";	
	echo "<td><input type=text name=thiscode value='' form='listform'></TD>";
	 echo "<td><select name=thisenttype form='listform'>";
	 for ($i=0;$i<sizeof($myarr);$i++){
		echo "<option value='".$myarr[$i]['id']."'>".$myarr[$i]['name']."</option>";
 	 }
	 echo "</select></td>";
	 echo "<td><select name=usertype form='listform'>";
	 for ($i=0;$i<sizeof($myarr1);$i++){
		echo "<option value='".$myarr1[$i]['id']."'>".$myarr1[$i]['name']."</option>";
 	 }
	 echo "</select></td>"; 
	echo "<td></td>";
	echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";
	echo "<td></td>";	
	echo "<td></td>";	
	echo "</tr>";			
	$typelist=share_gettable($mytable.$mycheck."  ORDER BY thisid");
	$formid=0;
	foreach($typelist as $type){
		$formid++;
		$funmember=share_gettable($memtable." where thisentid='".$type['thisid']."'");
		echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2&thisid=".$type['thisid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
		if (count($funmember)>0){
			echo "<td></td>";
		}else{
			echo "<td><a href='index.php?tf=".$mypage."&job=deltype2&thisid=".$type['thisid']."'  class='blue-btn delbtn'>刪除</a></td>";
		}
		echo "<td><input type=text name=thisname value='".$type['thisname']."' form='itemform".$formid."'></TD>";	
		echo "<td><input type=text name=thiscode value='".$type['thiscode']."' form='itemform".$formid."'></TD>";
		echo "<td><select name=thisenttype form='itemform".$formid."'>";
		for ($i=0;$i<sizeof($myarr);$i++){
			if($type['thisenttype']==$myarr[$i]['id']){
				echo "<option value='".$myarr[$i]['id']."' selected>".$myarr[$i]['name']."</option>";						
			}else{
				echo "<option value='".$myarr[$i]['id']."'>".$myarr[$i]['name']."</option>";
			}
		}
		echo "</select></td>";		
		echo "<td><select name=usertype form='itemform".$formid."'>";
		for ($i=0;$i<sizeof($myarr1);$i++){
			if($type['usertype']==$myarr1[$i]['id']){
				echo "<option value='".$myarr1[$i]['id']."' selected>".$myarr1[$i]['name']."</option>";						
			}else{
				echo "<option value='".$myarr1[$i]['id']."'>".$myarr1[$i]['name']."</option>";
			}
		}
		echo "</select></td>";			
		
		echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
		echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
		echo "<td><a href='index.php?tf=".$mypage."&job=getdetaillist2&thisid=".$type['thisid']."'  class='gray-btn'>編輯</a></TD>";
		echo "<td><a href='index.php?tf=".$mypage."&job=getdetaillist3&thisid=".$type['thisid']."'  class='gray-btn'>";
		
		if (count($funmember)>0){
			echo "管理";
		}else{
			echo "<font color=red>新增</font>";
		}
		echo"</a></TD>";		
	}
	echo "</table>";	
}
function getdetail2($mypage,$mytable){
	global $newname;
	$me=share_getinfo($mytable,"thisid",$GLOBALS['thisid']);	
	$me['thisname']=checkval($GLOBALS['thisname'],$me['thisname']);
	$me['thiscode']=checkval($GLOBALS['thiscode'],$me['thiscode']);
	$me['contact_name']=checkval($GLOBALS['contact_name'],$me['contact_name']);		
	$me['contact_phone']=checkval($GLOBALS['contact_phone'],$me['contact_phone']);
	echo "<span class='fR' style='margin:10px 10px 0 0;font-size:15px;'><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist2&thisid=".$GLOBALS['thisid']."' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100%  style='margin-top: 0px;    margin-bottom: 0px;'>";
	echo "<tr><th colspan=2>".$me['thisname']." 相關資料管理 </th></TR>";
	echo "<tr><th width=15% ><span class='redtext'>＊</span>".$newname."名稱</th><TD width=85% class='tleft'><input type=text name='thisname' value='".$me['thisname']."' form='listform'></TD></tr>";
	echo "<tr><th width=15% ><span class='redtext'>＊</span>代碼</th><TD width=85% class='tleft'><input type=text name='thiscode' value='".$me['thiscode']."' form='listform'></TD></tr>";
	echo "<tr><th width=15% >聯絡人</th><TD width=85% class='tleft'><input type=text name='contact_name' value='".$me['contact_name']."' form='listform'></TD></tr>";
	echo "<tr><th width=15% >連絡電話</th><TD width=85% class='tleft'><input type=text name='contact_phone' value='".$me['contact_phone']."' form='listform'></TD></tr>";
	echo "<TR><TH></TH><TD><input type=submit class='gray-btn' value='更新' form='listform'>點選「更新」儲存上方資料</TD></TR>";
	echo "</TABLE>";	
}		
function getdetail3($mypage,$mytable,$memtable){
	
	$mycheck="";
	$me1=share_getinfo($mytable,"thisid",$GLOBALS['thisid']);
	$funmember=share_gettable($memtable." where thisentid='".$GLOBALS['thisid']."' order by thisid");
	if (count($funmember)>0){
		$me=$funmember[0];
		$mycheck="&meid=".$me['thisid'];
	}
	$me['thisname']=checkval($GLOBALS['uname'],$me['thisname']);
	$me['email']=checkval($GLOBALS['email'],$me['email']);		
	$me['phone']=checkval($GLOBALS['phone'],$me['phone']);
	$has_admin = ($me['thisid'] > 0);
	$is_active = ($me['isopen'] == 1);
	$op_msg = "";
	if (!$has_admin) {
		$op_msg = "此組織尚無管理者，請填寫下方資料後點選「建立」，系統將寄發啟用信給該管理者。";
	} elseif (!$is_active) {
		$op_msg = "管理者帳號已建立，尚未完成啟用。可修改聯絡資料後點「更新」，或點「重發啟用信」重新寄送。";
	} else {
		$op_msg = "管理者已啟用，聯絡資料由本人自行管理，營運人員僅能檢視。";
	}
	
	echo "<span class='fR' style='margin:10px 10px 0 0;font-size:15px;'><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist3&thisid=".$GLOBALS['thisid'].$mycheck."' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100% style='margin-top: 0px; margin-bottom: 0px;'>";
	
	if ($has_admin) {
		echo "<tr><th colspan=2>".$me1['thisname']." 管理者明細</th></TR>";
	} else {
		echo "<tr><th colspan=2>".$me1['thisname']." 建立管理者</th></TR>";
	}	
	echo "<tr><td colspan=2 style='padding:8px 15px;background:#f5f5f5;color:#555;font-size:14px;border-bottom:1px solid #ddd;'>".$op_msg."</td></tr>";
	echo "<tr><th width=15%><span class='redtext'>＊</span>帳號</th>";
	echo "<TD width=85% class='tleft'>";
	if ($has_admin) {
		echo "<span style='display:inline-block;padding:5px 10px;background:#f0f0f0;border-radius:4px;'>".$me['thisname']."</span>";
		echo "<input type='hidden' name='uname' value='".$me['thisname']."' form='listform'>";
	} else {
		echo "<input type=text name='uname' value='' form='listform'><br>帳號需為8~20碼英文與數字混合，不可使用中文或特殊符號";
	}
	echo "</TD></tr>";
	echo "<tr><th width=15%><span class='redtext'>＊</span>email</th>";
	echo "<TD width=85% class='tleft'>";
	if ($has_admin && $is_active) {
		echo "<span style='display:inline-block;padding:5px 10px;background:#f0f0f0;border-radius:4px;'>".$me['email']."</span>";
		echo "<input type='hidden' name='email' value='".$me['email']."' form='listform'>";
	} else {
		echo "<input type=text name='email' value='".$me['email']."' form='listform'>";
	}
	echo "</TD></tr>";
	echo "<tr><th width=15%><span class='redtext'>＊</span>手機號碼</th>";
	echo "<TD width=85% class='tleft'>";
	if ($has_admin && $is_active) {
		echo "<span style='display:inline-block;padding:5px 10px;background:#f0f0f0;border-radius:4px;'>".$me['phone']."</span>";
		echo "<input type='hidden' name='phone' value='".$me['phone']."' form='listform'>";
	} else {
		echo "<input type=text name='phone' value='".$me['phone']."' form='listform'>";
	}
	echo "</TD></tr>";
	echo "<tr><th width=15%>啟用狀態</th>";
	echo "<TD width=85% class='tleft'>";
	if (!$has_admin) {
		echo "<span style='color:gray;'>尚未建立</span>";
	} elseif ($is_active) {
		echo "<span style='color:green;font-weight:bold;'>已啟用</span>";
	} else {
		$expired = false;
		if (!empty($me['token_expire']) && strtotime($me['token_expire']) < time()) {
			$expired = true;
		}
		if ($expired) {
			echo "<span style='color:#e67e22;font-weight:bold;'>待啟用（連結已過期）</span>";
		} else {
			echo "<span style='color:#e67e22;font-weight:bold;'>待啟用</span>";
		}
	}
	echo "</TD></tr>";
	echo "<TR><TH></TH><TD>";	
	if (!$has_admin) {
		echo "<input type=submit class='gray-btn' value='建立' form='listform'> ";
		echo "點選「建立」將寄發啟用信給此管理者";
	} elseif (!$is_active) {
		echo "<input type=submit class='gray-btn' value='更新' form='listform'> ";
		echo "點選「更新」儲存上方資料";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		echo "<a href='index.php?tf=".$mypage."&job=resend_activation&meid=".$me['thisid']."&thisid=".$GLOBALS['thisid']."' class='gray-btn' style='display:inline-block;padding:4px 12px;background:#f0f0f0;border:1px solid #ccc;border-radius:4px;text-decoration:none;color:#333;font-size:14px;'>重發啟用信</a>";
	} else {
		echo "<span style='color:gray;'>管理者已啟用，無需操作</span>";
	}
	
	echo "</TD></TR>";
	echo "</TABLE>";
}
?>
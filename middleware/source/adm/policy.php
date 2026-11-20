<?php
$responz=""; 
global $imgurl;
$listtable="gaia_policy_template";    //資料庫list table
$mypage="policy";			//網頁名
$newname="政策範本";
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
	share_del($listtable." WHERE thisid='".$GLOBALS['thisid']."'");		
	$responz="刪除成功";
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
	if(empty($GLOBALS['policy_json'])){
		$myerr.="<br>JOSN為必填專案";
	}	
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid'])){//已經有了
		
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{				
		
			share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',policy_json='".$GLOBALS['policy_json']."',isopen='".$GLOBALS['isopen']."',lastadmin='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");
			$myerr="更新成功";				
		}			
	}else{
			
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{					
		    share_insert($mytable,"`thisname`,policy_json,lastadmin,dateadd","'".$GLOBALS['thisname']."','".$GLOBALS['policy_json']."','".$_SESSION['admuserid']."',now()");	
			$myerr="新增成功";	
		}	
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

		<a href='index.php?tf=<?=$mypage?>&job=getContact2&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==3?"on":"")?> ' >
          <div class='tagin'><?php echo $newname;?>列表</div>
        </a>

	
     </div>
      <div class='tagbody'>
        <div class='tagbodyin'>
<?php
if($tagshow==3){		  
	getContact2($mypage,$listtable,$memtable);		
}
?>		
        </div>
      </div>
   </div>
<?php
function getContact2($mypage,$mytable,$memtable){
	global $newname;
	$mycheck=" where thisid>0";	
echo "<font color='red'>📌 操作說明：<br>";
echo "1. 輸入政策範本名稱（如：公家機關、學術機構、一般使用者）<br>";
echo "2. JSON 欄位請輸入完整的政策內容，包含以下欄位：<br>";

echo "&nbsp;&nbsp;&nbsp;• allowed_actions：可做什麼（陣列）<br>";
echo "&nbsp;&nbsp;&nbsp;• prohibited_actions：不可做什麼（陣列）<br>";
echo "&nbsp;&nbsp;&nbsp;• constraints：限制條件（物件）<br>";
echo "&nbsp;&nbsp;&nbsp;• data_retention_rule：保存規範<br>";
echo "&nbsp;&nbsp;&nbsp;• data_sensitivity_level：敏感等級（0-4）<br>";
;
echo "3. 上架後即可在前台版本編輯時選擇使用</font><br>";
	echo "<span class='fR' style='margin:10px 10px 0 0;font-size:15px;'><span class='redtext'>＊</span>必填</span>";	
	echo "<TABLE class=formtable width=100%  style='margin-top: 0px;    margin-bottom: 0px;'>";
	echo "<tr><th width=10% >刪除</th><Th width=25%><span class='redtext'>＊</span>".$newname."名稱</TH><th width=45% ><span class='redtext'>＊</span>JOSN</th><TH width=10%>上架狀態</TH><TH width=10%>更新</TH></tr>";	
	echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
	echo "<td>新增</th>";
	echo "<td><input type=text name=thisname value='' form='listform'></TD>";	
	echo "<td><textarea name='policy_json' rows='3' form='listform' style='width:95%;font-size:12px;font-family:monospace;' placeholder='請輸入 JSON 格式...'></textarea></td>";
	echo "<td></td>";
	echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";
	echo "</tr>";			
	$typelist=share_gettable($mytable.$mycheck."  ORDER BY thisid");
	$formid=0;
	foreach($typelist as $type){
		$formid++;
		echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2&thisid=".$type['thisid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
		echo "<td><a href='index.php?tf=".$mypage."&job=deltype2&thisid=".$type['thisid']."'  class='blue-btn delbtn'>刪除</a></td>";
		echo "<td><input type=text name=thisname value='".$type['thisname']."' form='itemform".$formid."'></TD>";	
		echo "<td><textarea name='policy_json' rows='3' form='itemform".$formid."' style='width:95%;font-size:12px;font-family:monospace;' placeholder='請輸入 JSON 格式...' >".$type['policy_json']."</textarea></td>";		
		
		echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
		echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
	}
	echo "</table>";	
}

?>
<?php
$responz="";
$newname="允許行為";
global $imgurl;
$listtable="gaia_allowed_actions";    //資料庫list table
$mypage="allowed";			//網頁名

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
 }else if($job=="listsort"){
 	sorting($listtable,$GLOBALS['id']);
    $tagon=3;
    $tagshow=3; 
 }else{
	$tagon=3;
	$tagshow=3;
 }   
?>
<style   nonce="<?=$_SESSION['nonce']?>">
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
.nselectbox {
    display: inline-block;
    padding: 3px 12px;
    margin-right: 8px;
    font-size: 14px;
    line-height: 24px;
    color: #fff;
    background: #428bca;
    -webkit-border-radius: 15px;
    -moz-border-radius: 15px;
    border-radius: 15px;
}
	</style>
<script   nonce="<?=$_SESSION['nonce']?>">
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
		<a href='index.php?tf=<?=$mypage?>&job=getContact2' class='tagitem  <?=($tagon==3?"on":"")?> ' >
          <div class='tagin'><?php echo $newname;?>列表</div>
        </a>
     </div>
      <div class='tagbody'>
        <div class='tagbodyin'>
<?php
		getContact2($mypage,$listtable);	
?>		
        </div>
      </div>
   </div>
 <?php
function sorting($mtable,$id){
	$row=share_gettable($mtable." ORDER BY sorting DESC limit ".($GLOBALS["myorder"]-1).",1");
	$neworder=$row[0]['sorting'];
	if($GLOBALS["thisorder"]>$neworder){
		share_update($mtable,"sorting=sorting+1", "sorting<".$GLOBALS["thisorder"]." AND sorting>=".$neworder);
	}else{
		share_update($mtable,"sorting=sorting-1", "sorting>".$GLOBALS["thisorder"]." AND sorting<=".$neworder);
	}
	share_update($mtable,"sorting=".$neworder, "thisid=".$id);	
}
function getContact2($mypage,$mytable){
	$mycheck=" where thisid>0 ";
	echo "<span class='fR' style='margin:10px 10px 0 0;font-size:15px;'><span class='redtext'>＊</span>必填</span>";	
	echo "<TABLE class=formtable width=100%  style='margin-top: 0px;    margin-bottom: 0px;'>";
	echo "<tr><th width=10% >刪除</th><Th width=30%><span class='redtext'>＊</span>行為名稱</TH><th width=30% ><span class='redtext'>＊</span>行為代碼</th><th width=10% >上架狀態</th><TH width=10%>更新</TH><TH width=10%>排序</TH></tr>";	
	echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
	echo "<td>新增</th>";
	echo "<td><input type=text name=thisname value='' form='listform'></TD>";	
	echo "<td><input type=text name=thiscode value='' form='listform'></TD>";	
	echo "<td></td>";	
	echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";
		
	echo "<td></td>";
	echo "</tr>";			

	$typelist=share_gettable($mytable.$mycheck."  ORDER BY 	sorting DESC");
	$formid=1;
	$lcnt=1;
	foreach($typelist as $type){
		echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2&thisid=".$type['thisid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
		echo "<td><a href='index.php?tf=".$mypage."&job=deltype2&thisid=".$type['thisid']."'  class='blue-btn delbtn'>刪除</a></td>";
		echo "<td><input type=text name=thisname value='".$type['thisname']."' form='itemform".$formid."'></TD>";	
		echo "<td><input type=text name=thiscode value='".$type['thiscode']."' form='itemform".$formid."'></TD>";
		echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
		echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
		if (!empty($GLOBALS['thisnamelist']))
		{
			echo "<td></td>";			
		}else{
			sortingbox("listsort",count($typelist),$type['thisid'],$type['sorting'],$lcnt);
		}			
		echo "</tr>";
		$formid++;
		$lcnt++;
	}
	echo "</table>";	

} 		
function uptypelist2($mytable){
	global $newname;
	$myerr="";
	if(empty($GLOBALS['thisname'])){
		$myerr.="<br>行為名稱為必填專案";
	}	
	if(empty($GLOBALS['thiscode'])){
		$myerr.="<br>行為代碼為必填專案";
	}	
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid'])){//已經有了
		$row=share_getinfo($mytable,"thiscode",$GLOBALS["thiscode"]);
		if($row){
			if ($row['thisid']==$GLOBALS['thisid']){
				
			}else{
				$myerr.="<br>行為代碼已存在";
			}
		}	
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{

			share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thiscode='".$GLOBALS['thiscode']."',isopen='".$GLOBALS['isopen']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");		
			$myerr="更新成功";	 			
		}			
	}else{
		$row=share_getinfo($mytable,"thiscode",$GLOBALS["thiscode"]);
		if($row){
			$myerr.="<br>行為代碼已存在";
		}		
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{
			$row=share_gettable($mytable." order by sorting desc limit 1");
			if(is_array($row)){
				$myorders=$row[0]["sorting"]+1;
			}else{
				$myorders=1;
			}
			share_insert($mytable,"`thisname`,thiscode,sorting,dateadd,lastadmin","'".$GLOBALS['thisname']."','".$GLOBALS['thiscode']."','".$myorders."',now(),'".$_SESSION['admuserid']."'");	
			$myerr="新增成功";	
		}	
	}		 
	return $myerr;
}

?>		
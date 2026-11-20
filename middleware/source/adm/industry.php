<?php 
$responz="";
$newname="產業分類表";
global $imgurl;
$listtable="gaia_industry";   
$listtable1="gaia_schema";    
$listtable2="gaia_schema_version";    
$mypage="industry";			//網頁名

 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;  
 }elseif($job=="uptypelist1"){	
   $tagon=1;
   $tagshow=1;  
   $responz=uptypelist1($listtable);   
  }elseif($job=="deltype1"){	
	$tagon=1;
	$tagshow=1;	
	$row = share_getinfo($listtable, "thisid", $GLOBALS['thisid']);
	if($row && $row['isopen']==1){
		echo "<script nonce='".$_SESSION['nonce']."'>alert('此產業為上架中不能刪除'); window.location='index.php?tf=".$GLOBALS['tf']."&job=getContact1';</script>";
		exit;
	}	
	share_del($listtable." WHERE thisid='".$GLOBALS['thisid']."'");		
	$responz="刪除成功";
 }elseif($job=="getContact2"){
   $tagon=2;
   $tagshow=2;  
 }elseif($job=="uptypelist2"){	
   $tagon=2;
   $tagshow=2;  
   $responz=uptypelist2($listtable1);  	
  }elseif($job=="deltype2"){	
	$tagon=2;
	$tagshow=2;	
	$row = share_getinfo($listtable1, "thisid", $GLOBALS['thisid']);
	if($row && $row['isopen']==1){
		echo "<script  nonce='".$_SESSION['nonce']."'>alert('此Schema名稱為上架中不能刪除'); window.location='index.php?tf=".$GLOBALS['tf']."&job=getContact2&typeid=".$GLOBALS['typeid']."';</script>";
		exit;
	}	
	share_del($listtable1." WHERE thisid='".$GLOBALS['thisid']."'");		
	$responz="刪除成功";   
 }elseif($job=="getContact3"){
   $tagon=3;
   $tagshow=3;  
 }elseif($job=="uptypelist3"){	
   $tagon=3;
   $tagshow=3;  
   $responz=uptypelist3($listtable2);  	
  }elseif($job=="deltype3"){	
	$tagon=3;
	$tagshow=3;	
	$row = share_getinfo($listtable2, "thisid", $GLOBALS['thisid']);
	if($row && $row['isopen']==1){
		echo "<script  nonce='".$_SESSION['nonce']."'>alert('此Schema名稱為上架中不能刪除'); window.location='index.php?tf=".$GLOBALS['tf']."&job=getContact3&typeid=".$GLOBALS['typeid']."&schemaid=".$GLOBALS['schemaid']."';</script>";
		exit;
	}	
	share_del($listtable2." WHERE thisid='".$GLOBALS['thisid']."'");		
	$responz="刪除成功";
   
   
	
 }else{
   $tagon=1;
   $tagshow=1;  	 
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
    <h1>產業管理</h1>
    <div class='bknotice'>

    </div>
</div>
<div class='contentin'>
  <div class='error'>
  </div>
  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>	
		<a href='index.php?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>產業分類設定</div>
        </a>
<?php
if 	($tagon>1){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getContact2&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'> Schema名稱列表</div>
        </a>
<?php
}
if 	($tagon>2){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getContact3&typeid=<?=$typeid?>&schemaid=<?=$schemaid?>' class='tagitem  <?=($tagon==3?"on":"")?> ' >
          <div class='tagin'> Schema版本列表</div>
        </a>
<?php 
}
?>		
		
     </div>
      <div class='tagbody'>
        <div class='tagbodyin'>
<?php
		if($tagshow==1){
			getContact1($mypage,$listtable);
        }else if($tagshow==2){
			getContact2($mypage,$listtable1,$listtable);
        }else if($tagshow==3){
			getContact3($mypage,$listtable2,$listtable1,$listtable);
		}
?>		
        </div>
      </div>
   </div>
 <?php
function getContact1($mypage,$mytable){
    $mycheck=" where thisid>0 ";
	echo "<div style='background:#f8f9fa;padding:12px 16px;border-radius:4px;border:1px solid #ddd;margin-bottom:12px;font-size:14px;'>";
	echo "<span style='color:#d32f2f;font-weight:bold;'>架構：</span>產業分類（第1層）→ Schema名稱（第2層）→ Schema版本（第3層）<br>";
	echo "第1層上架後前台才看得到，但要有第2、3層的資料，前台才會有內容可選。<br>";
	echo "建議先把三層都建好，最後再將第1層設為上架。";
	echo "</div>";
    echo "<span class='fR' style='margin:10px 10px 0 0;font-size:14px;'><span class='redtext'>＊</span>必填</span>";
    echo "<TABLE class='formtable' width='100%' style='margin-top:0;margin-bottom:0;'>";
    echo "<tr>";
    echo "<th width='8%'>刪除</th>";
    echo "<th width='22%'><span class='redtext'>＊</span>產業名稱</th>";
    echo "<th width='12%'><span class='redtext'>＊</span>產業代碼</th>";
    echo "<th width='12%'>上架狀態</th>";
    echo "<th width='8%'>更新</th>";
    echo "<th width='10%'>Schema管理</th>";
    echo "</tr>";	
    
    // 新增列
    echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist1' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
    echo "<td>新增</td>";
    echo "<td><input type=text name=thisname value='' form='listform'></TD>";	
    echo "<td><input type=text name=thiscode value='' form='listform'></TD>";
    echo "<td></td>";	
    echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";		
    echo "<td></td>";
    echo "</tr>";			
    
    $typelist=share_gettable($mytable.$mycheck."  ORDER BY thisid");
    $formid=1;
    foreach($typelist as $type){
        echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist1&thisid=".$type['thisid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
        if($type['isopen']==1){
            echo "<td>--</td>";
        }else{
            echo "<td><a href='index.php?tf=".$mypage."&job=deltype1&thisid=".$type['thisid']."' class='blue-btn delbtn'>刪除</a></td>";
        }
		
        echo "<td><input type=text name=thisname value='".$type['thisname']."' form='itemform".$formid."'></TD>";	
        echo "<td><input type=text name=thiscode value='".$type['thiscode']."' form='itemform".$formid."'></TD>";
        if($type['isopen']==1){
            echo "<td>";
            echo "<span style='display:inline-block;padding:4px 12px;background:#4CAF50;color:#fff;border-radius:3px;font-size:13px;'>上架中</span>";
            echo "<input type='hidden' name='isopen' value='1' form='itemform".$formid."'>";
            echo "</td>";
        }else{
            echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
        }
        
        echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
        echo "<td><a href='index.php?tf=".$mypage."&job=getContact2&typeid=".$type['thisid']."' class='gray-btn'>Schema 管理</a></td>";	
        echo "</tr>";
        $formid++;
    }
    echo "</table>";	
}
function uptypelist1($mytable){
	global $newname;
	$myerr="";
	if(empty($GLOBALS['thisname'])){
		$myerr.="<br>產業名稱為必填專案";
	}	
	if(empty($GLOBALS['thiscode'])){
		$myerr.="<br>產業代碼為必填專案";
	}	
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid'])){//已經有了
		$row=share_getinfo($mytable,"thiscode",$GLOBALS["thiscode"]);
		if($row){
			if ($row['thisid']==$GLOBALS['thisid']){
				
			}else{
				$myerr.="<br>產業代碼已存在";
			}
		}	
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{
			$schema_json = $GLOBALS['schema_json'];
			share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thiscode='".$GLOBALS['thiscode']."',isopen='".$GLOBALS['isopen']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");		
			$myerr="更新成功";				
		}			
	}else{
		$row=share_getinfo($mytable,"thiscode",$GLOBALS["thiscode"]);
		if($row){
			$myerr.="<br>產業代碼已存在";
		}		
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{
			share_insert($mytable,"thisname, thiscode,isopen, dateadd, lastadmin","'" . $GLOBALS['thisname'] . "','" . $GLOBALS['thiscode'] . "',0,NOW(),'" . $_SESSION['admuserid'] . "'");
			$myerr="新增成功";	
		}	
	}		 
	return $myerr;
}

function getContact2($mypage,$mytable,$mytable1){
    $mycheck=" where thisid>0 and industry_id='".$GLOBALS['typeid']."' ";
	echo "<div style='background:#f8f9fa;padding:12px 16px;border-radius:4px;border:1px solid #ddd;margin-bottom:12px;font-size:14px;'>";
	echo "<span style='color:#d32f2f;font-weight:bold;'>架構：</span>Schema名稱（第2層）→ Schema版本（第3層）<br>";
	echo "第2層上架後前台才看得到，但要有第3層的資料，前台才會有內容可選。<br>";
	echo "建議先把第3層建好，最後再將第2層設為上架。";
	echo "</div>";
    echo "<span class='fR' style='margin:10px 10px 0 0;font-size:14px;'><span class='redtext'>＊</span>必填</span>";
    echo "<TABLE class='formtable' width='100%' style='margin-top:0;margin-bottom:0;'>";
	if ($GLOBALS['typeid']>0){
		$me=share_getinfo($mytable1,"thisid",$GLOBALS['typeid']);
		echo "<tr><th colspan=10>分類 ".$me['thisname']." 管理 </th></TR>";
	}
    echo "<tr>";
    echo "<th width='8%'>刪除</th>";
    echo "<th width='22%'><span class='redtext'>＊</span>Schema名稱</th>";
    echo "<th width='12%'><span class='redtext'>＊</span>Schema代碼</th>";
    echo "<th width='12%'>上架狀態</th>";
    echo "<th width='8%'>更新</th>";
    echo "<th width='10%'>Schema管理</th>";
    echo "</tr>";	
    
    // 新增列
    echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2&typeid=".$GLOBALS['typeid']."' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
    echo "<td>新增</td>";
    echo "<td><input type=text name=schemaname value='' form='listform'></TD>";	
    echo "<td><input type=text name=schemacode value='' form='listform'></TD>";
    echo "<td></td>";	
    echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";		
    echo "<td></td>";
    echo "</tr>";			
    
    $typelist=share_gettable($mytable.$mycheck."  ORDER BY thisid");
    $formid=1;
    foreach($typelist as $type){
        echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2&typeid=".$GLOBALS['typeid']."&thisid=".$type['thisid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
        if($type['isopen']==1){
            echo "<td>--</td>";
        }else{
            echo "<td><a href='index.php?tf=".$mypage."&job=deltype2&typeid=".$GLOBALS['typeid']."&thisid=".$type['thisid']."' class='blue-btn delbtn'>刪除</a></td>";
        }
		
        echo "<td><input type=text name=schemaname value='".$type['schemaname']."' form='itemform".$formid."'></TD>";	
        echo "<td><input type=text name=schemacode value='".$type['schemacode']."' form='itemform".$formid."'></TD>";
        if($type['isopen']==1){
            echo "<td>";
            echo "<span style='display:inline-block;padding:4px 12px;background:#4CAF50;color:#fff;border-radius:3px;font-size:13px;'>上架中</span>";
            echo "<input type='hidden' name='isopen' value='1' form='itemform".$formid."'>";
            echo "</td>";
        }else{
            echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
        }
        
        echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
        echo "<td><a href='index.php?tf=".$mypage."&job=getContact3&typeid=".$GLOBALS['typeid']."&schemaid=".$type['thisid']."' class='gray-btn'>Schema版本管理</a></td>";	
        echo "</tr>";
        $formid++;
    }
    echo "</table>";	
}


function uptypelist2($mytable){
	$myerr="";
	if(empty($GLOBALS['schemaname'])){
		$myerr.="<br>Schema名稱為必填專案";
	}	
	if(empty($GLOBALS['schemacode'])){
		$myerr.="<br>Schema代碼為必填專案";
	}	
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid'])){
        $check = share_gettable($mytable." WHERE industry_id='".$GLOBALS['typeid']."' AND schemacode='".$GLOBALS['schemacode']."' AND thisid!='".$GLOBALS['thisid']."'");
        if(count($check) > 0){
            return "Schema代碼已存在";
        }
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{
			share_update($mytable,"`schemaname`='".$GLOBALS['schemaname']."',schemacode='".$GLOBALS['schemacode']."',isopen='".$GLOBALS['isopen']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");		
			$myerr="更新成功";				
		}			
	}else{
        $check = share_gettable($mytable." WHERE industry_id='".$GLOBALS['typeid']."' AND schemacode='".$GLOBALS['schemacode']."'");
        if(count($check) > 0){
            return "Schema代碼已存在";
        }
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{
			share_insert($mytable,"schemaname, schemacode, industry_id, isopen, dateadd, lastadmin","'" . $GLOBALS['schemaname'] . "','" . $GLOBALS['schemacode'] . "','" . $GLOBALS['typeid'] . "',0,NOW(),'" . $_SESSION['admuserid'] . "'");
			$myerr="新增成功";	
		}	
	}		 
	return $myerr;
}


function getContact3($mypage,$mytable,$mytable1,$mytable2){
    $mycheck = " where thisid>0 and schema_id='".$GLOBALS['schemaid']."' ";
    
    echo "<div style='background:#f8f9fa;padding:12px 16px;border-radius:4px;border:1px solid #ddd;margin-bottom:12px;font-size:14px;'>";
    echo "<span style='color:#d32f2f;font-weight:bold;'>架構：</span>Schema版本（第3層）<br>";
    echo "第3層上架後前台才會有內容可選。<br>";
    echo "Schema JSON 請輸入完整格式，需包含 @contex必要結構欄位。";
    echo "</div>";
    
    echo "<span class='fR' style='margin:10px 10px 0 0;font-size:14px;'><span class='redtext'>＊</span>必填</span>";
    echo "<TABLE class='formtable' width='100%' style='margin-top:0;margin-bottom:0;'>";
    
    if ($GLOBALS['schemaid']>0){
        $me = share_getinfo($mytable1, "thisid", $GLOBALS['schemaid']);
        $me2 = share_getinfo($mytable2, "thisid", $me['industry_id']);
        echo "<tr><th colspan=10>分類 ".$me2['thisname']." 管理 </th></TR>";
        echo "<tr><th colspan=10>Schema名稱 ".$me['schemaname']." 版本管理 </th></TR>";	
    }
    
    echo "<tr>";
    echo "<th width='8%'>刪除</th>";
    echo "<th width='15%'><span class='redtext'>＊</span>版本號</th>";
    echo "<th width='35%'><span class='redtext'>＊</span>Schema JSON</th>";
    echo "<th width='12%'>上架狀態</th>";
    echo "<th width='8%'>更新</th>";
    echo "</tr>";
    
    echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist3&typeid=".$GLOBALS['typeid']."&schemaid=".$GLOBALS['schemaid']."' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
    echo "<td>新增</td>";
    echo "<td><input type='text' name='version' value='' form='listform'></td>";
    echo "<td><textarea name='schema_json' rows='3' form='listform' style='width:95%;font-size:12px;font-family:monospace;' placeholder='請輸入 JSON 格式...'></textarea></td>";
    echo "<td></td>";
    echo "<td><input type='submit' class='gray-btn' value='新增' form='listform'></td>";
    echo "</tr>";
    
    $typelist = share_gettable($mytable.$mycheck." ORDER BY thisid");
    $formid = 1;
    foreach($typelist as $type){
        $formname = "itemform".$formid;
        
        echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist3&typeid=".$GLOBALS['typeid']."&schemaid=".$GLOBALS['schemaid']."&thisid=".$type['thisid']."' method='post' enctype='multipart/form-data' id='".$formname."'></FORM><tr>";
        
       if($type['isopen']==1){
            echo "<td>--</td>";
            echo "<td>".$type['version']."</td>";
            echo "<td><textarea rows='3' style='width:95%;font-size:12px;font-family:monospace;' readonly>".$type['schema_json']."</textarea></td>";
            echo "<td><span style='display:inline-block;padding:4px 12px;background:#4CAF50;color:#fff;border-radius:3px;font-size:13px;'>上架中</span></td>";
            echo "<td>--</td>";
       }else{
			
            echo "<td><a href='index.php?tf=".$mypage."&job=deltype3&typeid=".$GLOBALS['typeid']."&schemaid=".$GLOBALS['schemaid']."&thisid=".$type['thisid']."' class='blue-btn delbtn'>刪除</a></td>";
            echo "<td><input type='text' name='version' value='".$type['version']."' form='".$formname."'></td>";
            echo "<td><textarea name='schema_json' rows='3' form='".$formname."' style='width:95%;font-size:12px;font-family:monospace;' placeholder='請輸入 JSON 格式...'>".$type['schema_json']."</textarea></td>";
            echo "<td>".setisopenselectnew($type['isopen'], $formname)."</td>";
            echo "<td><input type='submit' class='gray-btn' value='更新' form='".$formname."'></td>";
			
        }
        
        echo "</tr>";
        $formid++;
    }
    echo "</table>";
}
function uptypelist3($mytable){
    $myerr = "";    
    if(empty($GLOBALS['version'])){
        $myerr .= "<br>版本號為必填項目";
    }
    
if(empty($GLOBALS['schema_json'])){
        $myerr .= "<br>JSON為必填項目";
    } else {		
        $raw_json = $GLOBALS['schema_json'];
        
        $safe_json = str_replace('\\', '&#92;', $raw_json);

        // 進行解碼驗證（此時已經沒有契約違規的斜線了，驗證必過）
        $decoded = json_decode($safe_json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $myerr .= "<br>JSON格式錯誤：" . json_last_error_msg();
        } else {
            if (!isset($decoded['@context']) ) {
                $myerr .= "<br>JSON缺少必要的結構欄位 (@context)";
            } else {
                // 驗證成功！我們把已經安全轉譯的 $safe_json 指定給全域變數準備寫入資料庫
                $GLOBALS['schema_json'] = $safe_json;
            }
        }
    }
    if($temp = share_getinfo($mytable, "thisid", $GLOBALS['thisid'])){ // 更新
        $check = share_gettable($mytable." WHERE schema_id='".$GLOBALS['schemaid']."' AND version='".$GLOBALS['version']."' AND thisid!='".$GLOBALS['thisid']."'");
        if(count($check) > 0){
            return "版本號已存在";
        }
        if($myerr){
            $myerr = "請確認以下內容：" . $myerr;
        }else{
            share_update($mytable,"`version`='" . $GLOBALS['version'] . "',`schema_json`='" . $GLOBALS['schema_json'] . "',`isopen`='" . $GLOBALS['isopen'] . "',`lastadmin`='" . $_SESSION['admuserid'] . "'","thisid='" . $GLOBALS['thisid'] . "'");
            $myerr = "更新成功";
        }
    }else{ 
		$check = share_gettable($mytable." WHERE schema_id='".$GLOBALS['schemaid']."' AND version='".$GLOBALS['version']."'");
        if(count($check) > 0){
            return "版本號已存在";
        }
        
        if($myerr){
            $myerr = "請確認以下內容：" . $myerr;
        }else{
            share_insert($mytable,"version, schema_json, schema_id, isopen, dateadd, lastadmin","'" . $GLOBALS['version'] . "','" . $GLOBALS['schema_json'] . "','" . $GLOBALS['schemaid'] . "',0,NOW(),'" . $_SESSION['admuserid'] . "'");
            $myerr = "新增成功";
        }
    }
    
    return $myerr;
}


?>		
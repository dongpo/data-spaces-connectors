<?php
$responz="";
$type=1; //分類設定1有，0無
$typeseo=1; //分類SEO設定1有，0無
$typeimg=1; //分類圖式設定1有，0無
$typewidth[1]=1920; //分類圖片
$typeheight[1]=400;	
$typewidth[2]=1028; //分類列表圖片
$typeheight[2]=400;	

$seowidth1=900;  //seo圖片
$seoheight1=470;	
$listseo=1; //列表SEO設定1有，0無
$listimg=0; //列表圖設定1(pc)，2(pc、手機)，3(pc、手機、列表)
$imgwidth[1]=1920; //圖片
$imgheight[1]=900;	
$imgwidth[2]=640; //列表圖片
$imgheight[2]=640;	
$newname="最新消息";
$listhtml=0;//列表html設定1有，0無
$listindex=0;//列表有無首頁設定1有，0無
$listtime=0;//列表有無開始結束日期1有，0無
global $imgurl;
$typetable="new_type";     //資料庫type table
$listtable="new_list";    //資料庫list table
$mypage="newslist";			//網頁名

 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;  
 }elseif($job=="uptypelist1"){	
   $tagon=1;
   $tagshow=1;  
   $responz=uptypelist1($typetable,$typeseo,$typeimg);
 }else if($job=="typesort"){
 	sorting($typetable,$GLOBALS['id']);
    $tagon=1;
    $tagshow=1; 
  }elseif($job=="deltype1"){	
    $tagon=1;
    $tagshow=1;  
	share_del($listtable." WHERE typeid='".$GLOBALS['thisid']."'");	   
	share_del($typetable." WHERE thisid='".$GLOBALS['thisid']."'");		
	$responz="刪除成功";	
 }else if($job=="uptypedetaillist1" ){
   $tagon=2;
   $tagshow=2;  
   $responz=uptypedetaillist1($typetable,$typeseo,$typeimg);	  	
 }elseif($job=="getdetaillist1"){	
   $tagon=2;
   $tagshow=2;
 }elseif($job=="getContact2"){   
   $tagon=3;
   $tagshow=3;  
 }elseif($job=="uptypelist2"){	
   $tagon=3;
   $tagshow=3; 
   $responz=uptypelist2($listtable,$listseo,$listimg,$listhtml,$listtime);   
  }elseif($job=="deltype2"){	
    $tagon=3;
    $tagshow=3;
	share_del($listtable." WHERE thisid='".$GLOBALS['thisid']."'");		
	$responz="刪除成功";	
 }else if($job=="listsort"){
 	sorting($listtable,$GLOBALS['id']);
    $tagon=3;
    $tagshow=3;  
 }elseif($job=="getdetaillist2"){	
   $tagon=4;
   $tagshow=4;
 }else if($job=="uptypedetaillist2" ){
   $tagon=4;
   $tagshow=4;  
   $responz=uptypedetaillist2($listtable,$listseo,$listimg,$listhtml,$listtime);	
 }else{
	if ($type==1){
	   $tagon=1;
	   $tagshow=1; 		
	}else{
		$tagon=3;
		$tagshow=3;
	}
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
	$('body').on('focus',",cleartext", function(){
       $(this).val('');
	});	
	//錯誤顯示
  err='<?php echo htmlspecialchars($responz ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>';
  if(err){
      $(".error").html(err);
      $(".error").fadeIn();
  }
});
</script>

<div class='bkhead'>
    <h1><?php echo htmlspecialchars($newname ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>管理</h1>
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
if 	($type==1){
?>		
		<a href='index.php?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'><?php echo htmlspecialchars($newname ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>分類設定</div>
        </a>
<?php 
}
if 	($tagon==2){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getdetaillist1&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'><?php echo htmlspecialchars($newname ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>分類明細</div>
        </a>		
<?php 
}
if 	($tagon>2){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getContact2&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==3?"on":"")?> ' >
          <div class='tagin'><?php echo htmlspecialchars($newname ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>列表</div>
        </a>
<?php 
}
if 	($tagon>3){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getdetaillist2&thisid=<?=$thisid?>&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==4?"on":"")?> ' >
          <div class='tagin'><?php echo htmlspecialchars($newname ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>內容明細</div>
        </a>			
<?php 
}
?>		
     </div>
      <div class='tagbody'>
        <div class='tagbodyin'>
<?php
		if($tagshow==1){
			getContact1($mypage,$typetable);
        }else if($tagshow==2){
			getdetail1($mypage,$typetable);
		}elseif($tagshow==3){		  
			getContact2($mypage,$listtable,$typetable);	
		}elseif($tagshow==4){
				if ($listhtml==1){
				
				}ELSE{
?>
<link nonce="<?=$_SESSION['nonce']?>" rel="stylesheet" type="text/css" href="../inblocks/css/inputblock.css?id=90" />
<link nonce="<?=$_SESSION['nonce']?>" rel="stylesheet" type="text/css" href="../inblocks/css/inputblockfront.css?id=34" />
<script nonce="<?=$_SESSION['nonce']?>" type="text/javascript" src="../inblocks/js/inputblock.js?id=90"></script>
<script  nonce="<?=$_SESSION['nonce']?>">
$(document).ready(function(){
	var pagename="newslist";//網頁code,譬如新聞頁 newspage..前台呼叫時呼應
	var pageid="<?php echo htmlspecialchars($GLOBALS['thisid'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>";//unique id
	//內容會插入  inputblockwrap
	ibprint_start(pagename,pageid,'');
});
</script>
<?PHP					
				}	
				getdetail2($mypage,$listtable,$typetable,$job);
		}
?>		
        </div>
      </div>
   </div>
 <?php
function getContact1($mypage,$mytable){
	global $newname;
	$mycheck=" where thisid>0 ";	
	$myline="&job=getContact1";
	echo "<font class='redtext'>新增資料:<br>1.點選該筆『編輯』則管理相關明細資料<br>2.點選該筆『".htmlspecialchars($newname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."管理』則管理『".htmlspecialchars($newname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."列表』，新增相關明細資料<br>3.搜尋時，則無法修改排序</font><br><br>";	
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<FORM action='index.php?tf=".$mypage."&job=getContact1' method='post' enctype='multipart/form-data' id='seachform'></FORM>";

	echo "<tr><th colspan=1>搜尋方式</th><th colspan=6 class='tleft'>";
	echo "分類名稱:<input type=text name='thisnamelist'  value='".htmlspecialchars($GLOBALS['thisnamelist'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='seachform'> ";	
	echo "<input type=submit value='查詢' class='gray-btn' form='seachform'></TH>";
	if (!empty($GLOBALS['thisnamelist']))
	{
		$mycheck.=" and `thisname` like '%".$GLOBALS['thisnamelist']."%'";
	}
	echo "<tr><th width=10% >刪除</th><Th width=40%><span class='redtext'>＊</span>分類名稱(限六個中文字)</TH><th width=10% >上架狀態</th><TH width=10%>更新</TH><TH width=10%>內容編輯</TH><TH width=10%>".htmlspecialchars($newname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."列表</TH><th width=10% >排序</th></tr>";	
	echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist1' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
	echo "<td>新增</th>";
	echo "<td><input type=text name=thisname value='' form='listform'></TD>";	
	echo "<td></td>";
	echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";
	echo "<td></td>";
	echo "<td></td>";		
	echo "<td></td>";
	echo "</tr>";			

	$typelist=share_gettable($mytable.$mycheck."  ORDER BY sorting DESC");
	$formid=1;
	$lcnt=1;
	foreach($typelist as $type){
		echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist1&thisid=".$type['thisid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
		echo "<td><a href='index.php?tf=".$mypage."&job=deltype1&thisid=".$type['thisid']."'  class='blue-btn delbtn'>刪除</a></td>";
		echo "<td><input type=text name=thisname value='".htmlspecialchars($type['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='itemform".$formid."'></TD>";	
		echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
		echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
		echo "<td><a href='index.php?tf=".$mypage."&job=getdetaillist1&typeid=".$type['thisid']."'  class='gray-btn'>編輯</a></TD>";				
		echo "<td><a href='index.php?tf=".$mypage."&job=getContact2&typeid=".$type['thisid']."'  class='gray-btn'>".htmlspecialchars($newname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."管理</a>";	
		if (!empty($GLOBALS['thisnamelist']))
		{
			echo "<td></td>";			
		}else{
			sortingbox("typesort",count($typelist),$type['thisid'],$type['sorting'],$lcnt);
		}			
		echo "</tr>";
		$formid++;
		$lcnt++;
	}
	echo "</table>";
}
function uptypelist1($mytable,$typeseo,$typeimg){
	$myerr="";
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid'])){//已經有了
		if(empty($GLOBALS['thisname'])){
			$myerr.="<br>分類名稱為必填專案";
		}else{
			$myerr.=checklen("分類名稱",$GLOBALS["thisname"],6,12);
		}	
		if ($GLOBALS['isopen']==2){
			if(empty($temp["thisname"])){
				$myerr.="<br>名稱為必填專案";
			}	
			if ($typeimg>0){	
				if(empty($temp['thispic'])){
					$myerr.="<br>PC版BANNER為必填專案";
				}
			
				if(empty($temp['thispicm'])){
					$myerr.="<br>手機版BANNER為必填專案";
				}		

			}
			if ($typeseo==1){
				if(empty($temp['seokey'])){
					$myerr.="<br>SEO關鍵字必填專案";
				}
				if(empty($temp['seopic'])){
					$myerr.="<br>SEO圖片必填專案";
				}
				if(empty($temp['seointro'])){
					$myerr.="<br>SEO描述必填專案";
				}			
			}	
		}			
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{				
			share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',isopen='".$GLOBALS['isopen']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");	
			$myerr="更新成功";				
		}			
	}else{
		if(empty($GLOBALS['thisname'])){
			$myerr.="<br>分類名稱為必填專案";
		}else{
			$myerr.=checklen("分類名稱",$GLOBALS["thisname"],6,12);
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
			share_insert($mytable,"`thisname`,sorting,dateadd,lastadmin","'".$GLOBALS['thisname']."','".$myorders."',now(),'".$_SESSION['admuserid']."'");	
			$myerr="新增成功";	
		}	
	}		 
	return $myerr;
}
function sorting($mtable,$id){
	global $typetable,$listtable;
	if($mtable==$typetable){
		$row=share_gettable($mtable." ORDER BY sorting DESC limit ".($GLOBALS["myorder"]-1).",1");
		$neworder=$row[0]['sorting'];
		if($GLOBALS["thisorder"]>$neworder){
			share_update($mtable,"sorting=sorting+1", "sorting<".$GLOBALS["thisorder"]." AND sorting>=".$neworder);
		}else{
			share_update($mtable,"sorting=sorting-1", "sorting>".$GLOBALS["thisorder"]." AND sorting<=".$neworder);
		}
		share_update($mtable,"sorting=".$neworder, "thisid=".$id);
	}else if($mtable==$listtable){
		$tst=share_getinfo($listtable,"thisid",$id);
		$row=share_gettable($listtable." WHERE typeid='".$tst['typeid']."' ORDER BY sorting DESC limit ".($GLOBALS["myorder"]-1).",1");
		$neworder=$row[0]['sorting'];
		if($GLOBALS["thisorder"]>$neworder){
			share_update($listtable,"sorting=sorting+1", "sorting<".$GLOBALS["thisorder"]." AND sorting>=".$neworder." and typeid='".$tst['typeid']."'");
		}else{
			share_update($listtable,"sorting=sorting-1", "sorting>".$GLOBALS["thisorder"]." AND sorting<=".$neworder." and typeid='".$tst['typeid']."'");
		}
		share_update($listtable,"sorting=".$neworder, "thisid=".$id);
	}	
}
function getdetail1($mypage,$mytable){
	global $imgurl,$typewidth,$typeheight,$typeseo,$typeimg,$seowidth1,$seoheight1;		
	$me=share_getinfo($mytable,"thisid",$GLOBALS['typeid']);	
	$me['thisname']=checkval($GLOBALS['thisname'],$me['thisname']);
	$me['thisnamee']=checkval($GLOBALS['thisnamee'],$me['thisnamee']);
	$me['seopic']=checkval($GLOBALS['seopic'],$me['seopic']);		
	$me['seointro']=checkval($GLOBALS['seointro'],$me['seointro']);	
	$me['seokey']=checkval($GLOBALS['seokey'],$me['seokey']);
	$me['thispic']=checkval($GLOBALS['thispic'],$me['thispic']);
	$me['thispicm']=checkval($GLOBALS['thispicm'],$me['thispicm']);
	$me['listpic']=checkval($GLOBALS['listpic'],$me['listpic']);	
	
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist1&typeid=".$GLOBALS['typeid']."' method='post' enctype='multipart/form-data' id='typeform'></form>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<tr><th colspan=2>".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')." 相關資料管理 </th></TR>";
	echo "<tr><th width=15% ><span class='redtext'>＊</span>名稱/SEO名稱(限六個中文字)</th><TD width=85% class='tleft'><input type=text name='thisname' value='".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='typeform'></TD></tr>";
		
	if ($typeseo==1){
		echo "<tr><th><span class='redtext'>＊</span>SEO關鍵字</th><TD  class='tleft'><input type=text name=seokey value='".htmlspecialchars($me['seokey'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='typeform'></TD></tr>";
		echo "             <TR>";
		echo "               <TH><span class='redtext'>＊</span>SEO圖片( ". $seowidth1." X ". $seoheight1." )</TH>";
		echo "               <TD  class='tleft' >";
			echo "<div>";
			echo "					<div class='imagewrap'>";
			if($me['seopic']){
				echo "				<img src='".htmlspecialchars($imgurl.$me['seopic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'> ";
			}
			echo "					</div>";
			echo "					<div class='filewrapout'>";
			echo "						<div class='fileimgwrap'>";
			echo "						</div>";
			echo "						<div class='filewrapnew'>";
			echo "							<input class='instantuploadp'  type='file' data-h='".$seoheight1."' data-w='".$seowidth1."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'>";
			echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
			echo "						</div>";
			echo "						<input class='formfield formreq imgadd ' form='typeform' name='seopic' type='hidden' value='".htmlspecialchars($me['seopic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2'>"; 
			echo "					</div>";				  

		echo "               </TD>";
		echo "             </TR>";	
		echo "             <TR>";
		echo "               <TH><span class='redtext'>＊</span>SEO描述/頁面上描述</TH>";
		echo "               <TD class='tleft' > <textarea name='seointro' form='typeform'>".htmlspecialchars($me['seointro'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."</textarea>";
		echo "               </TD>";
		echo "             </TR>";	
	}

	if ($typeimg>0){
			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>PC版BANNER( ". $typewidth[1]." X ". $typeheight[1]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispic']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['thispic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$typeheight[1]."' data-w='".$typewidth[1]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispic' type='hidden' value='".htmlspecialchars($me['thispic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='typeform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";	

			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>手機版BANNER( ". $typewidth[2]." X ". $typeheight[2]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispicm']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['thispicm'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$typeheight[2]."' data-w='".$typewidth[2]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispicm' type='hidden' value='".htmlspecialchars($me['thispicm'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='typeform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";					  
	}

	echo "<TR><TH></TH><TD class='tleft'><input type=submit class='gray-btn' value='更新' form='typeform'>點選「更新」儲存上方資料</TD></TR>";			
	echo "</TABLE>";
}		
function uptypedetaillist1($mytable,$typeseo,$typeimg){
	$myerr="";
	if(empty($GLOBALS["thisname"])){
		$myerr.="<br>名稱為必填專案";
	}else{
		$myerr.=checklen("名稱",$GLOBALS["thisname"],6,12);
	}		

	if ($typeimg>0){	
		if(empty($GLOBALS['thispic'])){
			$myerr.="<br>PC版BANNER為必填專案";
		}
	
		if(empty($GLOBALS['thispicm'])){
			$myerr.="<br>手機版BANNER為必填專案";
		}		
	}	
	if ($typeseo==1){
		if(empty($GLOBALS['seokey'])){
			$myerr.="<br>SEO關鍵字必填專案";
		}
		if(empty($GLOBALS['seopic'])){
			$myerr.="<br>SEO圖片必填專案";
		}
		if(empty($GLOBALS['seointro'])){
			$myerr.="<br>SEO描述必填專案";
		}			
	}
	if($myerr){
		$myerr="請確認以下內容:".$myerr;
	}else{
        share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thisnamee='".$GLOBALS['thisnamee']."',thispic='".$GLOBALS['thispic']."',thispicm='".$GLOBALS['thispicm']."',listpic='".$GLOBALS['listpic']."',seopic='".$GLOBALS['seopic']."',`seointro`='".$GLOBALS['seointro']."',`seokey`='".$GLOBALS['seokey']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['typeid']."'");	

		$myerr="更新成功";
	}	
	return $myerr;
}	
function getContact2($mypage,$mytable,$mytable1){
	global $newname,$listindex,$listtime;
	$mycheck=" where thisid>0 and typeid='".$GLOBALS['typeid']."' ";	
	echo "<font class='redtext'>新增資料:<br>1.先新增".$newname."名稱<br>2.點選該筆『內容編輯』，新增相關明細資料<br>3.排序以發佈日期排序<br>";	
	echo "<TABLE class=formtable width=100%  >";
	if ($GLOBALS['typeid']>0){
		$me=share_getinfo($mytable1,"thisid",$GLOBALS['typeid']);
		echo "<tr><th colspan=10>分類 ".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')." 管理 </th></TR>";
	}	
	echo "<tr><th colspan=1>搜尋方式</th><th colspan=6 class='tleft'>";
	echo "<FORM action='index.php?tf=".$mypage."&job=getContact2&typeid=".$GLOBALS['typeid']."' method='post' enctype='multipart/form-data' id='seachform'></FORM>";
	echo $newname."名稱:<input type=text name='thisnamelist'  value='".htmlspecialchars($GLOBALS['thisnamelist'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'  form='seachform'> ";	
	echo "<input type=submit value='查詢' class='gray-btn' form='seachform'></TH>";
	if (!empty($GLOBALS['thisnamelist']))
	{
		$mycheck.=" and `thisname` like '%".$GLOBALS['thisnamelist']."%'";
	}	
	echo "<tr><th width=10% >刪除</th><Th width=40%><span class='redtext'>＊</span>".$newname."名稱</TH><th width=10% ><span class='redtext'>＊</span>發佈日期</th><th width=10% >上架狀態</th><TH width=10%>更新</TH><TH width=10%>內容編輯</TH></tr>";	
	echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2&typeid=".$GLOBALS['typeid']."' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
	echo "<td>新增</th>";
	echo "<td><input type=text name=thisname value='' form='listform'></TD>";	
	echo "<td><input type=date name=mydate class='dateitem w90' value='' form='listform' ></TD>";
	echo "<td></td>";	
	echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";
		
	echo "<td></td>";
	echo "</tr>";			

 	$cct=share_getcount($mytable.$mycheck);
	$perpage=30;
	$pcct=ceil($cct/$perpage);
	if($GLOBALS['p']){
		$page=$GLOBALS['p'];
	}else{
		$page=1;
	}


	$typelist=share_gettable($mytable.$mycheck."  order by mydate DESC limit ".(($page-1)*$perpage)." ,".$perpage );	
	
	$formid=1;
	$lcnt=1;
	foreach($typelist as $type){
		echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist2&thisid=".$type['thisid']."&typeid=".$GLOBALS['typeid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
		echo "<td><a href='index.php?tf=".$mypage."&job=deltype2&thisid=".$type['thisid']."&typeid=".$GLOBALS['typeid']."'  class='blue-btn delbtn'>刪除</a></td>";
		echo "<td><input type=text name=thisname value='".htmlspecialchars($type['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='itemform".$formid."'></TD>";	
		echo "<td><input type=date name=mydate class='dateitem w90' value='".htmlspecialchars($type['mydate'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'  form='itemform".$formid."' ></TD>";			
		echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
		echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
		echo "<td><a href='index.php?tf=".$mypage."&job=getdetaillist2&thisid=".$type['thisid']."&typeid=".$GLOBALS['typeid']."'  class='gray-btn'>編輯</a></TD>";				

		echo "</tr>";
		$formid++;
		$lcnt++;
	}
	echo "</table>";	
		echo "<div  id='pagewrap'>";
		for($a=1;$a<=$pcct;$a++){
			if($page==$a){
				echo "<span class='gray-btn'> -".$a."- </span>";
			}else{
				echo "<a href='index.php?tf=".$mypage."&job=getContact2&typeid=".$GLOBALS['typeid']."&p=".$a."&thisnamelist=".htmlspecialchars($GLOBALS['thisnamelist'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' class='gray-btn'><span> -".$a."- </span></a>";
			}
			if (($a%10)==0)
			{
				echo"<BR>";
			}
		}
		echo "</div>";	
} 		
function uptypelist2($mytable,$listseo,$listimg,$listhtml,$listtime){
	global $newname;
	$myerr="";
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid'])){//已經有了
		if ($GLOBALS['isopen']==2){
			if(empty($temp["thisname"])){
				$myerr.="<br>名稱為必填專案";
			}
			if(empty($temp['mydate'])){
				$myerr.="<br>發佈日期為必填專案";
			}				
			if ($listimg>0){	
				if(empty($temp['thispic'])){
					$myerr.="<br>大圖為必填專案";
				}
			
				if(empty($temp['listpic'])){
					$myerr.="<br>列表圖為必填專案";
				}							
			}
			if ($listseo==1){
				if(empty($temp['seokey'])){
					$myerr.="<br>SEO關鍵字必填專案";
				}
				if(empty($temp['seopic'])){
					$myerr.="<br>SEO圖片必填專案";
				}
				if(empty($temp['seointro'])){
					$myerr.="<br>SEO描述必填專案";
				}			
			}
			if ($listtime==1){
				if(empty($temp['opentime'])){
					$myerr.="<br>開始日期必填專案";
				}
				if(empty($temp['closetime'])){
					$myerr.="<br>結束日期必填專案";
				}
			}			
			if ($listhtml==1){
				if(empty($temp['thishtml'])){
					$myerr.="<br>html語法必填專案";
				}
			}
		}			
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{				
			share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',mydate='".$GLOBALS['mydate']."',isopen='".$GLOBALS['isopen']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");
	
			$myerr="更新成功";				
		}			
	}else{
		if(empty($GLOBALS['thisname'])){
			$myerr.="<br>".$newname."名稱為必填專案";
		}	
		if(empty($GLOBALS['mydate'])){
			$myerr.="<br>發佈日期為必填專案";
		}				
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{
			$row=share_gettable($mytable." where typeid='".$GLOBALS['typeid']."'  order by sorting desc limit 1");
			if(is_array($row)){
				$myorders=$row[0]["sorting"]+1;
			}else{
				$myorders=1;
			}				
			share_insert($mytable,"`thisname`,typeid,mydate,sorting,dateadd,lastadmin","'".$GLOBALS['thisname']."','".$GLOBALS['typeid']."','".$GLOBALS['mydate']."','".$myorders."',now(),'".$_SESSION['admuserid']."'");	
			$myerr="新增成功";	
		}	
	}		 
	return $myerr;
}
function getdetail2($mypage,$mytable,$mytable1,$job){
	global $imgurl,$imgwidth,$imgheight,$listseo,$listimg,$seowidth1,$seoheight1,$listhtml,$listtime,$listindex;				
	$me=share_getinfo($mytable,"thisid",$GLOBALS['thisid']);	
	$me['thisname']=checkval($GLOBALS['thisname'],$me['thisname']);
	$me['thisnamee']=checkval($GLOBALS['thisnamee'],$me['thisnamee']);
	$me['seopic']=checkval($GLOBALS['seopic'],$me['seopic']);		
	$me['seointro']=checkval($GLOBALS['seointro'],$me['seointro']);	
	$me['seokey']=checkval($GLOBALS['seokey'],$me['seokey']);
	$me['thispic']=checkval($GLOBALS['thispic'],$me['thispic']);	
	$me['listpic']=checkval($GLOBALS['listpic'],$me['listpic']);
	$me['mydate']=checkval($GLOBALS['mydate'],$me['mydate']);
	$me['opentime']=checkval($GLOBALS['opentime'],$me['opentime']);
	$me['closetime']=checkval($GLOBALS['closetime'],$me['closetime']);	
	$me['isindex']=checkval($GLOBALS['isindex'],$me['isindex']);	
	
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist2&typeid=".$GLOBALS['typeid']."&thisid=".$GLOBALS['thisid']."' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100%  >";
	if ($GLOBALS['typeid']>0){
		$me1=share_getinfo($mytable1,"thisid",$GLOBALS['typeid']);
		echo "<tr><th colspan=10>分類 ".htmlspecialchars($me1['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')." 管理 </th></TR>";
	}		
	echo "<tr><th colspan=2>".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')." 相關資料管理 </th></TR>";
	
	if ($listindex>0){	
		echo "<tr><th width=15% >是否上首頁</th><TD width=80% class='tleft'><input type=checkbox name=isindex value='1' ".($me['isindex']==1?"checked":"")." form='listform'>熱門文章</TD></tr>";	
	}
	echo "<tr><th width=15% ><span class='redtext'>＊</span>名稱</th><TD width=85% class='tleft'><input type=text name='thisname' value='".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";
	echo "<tr><th width=15% ><span class='redtext'>＊</span>發佈日期</th><td width=85% class='tleft'><input type=date name=mydate class='dateitem w90' value='".htmlspecialchars($me['mydate'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'  form='listform'></TD>";
	if ($listtime>0){
		echo "<tr><th width=15% ><span class='redtext'>＊</span>開始日期</th><td width=85% class='tleft'><input type=date name=opentime class='dateitem w90' value='".htmlspecialchars($me['opentime'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'  form='listform'></TD>";
		echo "<tr><th width=15% ><span class='redtext'>＊</span>結束日期</th><td width=85% class='tleft'><input type=date name=closetime class='dateitem w90' value='".htmlspecialchars($me['closetime'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'  form='listform'></TD>";		
	}
		
	if ($listimg>0){
			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>大圖( ". $imgwidth[1]." X ". $imgheight[1]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispic']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['thispic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[1]."' data-w='".$imgwidth[1]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispic' type='hidden' value='".htmlspecialchars($me['thispic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";	

			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>列表圖( ". $imgwidth[2]." X ". $imgheight[2]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['listpicm']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['listpicm'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[2]."' data-w='".$imgwidth[2]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='listpic' type='hidden' value='".htmlspecialchars($me['listpic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";					  

	}
	if ($listseo==1){
		echo "<tr><th><span class='redtext'>＊</span>SEO關鍵字</th><TD  class='tleft'><input type=text name=seokey value='".htmlspecialchars($me['seokey'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";
		echo "             <TR>";
		echo "               <TH><span class='redtext'>＊</span>SEO圖片( ". $seowidth1." X ". $seoheight1." )</TH>";
		echo "               <TD  class='tleft' >";
			echo "<div>";
			echo "					<div class='imagewrap'>";
			if($me['seopic']){
				echo "				<img src='".htmlspecialchars($imgurl.$me['seopic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'> ";
			}
			echo "					</div>";
			echo "					<div class='filewrapout'>";
			echo "						<div class='fileimgwrap'>";
			echo "						</div>";
			echo "						<div class='filewrapnew'>";
			echo "							<input class='instantuploadp'  type='file' data-h='".$seoheight1."' data-w='".$seowidth1."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'>";
			echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
			echo "						</div>";
			echo "						<input class='formfield formreq imgadd ' name='seopic' type='hidden' value='".htmlspecialchars($me['seopic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
			echo "					</div>";				  

		echo "               </TD>";
		echo "             </TR>";	
		echo "             <TR>";
		echo "               <TH><span class='redtext'>＊</span>SEO描述/頁面上描述</TH>";
		echo "               <TD class='tleft' > <textarea name='seointro' form='listform'>".htmlspecialchars($me['seointro'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."</textarea>";
		echo "               </TD>";
		echo "             </TR>";	
	}
	if ($listhtml==1){
			echo "             <TR>";
			echo "               <TH ><span class='redtext'>＊</span>html語法</TH>";
			echo "               <TD> <textarea id='thishtml' name='thishtml' class='editorDetail' form='listform'>".htmlspecialchars($me['thishtml'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."</textarea>";
			echo "               </TD>";
			echo "             </TR>";	
		echo "<TR><TH></TH><TD><input type=submit class='gray-btn' value='更新' form='listform'>點選「更新」儲存上方資料</TD></TR>";
	}else{
		echo "<TR><TH></TH><TD><input type=submit class='gray-btn' value='更新' form='listform'>點選「更新」儲存上方資料</TD></TR>";			
		echo "            <tr><th colspan='2'><div class='tagbody'>";		
		echo "                 <div class='tagbodyin inputblockwrapback' id='inputblockwrapback'>";	

		echo "                 </div>";		
		echo "             </th></tr></div>";				
	}
	echo "</TABLE>";
}		
function uptypedetaillist2($mytable,$listseo,$listimg,$listhtml,$listtime){
	$myerr="";
	if(empty($GLOBALS["thisname"])){
		$myerr.="<br>名稱為必填專案";
	}	

	if ($listimg>0){	
		if(empty($GLOBALS['thispic'])){
			$myerr.="<br>大圖為必填專案";
		}
		
		if(empty($GLOBALS['listpicm'])){
			$myerr.="<br>列表圖為必填專案";
		}			
	}	
	if ($listseo==1){
		if(empty($GLOBALS['seokey'])){
			$myerr.="<br>SEO關鍵字必填專案";
		}
		if(empty($GLOBALS['seopic'])){
			$myerr.="<br>SEO圖片必填專案";
		}
		if(empty($GLOBALS['seointro'])){
			$myerr.="<br>SEO描述必填專案";
		}			
	}
	if ($listhtml==1){
		if(empty($GLOBALS['thishtml'])){
			$myerr.="<br>html語法必填專案";
		}
	}
	if ($listtime==1){
		if(empty($GLOBALS['opentime'])){
			$myerr.="<br>開始日期必填專案";
		}
		if(empty($GLOBALS['closetime'])){
			$myerr.="<br>結束日期必填專案";
		}
	}			
	if($myerr){
		$myerr="請確認以下內容:".$myerr;
	}else{	
		$GLOBALS['isindex']=0;
        share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thisnamee='".$GLOBALS['thisnamee']."',thispic='".$GLOBALS['thispic']."',listpic='".$GLOBALS['listpic']."',seopic='".$GLOBALS['seopic']."',`seointro`='".$GLOBALS['seointro']."',`seokey`='".$GLOBALS['seokey']."',`mydate`='".$GLOBALS['mydate']."',`isindex`='".$GLOBALS['isindex']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid']."'");	
		if ($listhtml==1){
			share_update($mytable,"`thishtml`='".$GLOBALS['thishtml']."'","thisid='".$GLOBALS['thisid']."'");			
		}
		$myerr="更新成功";
	}	
	return $myerr;
}	

?>
<?php

$responz="";
$newname="首頁管理";
global $imgurl;
$listtable="sit_pages";    //資料庫list table
$bannertable="bas_banner";
$mypage="indexseo";
$seowidth1=900;  //seo圖片
$seoheight1=470;
$imgwidth[1]=1920; //圖片
$imgheight[1]=400;	
$imgwidth[2]=1028; //列表圖片
$imgheight[2]=400;	
$listimg=1;
$thisid=1;
$thishtml=0;
$indexid=1;
if($job=="uptypedetaillist1" ){
	$tagon=1;
	$tagshow=1;
	$responz=uptypedetaillist1($listtable,$mypage);   
  }elseif($job=="getContact4"){
   $tagon=5;
   $tagshow=5;	    
 }elseif($job=="uptypelist4"){	
   $tagon=5;
   $tagshow=5;	 
   $responz=uptypelist4($bannertable); 
  }elseif($job=="deltype4"){	
   $tagon=5;
   $tagshow=5;	
	share_del($bannertable." WHERE thisid='".$GLOBALS['thisid1']."'");		
	$responz="刪除成功";	   
 }else if($job=="listsort"){
 	sorting($bannertable,$GLOBALS['id']);
   $tagon=5;
   $tagshow=5;	   
 }elseif($job=="getdetaillist4"){	
   $tagon=6;
   $tagshow=6;	
 }else if($job=="uptypedetaillist4" ){
   $tagon=6;
   $tagshow=6;	  
   $responz=uptypedetaillist4($bannertable);         
 }else{
	$tagon=1;
	$tagshow=1;
	
 }
?>
	<style nonce="<?=$_SESSION['nonce']?>" >
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
<script nonce="<?=$_SESSION['nonce']?>" >
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
		<a href='index.php?tf=<?=$mypage?>&job=getdetaillist1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>共用seo</div>
        </a>
		<a href='index.php?tf=<?=$mypage?>&job=getContact4' class='tagitem  <?=($tagon==5?"on":"")?> ' >
          <div class='tagin'>BANNER列表</div>
        </a>			
<?php 
if 	($tagon>5){
?>	
		<a href='index.php?tf=<?=$mypage?>&job=getdetaillist4&thisid1=<?=$thisid1?>' class='tagitem  <?=($tagon==6?"on":"")?> ' >
          <div class='tagin'>BANNER內容明細</div>
        </a>			
<?php 
}		
?>			
    </div>
      <div class='tagbody'>
        <div class='tagbodyin'>	
<?php
		if($tagshow==1){
			getdetail($mypage,$listtable,$myline,$tagshow);
		}else if($tagshow==5){
			getContact4($mypage,$bannertable);	
		}else if($tagshow==6){		
			getdetail4($mypage,$bannertable);			
		}	
?>		
        </div>
      </div>
   </div>		
<?php

function getdetail2($mypage,$mytable){
	$logowidth1=0;  
	$logoheight1=93;		
	global $imgurl,$indexid;				
	$me=share_getinfo($mytable,"thisid",$indexid);	
	$me['logopic']=checkval($GLOBALS['logopic'],$me['logopic']);	
	$me['logointro']=checkval($GLOBALS['logointro'],$me['logointro']);		
	$me['list1']=checkval($GLOBALS['list1'],$me['list1']);	
	$me['list2']=checkval($GLOBALS['list2'],$me['list2']);	
	$me['list3']=checkval($GLOBALS['list3'],$me['list3']);	
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist2' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<tr><th colspan=2>基本設定</th></TR>";

		echo "             <TR>";
		echo "               <TH>LOGO圖片( ". $logowidth1." X ". $logoheight1." )</TH>";
		echo "               <TD  class='tleft' >";
			echo "<div>";
			echo "					<div class='imagewrap'>";
			if($me['logopic']){
				echo "				<img src='".htmlspecialchars($imgurl.$me['logopic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'> ";
			}
			echo "					</div>";
			echo "					<div class='filewrapout'>";
			echo "						<div class='fileimgwrap'>";
			echo "						</div>";
			echo "						<div class='filewrapnew'>";
			echo "							<input class='instantuploadp'  type='file' data-h='".$logoheight1."' data-w='".$logowidth1."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'>";
			echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
			echo "						</div>";
			echo "						<input class='formfield formreq imgadd ' name='logopic' type='hidden' value='".htmlspecialchars($me['logopic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
			echo "					</div>";				  

		echo "               </TD>";
		echo "             </TR>";	
	
	echo "<tr><th width=15% ><span class='redtext'>＊</span>LOGO說明</th><TD width=85% class='tleft'><input type=text name='logointro' value='".htmlspecialchars($me['logointro'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";	
		echo "<TR><TH></TH><TD><input type=submit class='gray-btn' value='更新' form='listform'>點選「更新」儲存上方資料</TD></TR>"	;
}


function getdetail($mypage,$listtable,$myline,$tagshow){
	global $imgurl,$seowidth1,$seoheight1,$thisid,$imgwidth,$imgheight,$listimg;
	$me=share_getinfo($listtable,"thispage",$mypage);
	$me['thisname']=checkval($GLOBALS['thisname'],$me['thisname']);	
	$me['seointro']=checkval($GLOBALS['seointro'],$me['seointro']);	
	$me['seokey']=checkval($GLOBALS['seokey'],$me['seokey']);
	$me['seopic']=checkval($GLOBALS['seopic'],$me['seopic']);
	$me['thispic']=checkval($GLOBALS['thispic'],$me['thispic']);	
	$me['thispicm']=checkval($GLOBALS['thispicm'],$me['thispicm']);	
	echo "共用seo";
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist1' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<tr><th colspan=2>相關資料管理 </th></TR>";
	echo "<tr><th width=15% ><span class='redtext'>＊</span>標題名稱</th><TD width=85% class='tleft'><input type=text name='thisname' value='".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";
		echo "<tr><th><span class='redtext'>＊</span>SEO關鍵字</th><TD  class='tleft'><input type=text name=seokey value='".htmlspecialchars($me['seokey'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";
		echo "             <TR>";
		echo "               <TH><span class='redtext'>＊</span>SEO圖片( ". $seowidth1." X ". $seoheight1." )</TH>";
		echo "               <TD  class='tleft' >";
			echo "<div>";
			echo "					<div class='imagewrap'>";
			if($me['seopic']){
				echo "				<img src='".htmlspecialchars($imgurl.$me['seopic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'> ";
			}
			echo "					</div>";
			echo "					<div class='filewrapout'>";
			echo "						<div class='fileimgwrap'>";
			echo "						</div>";
			echo "						<div class='filewrapnew'>";
			echo "							<input class='instantuploadp'  type='file' data-h='".$seoheight1."' data-w='".$seowidth1."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'>";
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
	if ($listimg>0){
			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>PC版BANNER( ". $imgwidth[1]." X ". $imgheight[1]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispic']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['thispic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[1]."' data-w='".$imgwidth[1]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispic' type='hidden' value='".htmlspecialchars($me['thispic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";	

			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>手機版BANNER( ". $imgwidth[2]." X ". $imgheight[2]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispicm']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['thispicm'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[2]."' data-w='".$imgwidth[2]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispicm' type='hidden' value='".htmlspecialchars($me['thispicm'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";					  

	}
		
		echo "<TR><TH></TH><TD><input type=submit class='gray-btn' value='更新' form='listform'>點選「更新」儲存上方資料</TD></TR>";			
		echo "            <tr><th colspan='2'><div class='tagbody'>";		
		echo "                 <div class='tagbodyin inputblockwrapback' id='inputblockwrapback'>";	

		echo "                 </div>";		
		echo "             </th></tr></div>";				
	echo "</TABLE>";	
if ($thishtml==1){
?>
<link nonce="<?=$_SESSION['nonce']?>" rel="stylesheet" type="text/css" href="../inblocks/css/inputblock.css?id=90" />
<link nonce="<?=$_SESSION['nonce']?>" rel="stylesheet" type="text/css" href="../inblocks/css/inputblockfront.css?id=34" />
<script nonce="<?=$_SESSION['nonce']?>" type="text/javascript" src="../inblocks/js/inputblock.js?id=90"></script>
<script nonce="<?=$_SESSION['nonce']?>" >
$(document).ready(function(){
	var pagename="<?php echo htmlspecialchars($mypage ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>";//網頁code,譬如新聞頁 newspage..前台呼叫時呼應
	var pageid="<?php echo htmlspecialchars($thisid ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');?>";//unique id
	//內容會插入  inputblockwrap
	ibprint_start(pagename,pageid,'');
});
</script>
<?php	
}
}
function uptypedetaillist1($mytable,$mypage){
	global $listimg;
	$myerr="";
	if(empty($GLOBALS['thisname'])){
		$myerr.="<br>標題名稱為必填專案";
	}	
	if(empty($GLOBALS['seokey'])){
		$myerr.="<br>SEO關鍵字必填專案";
	}
	if(empty($GLOBALS['seopic'])){
		$myerr.="<br>SEO圖片必填專案";
	}
	if(empty($GLOBALS['seointro'])){
		$myerr.="<br>SEO描述必填專案";
	}	
	if ($listimg>0){	
		if(empty($GLOBALS['thispic'])){
			$myerr.="<br>PC版BANNER為必填專案";
		}
	
		if(empty($GLOBALS['thispicm'])){
			$myerr.="<br>手機版BANNER為必填專案";
		}							
	}	
	
	
	if($myerr){
		$myerr="請確認以下內容:".$myerr;
	}else{	
       share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thispic='".$GLOBALS['thispic']."',thispicm='".$GLOBALS['thispicm']."',seopic='".$GLOBALS['seopic']."',`seointro`='".$GLOBALS['seointro']."',`seokey`='".$GLOBALS['seokey']."',`lastadmin`='".$_SESSION['admuserid']."'","thispage='".$mypage."'");	
		$myerr="更新成功";
	}	
	return $myerr;	
} 	
function getdetail3($mypage,$mytable){
$logowidth1=400;  
$logoheight1=400;		
	global $imgurl,$indexid;				
	$me=share_getinfo($mytable,"thisid",$indexid);	
	$me['newtype']=checkval($GLOBALS['newtype'],$me['newtype']);
	$me['thispic']=checkval($GLOBALS['thispic'],$me['thispic']);
	$me['thispicintro']=checkval($GLOBALS['thispicintro'],$me['thispicintro']);		
	$me['thisname']=checkval($GLOBALS['thisname'],$me['thisname']);	
	$me['thisintro']=checkval($GLOBALS['thisintro'],$me['thisintro']);
	$me['thisdate']=checkval($GLOBALS['thisdate'],$me['thisdate']);
	$me['thishttp']=checkval($GLOBALS['thishttp'],$me['thishttp']);
	
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist3' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<tr><th colspan=2>相關資料管理 </th></TR>";

	echo "<tr><th width=15% ><span class='redtext'>＊</span>標題名稱</th><TD width=85% class='tleft'><input type=text name='thisname' value='".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";
	echo "             <TR>";
	echo "               <TH><span class='redtext'>＊</span>描述<br>請勿斷行系統會自動斷行</TH>";
	echo "               <TD class='tleft' > <textarea name='thisintro' form='listform'>".htmlspecialchars($me['thisintro'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."</textarea>";
	echo "               </TD>";
	echo "             </TR>";		
	
	echo "<tr><th width=15% ><span class='redtext'>＊</span>發佈日期</th><TD width=85% class='tleft'><input type=date name=thisdate class='dateitem w100p'  form='listform'  value='".htmlspecialchars($me['thisdate'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'></TD></tr>";	
	echo "<tr><th width=15% ><span class='redtext'>＊</span>超連結網址</th><TD width=85% class='tleft'><input type=text name='thishttp' value='".htmlspecialchars($me['thishttp'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'><br>請輸入站內網址，例如『懶人包專區』網址為https://網域/Clarify.php，請輸入Clarify.php</TD></tr>";	

	echo "<TR><TH width=15% ><span class='redtext'>＊</span>置頂管理</TH><TD width=85% class='tleft'>置頂使用:";
	
	echo "<input type='radio' name='newtype' value='0' data-id='0' ".(($me['newtype']=="0")?"CHECKED":"")." class='newtype' form='listform'>文字";			
	echo "<input type='radio' name='newtype' value='1' data-id='1' ".(($me['newtype']=="1")?"CHECKED":"")." class='newtype' form='listform'>圖片";
	echo "</TD></TR>";		
	
		echo "             <TR>";
		echo "               <TH>圖片( ". $logowidth1." X ". $logoheight1." )</TH>";
		echo "               <TD  class='tleft' >";
			echo "<div>";
			echo "					<div class='imagewrap'>";
			if($me['thispic']){
				echo "				<img src='".htmlspecialchars($imgurl.$me['thispic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'> ";
			}
			echo "					</div>";
			echo "					<div class='filewrapout'>";
			echo "						<div class='fileimgwrap'>";
			echo "						</div>";
			echo "						<div class='filewrapnew'>";
			echo "							<input class='instantuploadp'  type='file' data-h='".$logoheight1."' data-w='".$logowidth1."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'>";
			echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
			echo "						</div>";
			echo "						<input class='formfield formreq imgadd ' name='thispic' type='hidden' value='".htmlspecialchars($me['thispic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
			echo "					</div>";				  

		echo "               </TD>";
		echo "             </TR>";	

		echo "<tr><th width=15% >圖片說明</th><TD width=85% class='tleft'><input type=text name='thispicintro' value='".htmlspecialchars($me['thispicintro'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";
	
		echo "<TR><TH></TH><TD><input type=submit class='gray-btn' value='更新' form='listform'>點選「更新」儲存上方資料</TD></TR>";			
	echo "</TABLE>";
}		
function getContact4($mypage,$mytable){
	global $indexid;	
	$newname="BANNER";
	$mycheck=" where thisid>0 and thispageid='".$indexid."' ";	
	echo "<font color=red>新增資料:<br>1.先新增".htmlspecialchars($newname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."名稱<br>2.點選該筆『內容編輯』，新增相關明細資料<br>3.搜尋時，則無法修改排序</font><br><br>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<tr><th width=10% >刪除</th><Th width=40%><span class='redtext'>＊</span>".htmlspecialchars($newname, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."名稱</TH><th width=10% >上架狀態</th><TH width=10%>更新</TH><TH width=10%>內容編輯</TH><th width=10% >排序</th></tr>";	
	echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist4' method='post' enctype='multipart/form-data' id='listform'></FORM><tr>";
	echo "<td>新增</th>";
	echo "<td><input type=text name=thisname value='' form='listform'></TD>";	
	echo "<td></td>";
	echo "<td><input type=submit class='gray-btn' value='新增' form='listform'></td>";
	echo "<td></td>";		
	echo "<td></td>";
	echo "</tr>";			

	$typelist=share_gettable($mytable.$mycheck."  ORDER BY sorting DESC");
	$formid=1;
	$lcnt=1;
	foreach($typelist as $type){
		echo "<FORM action='index.php?tf=".$mypage."&job=uptypelist4&thisid1=".$type['thisid']."' method='post' enctype='multipart/form-data' id='itemform".$formid."'></FORM><tr>";
		echo "<td><a href='index.php?tf=".$mypage."&job=deltype4&thisid1=".$type['thisid']."'  class='blue-btn delbtn'>刪除</a></td>";
		echo "<td><input type=text name=thisname value='".htmlspecialchars($type['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='itemform".$formid."'></TD>";	
		
		echo "<td>".setisopenselectnew($type['isopen'],"itemform".$formid)."</TD>";
		
		echo "<td><input type=submit class='gray-btn' value='更新' form='itemform".$formid."'></td>";
		echo "<td><a href='index.php?tf=".$mypage."&job=getdetaillist4&thisid1=".$type['thisid']."'  class='gray-btn'>編輯</a></TD>";				

		if (!empty($GLOBALS['thisnamelist']))
		{
					
		}else{
			sortingbox("listsort",(count($typelist)),$type['thisid'],$type['sorting'],$lcnt,$GLOBALS['thisid1']);
			$lcnt++;
		}			
		echo "</tr>";
		$formid++;
		
	}
	echo "</table>";	

} 		
function uptypelist4($mytable){
	global $indexid;		
	$myerr="";
	if($temp=share_getinfo($mytable,"thisid",$GLOBALS['thisid1'])){//已經有了
		if ($GLOBALS['isopen']==2){
			if(empty($temp["thisname"])){
				$myerr.="<br>BANNER名稱為必填專案";
			}
			if(empty($temp['thispic'])){
				$myerr.="<br>pc圖片為必填專案";
			}		
			if(empty($temp['thispicm'])){
				$myerr.="<br>手機圖片為必填專案";
			}					
		}			
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{				
			share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',isopen='".$GLOBALS['isopen']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid1']."'");		
			$myerr="更新成功";				
		}			
	}else{
		if(empty($GLOBALS['thisname'])){
			$myerr.="<br>BANNER名稱為必填專案";
		}		
		if($myerr){
			$myerr="請確認以下內容:".$myerr;
		}else{
			$row=share_gettable($mytable." where thispageid='".$indexid."' order by sorting desc limit 1");
			if(is_array($row)){
				$myorders=$row[0]["sorting"]+1;
			}else{
				$myorders=1;
			}				
			share_insert($mytable,"`thisname`,thispageid,sorting,dateadd,lastadmin","'".$GLOBALS['thisname']."',".$indexid.",'".$myorders."',now(),'".$_SESSION['admuserid']."'");	
			$myerr="新增成功";	
		}	
	}		 
	return $myerr;
}

function sorting($mtable,$id){
	$tst=share_getinfo($mtable,"thisid",$id);
	$row=share_gettable($mtable." WHERE thispageid='".$tst['thispageid']."' ORDER BY sorting DESC limit ".($GLOBALS["myorder"]-1).",1");
	$neworder=$row[0]['sorting'];
	if($GLOBALS["thisorder"]>$neworder){
		share_update($mtable,"sorting=sorting+1", "sorting<".$GLOBALS["thisorder"]." AND sorting>=".$neworder." and thispageid='".$tst['thispageid']."'");
	}else{
		share_update($mtable,"sorting=sorting-1", "sorting>".$GLOBALS["thisorder"]." AND sorting<=".$neworder." and thispageid='".$tst['thispageid']."'");
	}
	share_update($mtable,"sorting=".$neworder, "thisid=".$id);	
}
function getdetail4($mypage,$mytable){
	global $imgurl,$imgwidth,$imgheight;				
	$me=share_getinfo($mytable,"thisid",$GLOBALS['thisid1']);	
	$me['thisname']=checkval($GLOBALS['thisname'],$me['thisname']);
	$me['thisintro']=checkval($GLOBALS['thisintro'],$me['thisintro']);
	$me['thispic']=checkval($GLOBALS['thispic'],$me['thispic']);
	$me['thispicm']=checkval($GLOBALS['thispicm'],$me['thispicm']);		
	$me['ishttps']=checkval($GLOBALS['ishttps'],$me['ishttps']);	
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist4&typeid=".$GLOBALS['typeid']."&thisid1=".$GLOBALS['thisid1']."' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<tr><th colspan=2>".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')." 相關資料管理 </th></TR>";
	echo "<tr><th width=15% ><span class='redtext'>＊</span>名稱</th><TD width=85% class='tleft'><input type=text name='thisname' value='".htmlspecialchars($me['thisname'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'></TD></tr>";
			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>pc圖( ". $imgwidth[1]." X ". $imgheight[1]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispic']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['thispic'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[1]."' data-w='".$imgwidth[1]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispic' type='hidden' value='".htmlspecialchars($me['thispic'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";	
			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>手機圖( ". $imgwidth[2]." X ". $imgheight[2]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispicm']){
					echo "				<img src='".htmlspecialchars($imgurl.$me['thispicm'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[2]."' data-w='".$imgwidth[2]."' data-url='".htmlspecialchars($imgurl, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8']."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispicm' type='hidden' value='".htmlspecialchars($me['thispicm'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";				  
	echo "<tr><th width=15% ><span class='redtext'>＊</span>超連結網址</th><TD width=85% class='tleft'><input type=text name='ishttps' value='".htmlspecialchars($me['ishttps'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."' form='listform'><br>如輸入站內網址，例如『檢測機構』網址為https://網域/agencies.php，請輸入agencies.php</TD></tr>";	
	echo "<TR><TH></TH><TD><input type=submit class='gray-btn' value='更新' form='listform'>點選「更新」儲存上方資料</TD></TR>";
	echo "</TABLE>";	
}		
function uptypedetaillist4($mytable){
	$myerr="";
	if(empty($GLOBALS["thisname"])){
		$myerr.="<br>名稱為必填專案";
	}	
	if(empty($GLOBALS['thispic'])){
		$myerr.="<br>pc圖為必填專案";
	}	
	if(empty($GLOBALS['thispicm'])){
		$myerr.="<br>手機圖為必填專案";
	}		
	if($myerr){
		$myerr="請確認以下內容:".$myerr;
	}else{	
        share_update($mytable,"`thisname`='".$GLOBALS['thisname']."',thisintro='".$GLOBALS['thisintro']."',thispic='".$GLOBALS['thispic']."',thispicm='".$GLOBALS['thispicm']."',ishttps='".$GLOBALS['ishttps']."',`lastadmin`='".$_SESSION['admuserid']."'","thisid='".$GLOBALS['thisid1']."'");	

		$myerr="更新成功";
	}	
	return $myerr;
}	

?>
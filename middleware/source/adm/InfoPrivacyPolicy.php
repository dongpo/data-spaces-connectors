<?php
$responz="";
$newname="資訊安全政策";
global $imgurl;
$listtable="sit_pages";    //資料庫list table
$mypage="InfoPrivacyPolicy";
$seowidth1=900;  //seo圖片
$seoheight1=470;
$imgwidth[1]=1920; //圖片
$imgheight[1]=400;	
$imgwidth[2]=1028; //列表圖片
$imgheight[2]=400;	
$listimg=1;
$thisid=1;
if($job=="uptypedetaillist1" ){
	$tagon=1;
	$tagshow=1;
	$responz=uptypedetaillist1($listtable,$mypage);   
 }else{
	$tagon=1;
	$tagshow=1;
	
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
		<a href='index.php?tf=<?=$mypage?>&job=getdetaillist1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'><?php echo $newname;?> </div>
        </a>
    </div>
      <div class='tagbody'>
        <div class='tagbodyin'>	
<?php
	getdetail($mypage,$listtable,$myline,$tagshow);
?>		
        </div>
      </div>
   </div>		
<?php
function getdetail($mypage,$listtable,$myline,$tagshow){
	global $imgurl,$seowidth1,$seoheight1,$thisid,$imgwidth,$imgheight,$listimg;
	$me=share_getinfo($listtable,"thispage",$mypage);
	$me['thisname']=checkval($GLOBALS['thisname'],$me['thisname']);	
	$me['seointro']=checkval($GLOBALS['seointro'],$me['seointro']);	
	$me['seokey']=checkval($GLOBALS['seokey'],$me['seokey']);
	$me['seopic']=checkval($GLOBALS['seopic'],$me['seopic']);
	$me['thispic']=checkval($GLOBALS['thispic'],$me['thispic']);	
	$me['thispicm']=checkval($GLOBALS['thispicm'],$me['thispicm']);	
	
	echo "<span class='fR' ><span class='redtext'>＊</span>必填</span>";
	echo "<form action='index.php?tf=".$mypage."&job=uptypedetaillist1' method='post' enctype='multipart/form-data' id='listform'></form>";	
	echo "<TABLE class=formtable width=100%  >";
	echo "<tr><th colspan=2>相關資料管理 </th></TR>";
	echo "<tr><th width=15% ><span class='redtext'>＊</span>標題名稱</th><TD width=85% class='tleft'><input type=text name='thisname' value='".$me['thisname']."' form='listform'></TD></tr>";
		echo "<tr><th><span class='redtext'>＊</span>SEO關鍵字</th><TD  class='tleft'><input type=text name=seokey value='".$me['seokey']."' form='listform'></TD></tr>";
		echo "             <TR>";
		echo "               <TH><span class='redtext'>＊</span>SEO圖片( ". $seowidth1." X ". $seoheight1." )</TH>";
		echo "               <TD  class='tleft' >";
			echo "<div>";
			echo "					<div class='imagewrap'>";
			if($me['seopic']){
				echo "				<img src='".$imgurl.$me['seopic']."'> ";
			}
			echo "					</div>";
			echo "					<div class='filewrapout'>";
			echo "						<div class='fileimgwrap'>";
			echo "						</div>";
			echo "						<div class='filewrapnew'>";
			echo "							<input class='instantuploadp'  type='file' data-h='".$seoheight1."' data-w='".$seowidth1."' data-url='".$imgurl."'>";
			echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
			echo "						</div>";
			echo "						<input class='formfield formreq imgadd ' name='seopic' type='hidden' value='".$me['seopic']."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
			echo "					</div>";				  
		echo "               </TD>";
		echo "             </TR>";	
		echo "             <TR>";
		echo "               <TH><span class='redtext'>＊</span>SEO描述/頁面上描述</TH>";
		echo "               <TD class='tleft' > <textarea name='seointro' form='listform'>".$me['seointro']."</textarea>";
		echo "               </TD>";
		echo "             </TR>";	
	if ($listimg>0){
			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>PC版BANNER( ". $imgwidth[1]." X ". $imgheight[1]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispic']){
					echo "				<img src='".$imgurl.$me['thispic']."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[1]."' data-w='".$imgwidth[1]."' data-url='".$imgurl."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispic' type='hidden' value='".$me['thispic']."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
				echo "					</div>";				  
	  
			  echo "               </TD>";
			  echo "             </TR>";	

			  echo "             <TR>";
			  echo "               <TH><span class='redtext'>＊</span>手機版BANNER( ". $imgwidth[2]." X ". $imgheight[2]." )</TH>";
			  echo "               <TD class='tleft' >";
				echo "<div>";
				echo "					<div class='imagewrap'>";
				if($me['thispicm']){
					echo "				<img src='".$imgurl.$me['thispicm']."'> ";
				}
				echo "					</div>";
				echo "					<div class='filewrapout'>";
				echo "						<div class='fileimgwrap'>";
				echo "						</div>";
				echo "						<div class='filewrapnew'>";
				echo "							<input class='instantuploadp'  type='file' data-h='".$imgheight[2]."' data-w='".$imgwidth[2]."' data-url='".$imgurl."'>";
				echo "							<div class='filewrapinp gray-btn'>上傳jpg/png檔</div>";
				echo "						</div>";
				echo "						<input class='formfield formreq imgadd ' name='thispicm' type='hidden' value='".$me['thispicm']."' data-err='請上傳圖片' data-max='100' data-min='0' data-chk='text2' form='listform'>"; 
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

?>
<link nonce="<?=$_SESSION['nonce']?>" rel="stylesheet" type="text/css" href="../inblocks/css/inputblock.css?id=90" />
<link nonce="<?=$_SESSION['nonce']?>" rel="stylesheet" type="text/css" href="../inblocks/css/inputblockfront.css?id=34" />
<script nonce="<?=$_SESSION['nonce']?>" type="text/javascript" src="../inblocks/js/inputblock.js?id=90"></script>
<script nonce="<?=$_SESSION['nonce']?>" >
$(document).ready(function(){
	var pagename="<?php echo $mypage;?>";//網頁code,譬如新聞頁 newspage..前台呼叫時呼應
	var pageid="<?php echo $thisid;?>";//unique id
	//內容會插入  inputblockwrap
	ibprint_start(pagename,pageid,'');
});
</script>
<?php	
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

?>
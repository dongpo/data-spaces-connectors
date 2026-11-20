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
$currentPage = 'contracts';
$mypage="contracts";
require_once 'seonew.php';
require_once 'headerlogin.php';
$typetable="gaia_data_products";
$uptable="gaia_subscriptions";
require_once 'adm/config/product.php';
$responz="";
global $imgurl;
 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;
 }elseif($job=="getdetaillist1"){	
   $tagon=2;
   $tagshow=2;
} elseif ($job == "monthlyAudit") {    
    $tagon = 3; 
	$tagshow = 3;
} elseif ($job == "jsonDownload") {    
    $tagon = 4; 
	$tagshow = 4;
 }else{
   $tagon=1;
   $tagshow=1;	 
 }
?>
<main id="main" role="main">
<section aria-label="契約管理 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="契約管理 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">契約管理</h1>
</div>
</section>
<section aria-label="契約管理" class="detail-page-hero detail-intro-after-query">
<div class="member-container">

    <?php include 'sidebar.php'; ?>
    <div class="content-area">
<div class='contentin'>

  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>
		<a href='contracts.html?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>契約列表</div>
        </a>
<?php 
if 	($tagon>1 ){
?>	
		<a href='contracts.html?tf=<?=$mypage?>&job=getdetaillist1&typeid=<?=$typeid?>' class='tagitem  <?=($tagon==2?"on":"")?> ' >
          <div class='tagin'>契約明細</div>
        </a>
		<a href='contracts.html?tf=<?=$mypage?>&job=monthlyAudit&typeid=<?=$typeid?>' class='tagitem <?=($tagon==3?"on":"")?>'>
            <div class='tagin'>月報表</div>
        </a>
        <a href='contracts.html?tf=<?=$mypage?>&job=jsonDownload&typeid=<?=$typeid?>' class='tagitem <?=($tagon==4?"on":"")?>'>
            <div class='tagin'>紀錄下載</div>
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
			getContractList($mypage, 'incoming');
		}else if($tagshow==2){
			getContractDetail($mypage, 'incoming');
		}else if($tagshow==3){
			getMonthlyAuditList($mypage, $typeid);
		}else if($tagshow==4){
			getJsonDownloadPage($mypage, $typeid);	
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
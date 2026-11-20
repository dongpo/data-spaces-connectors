<?php 
session_start();
require_once 'adm/config/config.php';
require_once 'adm/config/getformf.php';
require_once 'adm/config/createimg.php';
require_once 'adm/config/share.php';
global $wedaddress,$imwebgurl;
$imgurl=$wedaddress.$imwebgurl;	
$URL=$wedaddress.$_SERVER['REQUEST_URI'];
$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);
require_once 'Share.php';//共用資料
$temp=share_getinfo("`sit_pages`","thispage",'contact');
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);
$pdo = null;
require_once 'seonew.php';
require_once 'header.php';?>
<main id="main">
<section aria-label="聯絡我們 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="聯絡我們 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section class="intro-top-tools">
<div class="intro-container-1280">
<nav aria-label="麵包屑" class="breadcrumb intro-breadcrumb">
<a aria-label="首頁" class="breadcrumb-home-link" href="index.html"><span aria-hidden="true" class="crumb-home-icon"></span></a>
<span aria-hidden="true">/</span>
<span>聯絡我們</span>
</nav>
<div class="intro-page-heading-centered">
<p class="intro-page-en-title">Contact Us</p>
<h1 class="intro-page-title" id="intro-page-title">聯絡我們</h1>
</div>
</div>
</section>
<section aria-labelledby="section-why-title" class="intro-content-section why-section" id="section-why">
<div class="intro-container-1280">

	<section class='inputblockwrap'>
	<h2 class="sr-only">內容區</h2>
	</section>
</div>
</div>

</main>
<?php require_once 'footer.php';?>  
<?php require_once 'js.php';?>  
<script nonce="<?=$nonce?>" > 
$(document).ready(function(){
	$(".nav-inner a").removeAttr("aria-current");
	$(".mycontact").attr("aria-current", "page");
	var pagename="contact";//網頁code,譬如新聞頁 newspage..前台呼叫時呼應
	var pageid="1";//unique id
	if(pagename && pageid){
		ibprint_show(".inputblockwrap",pagename,pageid);
	}else{
	}

}); 
</script>	
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
$temp=share_getinfo("`sit_pages`","thispage",'PrivacyPolicy');
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
<main id="main" role="main">
<style nonce="<?php echo $nonce; ?>">
/* 桌機版：強制覆蓋外部 CSS */
.inner-hero-market {
    background-image: url('<?php echo $bgImage; ?>') !important;
}

/* 手機版：強制覆蓋外部 CSS */
@media (max-width: 720px) {
    .inner-hero-market {
        background-image: url('<?php echo $bgImageM; ?>') !important;
    }
}
</style>

<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="inner-hero-overlay"></div>
<div class="container inner-hero-content">
<nav aria-label="麵包屑" class="breadcrumb">
<a href="index.html">首頁</a>
<span aria-hidden="true">/</span>
<a href="PrivacyPolicy.html"><?php echo $temp['thisname'];?></a>
</nav>
<p class="eyebrow"><?php echo $temp['thispage'];?></p>
<h1><?php echo $temp['thisname'];?></h1>
<p></p>
</div>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="detail-page-hero detail-intro-after-query">
<div class="container">
	<section class='inputblockwrap'>
	<h2 class="sr-only">內容區</h2>
	</section>
</div>
</section>
</main>
<?php require_once 'footer.php';?>  
<?php require_once 'js.php';?>  
<script nonce="<?=$nonce?>" > 
$(document).ready(function(){
	var pagename="PrivacyPolicy";//網頁code,譬如新聞頁 newspage..前台呼叫時呼應
	var pageid="1";//unique id
	if(pagename && pageid){
		ibprint_show(".inputblockwrap",pagename,pageid);
	}else{
	}

}); 
</script>	
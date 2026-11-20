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
$temp=share_getinfo("`sit_pages`","thispage",'newsseo');
$mytable="new_list";
$cct=share_getcount($mytable." where isopen=1 and thisid=".$id);

if ($cct>0){


}else{
	header('Location: index.php');
	exit;		
}
$temp1=share_getinfo($mytable,"thisid",$id);
$out['page']=$temp1['thisname'];
$out['name']=$temp1['thisname'];
$out['intro']=$temp1['seointro'];
$out['key']=$temp1['seokey'];
$out['pic']=$temp1['seopic'];
$typeMap = [];
foreach($newmenu1 as $type){
    $typeMap[$type['thisid']] = $type['thisname'];
}
$typeName = isset($typeMap[$temp1['typeid']]) ? $typeMap[$temp1['typeid']] : '未分類';

$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);
$pdo = null;
require_once 'seonew.php';
require_once 'header.php';?>
<main id="main">
<section aria-label="公告消息 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="公告消息 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section class="intro-top-tools">
<div class="intro-container-1280">
<nav aria-label="麵包屑" class="breadcrumb intro-breadcrumb">
<a aria-label="首頁" class="breadcrumb-home-link" href="index.html"><span aria-hidden="true" class="crumb-home-icon"></span></a>
<span aria-hidden="true">/</span>
<a href="newslist.html?id=ALL">公告消息</a><span aria-hidden="true">/</span>
<span aria-current="page"><?php echo $temp1['thisname'];?></span>
</nav>
<div class="intro-page-heading-centered">
<p class="intro-page-en-title">ANNOUNCEMENTS</p>
<h1 class="intro-page-title" id="intro-page-title">公告消息</h1>
</div>
</div>
</section>
<section aria-labelledby="section-why-title" class="intro-content-section why-section" id="section-why">
<div class="intro-container-1280">
<div aria-label="消息資訊" class="news-article-meta"><span><b>發布日期</b><time datetime="<?php echo dateTo_ad($temp1['dateupdate']);?>"><?php echo dateTo_ad($temp1['dateupdate']);?></time></span></div>
<h2 id="news-article-title"><?php echo $temp1['thisname'];?></h2>
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
	$(".mynews").attr("aria-current", "page");
	var pagename="newslist";//網頁code,譬如新聞頁 newspage..前台呼叫時呼應
	var pageid="<?php echo $id;?>";//unique id
	if(pagename && pageid){
		ibprint_show(".inputblockwrap",pagename,pageid);
	}else{
	}

}); 
</script>	
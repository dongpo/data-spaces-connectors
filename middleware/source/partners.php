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
$temp=share_getinfo("`sit_pages`","thispage",'InfoPrivacyPolicy');
$out['page']="夥伴名單";
$out['name']="夥伴名單";
$out['intro']="夥伴名單";
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);
$pdo = null;
require_once 'seonew.php';
require_once 'header.php';?> 
<script defer="" nonce="<?php echo $nonce; ?>" src="js/partners-data.js?id=13"></script>
<script defer=""   nonce="<?=$nonce?>"  src="js/partners.js?id=1"></script>
<main id="main">
<section aria-labelledby="partners-page-title" class="partners-page-hero">
<div class="intro-container-1280 partners-hero-inner">
<nav aria-label="麵包屑" class="breadcrumb intro-breadcrumb dds-breadcrumb partners-breadcrumb">
<a aria-label="首頁" class="breadcrumb-home-link" href="index.html"><span aria-hidden="true" class="crumb-home-icon"></span></a>
<span aria-hidden="true">/</span>
<span aria-current="page">夥伴名單</span>
</nav>
<header class="partners-heading">
<p class="section-kicker">PARTNER ECOSYSTEM</p>
<h1 id="partners-page-title">夥伴名單</h1>
<p>串聯公部門、交通移動、地理資訊、技術服務與應用場域夥伴，共同建立可信任、可持續發展的資料合作生態系。</p>
<div aria-label="夥伴名單摘要" class="partners-metrics">
<span><strong data-partner-total="">5</strong><small>合作夥伴</small></span>
<span><strong data-partner-categories="">5</strong><small>合作類型</small></span>
<span><strong>DATA</strong><small>可信任連結</small></span>
</div>
</header>
<div aria-label="夥伴資料生態系動態示意" class="ecosystem-stage" data-partner-ecosystem="">
<div aria-hidden="true" class="ecosystem-grid-pattern"></div>
<svg aria-hidden="true" class="ecosystem-links" id="ecosystem-links"></svg>
<div aria-hidden="true" class="ecosystem-core">
<span class="ecosystem-core-ring ring-a"></span>
<span class="ecosystem-core-ring ring-b"></span>
<div class="ecosystem-core-center">
<strong>資料空間</strong>
<span>DATA SPACE</span>
</div>
</div>
<ul aria-label="精選合作夥伴" class="ecosystem-nodes" id="ecosystem-nodes"></ul>
</div>
<p class="ecosystem-note">動態效果僅作為視覺輔助；完整夥伴資訊可於下方名錄瀏覽。</p>
</div>
</section>
<section aria-labelledby="partners-directory-title" class="partners-directory-section">
<div class="intro-container-1280">
<div class="partners-directory-heading">
<div>
<p class="section-kicker">PARTNER DIRECTORY</p>
<h2 id="partners-directory-title">合作夥伴名錄</h2>
</div>
<p>依合作類型快速瀏覽夥伴；未來若由後台新增或調整資料，可直接沿用同一份夥伴資料來源同步呈現。</p>
</div>
<!--
<div aria-label="依合作類型篩選" class="partner-filter-bar">
<button aria-pressed="true" class="is-active" data-partner-filter="all" type="button">全部</button>
<button aria-pressed="false" data-partner-filter="public" type="button">公部門與公共資料</button>
<button aria-pressed="false" data-partner-filter="mobility" type="button">移動與交通服務</button>
<button aria-pressed="false" data-partner-filter="geo" type="button">地理資訊與導航</button>
<button aria-pressed="false" data-partner-filter="technology" type="button">技術與平台服務</button>
<button aria-pressed="false" data-partner-filter="application" type="button">應用場域與合作夥伴</button>
</div>
-->
<p aria-live="polite" class="partner-count-text" id="partner-count-text">目前顯示 7 個夥伴</p>
<div class="partner-directory-grid" id="partner-directory"></div>
<noscript><p class="partners-noscript">此頁的夥伴名錄使用 JavaScript 產生；正式系統串接後可由後台資料直接輸出完整名錄。</p></noscript>
</div>
</section>
</main>
<?php require_once 'footer.php';?>  
<?php require_once 'js.php';?>  
<script nonce="<?=$nonce?>" > 
$(document).ready(function(){
	$(".nav-inner a").removeAttr("aria-current");
	$(".mypartners").attr("aria-current", "page");
}); 
</script>	
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
$temp=share_getinfo("`sit_pages`","thispage",'about');
$out['page']="資料空間";
$out['name']="資料空間";
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);
$pdo = null;
require_once 'seonew.php';
require_once 'header.php';?>
<main id="main">
<section aria-label="資料空間簡介 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="資料空間簡介 Banner 圖" height="350" src="images/replaceable/intro-banner-16x4.jpg" width="1920"/>
</section>
<section class="intro-top-tools">
<div class="intro-container-1280">
<nav aria-label="麵包屑" class="breadcrumb intro-breadcrumb">
<a aria-label="首頁" class="breadcrumb-home-link" href="index.html"><span aria-hidden="true" class="crumb-home-icon"></span></a>
<span aria-hidden="true">/</span>
<span>資料空間簡介</span>
</nav>
<div class="intro-page-heading-centered">
<p class="intro-page-en-title">DATA SPACE OVERVIEW</p>
<h1 class="intro-page-title" id="intro-page-title">資料空間簡介</h1>
</div>
</div>
</section>
<!--
<nav aria-label="本頁快速導覽" class="intro-sticky-nav-section" data-intro-sticky-nav="">
<div class="intro-container-1280">
<div class="intro-quick-nav compact-quick-nav" data-intro-quick-nav="">
<a href="#section-what">什麼是資料空間？</a>
<a href="#section-why">為什麼需要資料空間？</a>
<a href="#section-benefits">加入有什麼好處？</a>
<a href="#section-usecases">資料空間可以做什麼？</a>
<a href="#section-security">資料共享機制是否安全？</a>
</div>
</div>
</nav>
-->
<section aria-labelledby="section-what-title" class="intro-content-section" id="section-what">
<div class="intro-container-1280">
<div class="what-layout">
<div class="intro-copy-wrap plain-panel what-copy">
<p class="section-kicker">WHAT IS DATA SPACE</p>
<h2 id="section-what-title">什麼是資料空間？</h2>
<p class="section-lead">Data Space 是一個可信任的資料共享與應用環境。</p>
<p>資料空間（Data Space）是一種以資料自主、可信任共享、安全治理及跨域應用為核心的資料流通環境。透過共同的資料標準、共享規範、身分識別、權限管理及安全機制，不同組織可以在保有資料控制權的前提下，進行資料的共享、共享與應用。</p>
<p>資料空間並非單純的資料庫，而是一個讓不同資料提供者與資料使用者，能夠在可信任的環境下進行資料合作的數位基礎設施。</p>
<div class="intro-quote clean-quote">資料不一定要集中，價值可以被連結。</div>
</div>
<figure class="intro-visual-card clean-visual what-image">
<!-- 可自行替換 JPG：images/replaceable/intro-what-is.jpg -->
<img alt="什麼是資料空間示意圖" height="960" src="images/replaceable/intro-what-is.jpg" width="1600"/>
</figure>
</div>
</div>
</section>
<section aria-labelledby="section-why-title" class="intro-content-section why-section" id="section-why">
<div class="intro-container-1280">
<div class="intro-heading clean-heading why-heading">
<div>
<p class="section-kicker">WHY DATA SPACE</p>
<h2 id="section-why-title">為什麼需要資料空間？</h2>
</div>
</div>
<div class="why-feature-row why-feature-row-rev13">
<figure class="intro-visual-card clean-visual why-image-rev11">
<!-- 可自行替換 JPG：images/replaceable/intro-why.jpg -->
<img alt="從資料孤島走向資料協作示意圖" height="960" src="images/replaceable/intro-why.jpg" width="1600"/>
</figure>
<div class="why-right-stack">
<p class="why-intro-copy why-intro-box">在數位轉型的過程中，各組織累積了大量資料，但資料往往分散於不同系統與組織之中。資料空間的目的，就是讓這些原本彼此孤立的資料，在安全與治理機制下走向協作。</p>
<div class="why-summary-block">
<span class="why-summary-label">FROM SILOS TO COLLABORATION</span>
<strong>從資料孤島，走向資料協作</strong>
<p>透過共同的共享規範、資料治理、身分與權限機制，降低跨組織合作的技術與信任門檻。</p>
</div>
</div>
</div>
<div aria-label="資料共享痛點" class="why-problem-grid">
<article><span class="problem-dot"></span><strong>資料分散</strong><p>資料分散於不同組織與資訊系統，難以形成完整的資料脈絡。</p></article>
<article><span class="problem-dot"></span><strong>共享困難</strong><p>不同系統的格式、標準與介接方式不一致，增加資料共享成本。</p></article>
<article><span class="problem-dot"></span><strong>信任不足</strong><p>資料提供者對資料使用方式、範圍與責任缺乏充分掌握。</p></article>
<article><span class="problem-dot"></span><strong>應用受限</strong><p>跨組織資料難以有效整合，連帶限制分析與創新應用。</p></article>
</div>
<div class="why-solution-strip">
<span>DATA SPACE</span>
<strong>在不犧牲資料自主權與安全性的前提下，實現資料流通與跨域合作。</strong>
</div>
</div>
</section>
<section aria-labelledby="section-benefits-title" class="intro-full-bleed benefits-full-section" id="section-benefits">
<div class="full-section-overlay"></div>
<div class="intro-container-wide benefits-inner">
<div class="full-section-heading">
<p class="section-kicker">BENEFITS</p>
<h2 id="section-benefits-title">加入資料空間有什麼好處？</h2>
<p>以安全、效率、信任與跨域應用為核心，建立可持續發展的資料合作生態。</p>
</div>
<div aria-label="加入資料空間六大好處" class="benefit-grid benefit-grid-full">
<article><span>01</span><h3>安全共享</h3><p>建立安全且可信任的資料共享環境，降低資料共享過程中的資安與管理風險。</p></article>
<article><span>02</span><h3>保有資料自主權</h3><p>資料提供者可以掌握資料的使用權限、使用範圍及共享條件，不必因資料共享而失去控制。</p></article>
<article><span>03</span><h3>降低資料共享成本</h3><p>透過共同標準及資料共享機制，減少不同組織之間反覆開發介接服務的成本。</p></article>
<article><span>04</span><h3>促進跨域合作</h3><p>串聯政府、企業、研究機構及產業夥伴，讓不同領域的資料產生更多合作可能。</p></article>
<article><span>05</span><h3>創造資料價值</h3><p>讓原本分散、孤立的資料被重新組合與分析，進一步發展新的產品、服務及應用。</p></article>
<article><span>06</span><h3>建立可信任的資料生態系</h3><p>透過會員、身分、權限、資料治理與共享紀錄等機制，逐步形成安全、透明且可持續發展的資料生態系。</p></article>
</div>
</div>
</section>
<section aria-labelledby="section-usecases-title" class="intro-content-section usecases-section-rev11" id="section-usecases">
<div class="intro-container-1280">
<div class="intro-heading clean-heading usecase-heading-rev11">
<div>
<p class="section-kicker">USE CASES</p>
<h2 id="section-usecases-title">資料空間可以做什麼？</h2>
</div>
<p>資料，從共享開始產生價值。從資料發布、搜尋、共享到應用，形成一套清楚而可治理的合作流程。</p>
</div>
<div aria-label="資料空間四種典型應用" class="usecase-grid-clean">
<article class="usecase-clean-card">
<img alt="資料提供圖示" class="usecase-icon-image" height="240" src="images/replaceable/usecase-icons/provide.png" width="240"/>
<div>
<span class="usecase-tag">DATA PROVIDE</span>
<h3>資料提供</h3>
<p>組織可以將可共享或可共享的資料服務發布至資料空間。</p>
<p class="usecase-flow-text">發布資料服務 → 設定使用條件 → 提供合作夥伴使用</p>
</div>
</article>
<article class="usecase-clean-card">
<img alt="資料搜尋圖示" class="usecase-icon-image" height="240" src="images/replaceable/usecase-icons/search.png" width="240"/>
<div>
<span class="usecase-tag">DATA SEARCH</span>
<h3>資料搜尋</h3>
<p>會員可以依照資料類型、主題、提供者或應用領域尋找所需資料。</p>
<p class="usecase-flow-text">找資料 → 查看資料服務 → 申請使用</p>
</div>
</article>
<article class="usecase-clean-card">
<img alt="資料共享圖示" class="usecase-icon-image" height="240" src="images/replaceable/usecase-icons/exchange.png" width="240"/>
<div>
<span class="usecase-tag">DATA EXCHANGE</span>
<h3>資料共享</h3>
<p>依照既定的共享規範與權限機制，進行跨組織資料共享。</p>
<p class="usecase-flow-text">身分驗證 → 權限確認 → 資料共享 → 紀錄追蹤</p>
</div>
</article>
<article class="usecase-clean-card">
<img alt="資料應用圖示" class="usecase-icon-image" height="240" src="images/replaceable/usecase-icons/application.png" width="240"/>
<div>
<span class="usecase-tag">DATA APPLICATION</span>
<h3>資料應用</h3>
<p>結合不同來源的資料，進行分析、AI、決策支援或創新服務。</p>
<p class="usecase-flow-text">資料 × AI × 產業 × 應用</p>
</div>
</article>
</div>
</div>
</section>
<section aria-labelledby="section-security-title" class="intro-full-bleed security-full-section" id="section-security">
<div class="full-section-overlay security-overlay"></div>
<div class="intro-container-wide security-inner">
<div class="security-full-heading">
<div>
<p class="section-kicker">SECURITY &amp; GOVERNANCE</p>
<h2 id="section-security-title">資料空間的資料共享機制是否安全？</h2>
</div>
<div class="security-lead-block">
<strong>安全，是資料流通的基礎。</strong>
<p>本資料空間透過多層次的安全與治理機制，建立可信任的資料共享環境。</p>
</div>
</div>
<div aria-label="六項安全與治理機制" class="security-full-grid">
<article><img alt="" src="images/security/identity.png"/><div><h3>身分驗證</h3><p>確認資料提供者與使用者身分</p></div></article>
<article><img alt="" src="images/security/permission.png"/><div><h3>權限管理</h3><p>依照角色與授權條件控管資料使用</p></div></article>
<article><img alt="" src="images/security/governance.png"/><div><h3>資料治理</h3><p>建立資料標準、使用規範與管理機制</p></div></article>
<article><img alt="" src="images/security/exchange.png"/><div><h3>安全共享</h3><p>確保資料共享過程的安全性</p></div></article>
<article><img alt="" src="images/security/tracking.png"/><div><h3>使用追蹤</h3><p>保留資料使用與共享紀錄</p></div></article>
<article><img alt="" src="images/security/traceability.png"/><div><h3>責任可追溯</h3><p>建立完整的資料使用軌跡</p></div></article>
</div>
<div class="security-final-quote">讓資料可以流通，也讓資料的使用有跡可循。</div>
</div>
</section>
</main>

<?php require_once 'footer.php';?>  
<?php require_once 'js.php';?>  
<script nonce="<?=$nonce?>" > 
$(document).ready(function(){
	$(".nav-inner a").removeAttr("aria-current");
	$(".myabout").attr("aria-current", "page");
}); 
</script>	

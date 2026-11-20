
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
$temp=share_getinfo("`sit_pages`","thispage",'indexseo');
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$pdo = null;
require_once 'seonew.php';
require_once 'header.php';?>
<main id="main">
<section aria-labelledby="hero-title" class="hero" data-parallax-root="">
<div aria-hidden="true" class="hero-grid-bg parallax-layer" data-layer-depth="0.08"></div>
<div aria-hidden="true" class="hero-orb hero-orb-a parallax-layer" data-layer-depth="0.2"></div>
<div aria-hidden="true" class="hero-orb hero-orb-b parallax-layer" data-layer-depth="0.14"></div>
<div aria-hidden="true" class="hero-spotlight parallax-layer" data-layer-depth="0.1"></div>
<div class="shell hero-layout">
<div class="hero-copy parallax-layer" data-layer-depth="0.12" data-layer-scroll="0.12">
<!--<p class="eyebrow"><span></span></p>-->
<h1 id="hero-title">保有資料主權<em>建立可信流通</em></h1>
<p class="hero-lead">建立可信任的資料共享環境，串聯產業企業、政府機關、學術與研究機構夥伴，促進跨域合作與多元創新應用。</p>
<div class="hero-actions">
<a class="button button-primary" href="about.html">認識資料空間 <span aria-hidden="true">→</span></a>
</div>
<p class="hero-note">資料不一定要集中，價值可以被連結。</p>
</div>
<div aria-label="資料提供者、資料空間與資料使用者之間進行可信任資料共享的示意圖" class="data-visual parallax-stage" data-tilt-stage="">
<div aria-hidden="true" class="visual-halo visual-halo-a parallax-layer" data-layer-depth="0.09"></div>
<div aria-hidden="true" class="visual-halo visual-halo-b parallax-layer" data-layer-depth="0.13"></div>
<div aria-hidden="true" class="visual-grid parallax-layer" data-layer-depth="0.08"></div>
<div aria-hidden="true" class="visual-particle particle-a parallax-layer" data-layer-depth="0.18"></div>
<div aria-hidden="true" class="visual-particle particle-b parallax-layer" data-layer-depth="0.22"></div>
<div aria-hidden="true" class="visual-particle particle-c parallax-layer" data-layer-depth="0.16"></div>
<div aria-hidden="true" class="orbit orbit-one"></div>
<div aria-hidden="true" class="orbit orbit-two"></div>
<div aria-hidden="true" class="signal signal-a"></div>
<div aria-hidden="true" class="signal signal-b"></div>
<div aria-hidden="true" class="signal signal-c"></div>
<div aria-hidden="true" class="ripple-field parallax-layer" data-layer-depth="0.08" data-layer-scroll="0.06">
<span class="ripple-wave ripple-wave-1"></span>
<span class="ripple-wave ripple-wave-2"></span>
<span class="ripple-wave ripple-wave-3"></span>
<span class="ripple-wave ripple-wave-4"></span>
</div>
<div class="core parallax-layer" data-base-transform="translate(-50%, -50%)" data-layer-depth="0.3" data-layer-scroll="0.18">
<span aria-hidden="true" class="core-ring"></span>
<strong>資料空間DATA SPACE</strong>
<small> </small>
</div>
<div class="data-node node-gov parallax-layer" data-layer-depth="0.18" data-layer-scroll="0.1"><span aria-hidden="true">01</span><strong>產業</strong><small>Industry</small></div>
<div class="data-node node-industry parallax-layer" data-layer-depth="0.22" data-layer-scroll="0.14"><span aria-hidden="true">02</span><strong>政府</strong><small>Government</small></div>
<div class="data-node node-research parallax-layer" data-layer-depth="0.17" data-layer-scroll="0.1"><span aria-hidden="true">03</span><strong>研究</strong><small>Research</small></div>
<div class="data-node node-service parallax-layer" data-layer-depth="0.2" data-layer-scroll="0.12"><span aria-hidden="true">04</span><strong>應用</strong><small>Service</small></div>



<div aria-hidden="true" class="data-beam beam-a parallax-layer" data-layer-depth="0.15"></div>
<div aria-hidden="true" class="data-beam beam-b parallax-layer" data-layer-depth="0.12"></div>
</div>
</div>
</section>
<section aria-labelledby="about-title" class="section about" id="about">
<div class="shell about-layout">
<div class="section-heading about-heading">
<p class="section-kicker">WHAT IS DATA SPACE</p>
<h2 id="about-title">什麼是資料空間？</h2>
<p class="section-lead">Data Space 是一個可信任的資料共享與應用環境。</p>
<p>資料空間（Data Space）是一種以資料自主、可信任共享、安全治理及跨域應用為核心的資料流通環境。透過共同的資料標準、共享規範、身分識別、權限管理及安全機制，不同組織可以在保有資料控制權的前提下，進行資料的共享、共享與應用。</p>
<p>它不是把所有資料集中到同一個資料庫，而是讓不同資料提供者與使用者，在可信任且可治理的條件下進行合作。</p>
<div class="about-actions">
<a aria-label="前往資料空間簡介內頁" class="button button-secondary" href="about.html">更多 <span aria-hidden="true">→</span></a>
</div>
</div>
<div aria-label="資料空間共享機制概念示意" class="exchange-card">
<div class="exchange-topline">
<span>DATA SPACE / EXCHANGE MODEL</span>
<b>LIVE</b>
</div>
<div class="exchange-scene">
<div class="exchange-party party-left">
<span aria-hidden="true" class="party-icon">A</span>
<strong>資料提供者</strong>
<small>保有資料控制權</small>
</div>
<div aria-hidden="true" class="exchange-center">
<span class="exchange-dash dash-1"></span>
<span class="exchange-dash dash-2"></span>
<span class="exchange-dash dash-3"></span>
<div class="exchange-hub">DDS</div>
<span class="exchange-dash dash-4"></span>
<span class="exchange-dash dash-5"></span>
<span class="exchange-dash dash-6"></span>
</div>
<div class="exchange-party party-right">
<span aria-hidden="true" class="party-icon">B</span>
<strong>資料使用者</strong>
<small>依授權條件使用</small>
</div>
</div>
<div class="exchange-rules">
<span>身分驗證</span><span>權限控管</span><span>共享紀錄</span><span>使用追蹤</span>
</div>
</div>
</div>
</section>
<section aria-labelledby="announcements-title" class="section announcements" id="announcements">
<div class="shell">
<div class="section-heading split-heading announcement-heading">
<div>
<p class="section-kicker">NEWS &amp; UPDATES</p>
<h2 id="announcements-title">公告消息</h2>
</div>
<div class="announcement-more">
<a aria-label="查看更多公告消息" class="button button-secondary" href="newslist.html?id=ALL">更多 <span aria-hidden="true">→</span></a>
</div>
</div>
<div class="announcement-list">
<article class="announcement-card">
<div class="announcement-meta"><time datetime="2026-08-06">2026.08.06</time></div>
<h3>臺灣無人機產業進軍日本，深化醫療與物流應用</h3>
<p>經濟部龔明鑫部長、駐日大使李逸洋今（15）日在日本東京見證「臺日先進空中運輸暨無人機系統產業技術合作」三項合作協議（MOU）：工研院（ITRI）及日本先進空中移動指標業者 SkyDrive 推動電動垂直起降飛行器</p>
<a aria-label="查看公告消息詳情" class="card-link" href="news-detail.html?id=4">查看詳細資料 <span aria-hidden="true">→</span></a>
</article>
<article class="announcement-card">
<div class="announcement-meta"><time datetime="2026-08-06">2026.08.06</time></div>
<h3>台灣卓越無人機海外商機聯盟大會師，展現臺灣無人機產業鏈整合實力</h3>
<p>台灣卓越無人機海外商機聯盟（TEDIBOA）今（9）日於台中舉辦「台灣卓越無人機海外商機聯盟大會師」活動，由賴清德總統親自出席，與經濟部何晉滄次長、聯盟成員以及現場超過百位產業代表，共同見證台灣無人機產業發展成果。經濟部表示，台灣推動無人機產業及非紅供應鏈發展的方向不會改變，雖然面對近期國防特別預算調整所帶來的挑戰</p>
<a class="card-link" href="news-detail.html?id=3">查看詳細資料 <span aria-hidden="true">→</span></a>
</article>
<article class="announcement-card">
<div class="announcement-meta"><time datetime="2026-08-15">2026.08.06</time></div>
<h3>金屬中心與美商Anduril簽署合作備忘錄，臺美攜手深化無人機關鍵技術合作</h3>
<p>經濟部今(3)日舉辦臺美無人機合作備忘錄簽署儀式，由金屬中心劉嘉茹董事長與美國國防科技大廠Anduril創辦人Palmer Luckey共同簽署，並由經濟部產發署邱求慧署長見證，未來雙方將以無人機關鍵技術為核心，聚焦AI自主系統、無人機製造、供應鏈在地化及建構非紅供應鏈等面向展開深度合作，進一步提升我國無人機產業的國際合作能量與全球供應鏈競爭力。</p>
<a class="card-link" href="news-detail.html?id=2">查看詳細資料 <span aria-hidden="true">→</span></a>
</article>
</div>
</div>
</section>
<section aria-labelledby="flow-title" class="section flow-section" id="data-flow">
<div class="shell">
<div class="section-heading centered light-heading">
<p class="section-kicker">FROM SILOS TO COLLABORATION</p>
<h2 id="flow-title">從資料孤島，走向資料協作</h2>
<p>Data Space 透過共同的治理與共享機制，在不犧牲資料自主權與安全性的前提下，實現資料流通與跨域合作。</p>
</div>
<div aria-label="資料分散、共享困難、信任不足、應用受限，透過 Data Space 轉化為跨域協作" class="problem-flow">
<article class="problem-card"><span>01</span><strong>資料分散</strong><p>資料分散於不同組織與資訊系統</p></article>
<i aria-hidden="true"> </i>
<article class="problem-card"><span>02</span><strong>共享困難</strong><p>格式、標準與介接方式不一致</p></article>
<i aria-hidden="true"> </i>
<article class="problem-card"><span>03</span><strong>信任不足</strong><p>資料提供者難掌握後續使用方式</p></article>
<i aria-hidden="true"> </i>
<article class="problem-card"><span>04</span><strong>應用受限</strong><p>跨組織資料難以整合與應用</p></article>
<div aria-hidden="true" class="flow-transform"><span>DATA SPACE</span><b>↓</b></div>
<article class="solution-card">
<div>
<span class="solution-label">TRUSTED DATA COLLABORATION</span>
<strong>可信任的跨域資料協作</strong>
</div>
<p>共同標準 × 身分識別 × 權限管理 × 安全共享 × 可追溯治理</p>
</article>
</div>
</div>
</section>
<section aria-labelledby="application-title" class="section application">
<div class="shell">
<div class="section-heading centered">
<p class="section-kicker">JOIN THE DATA SPACE</p>
<h2 id="application-title">加入資料空間，從這裡開始</h2>
<!--<p>先確認資格，再依申請流程完成資料與文件準備。</p>-->
<br>
<div>
<a aria-label="前往聯絡我們" class="button button-secondary" href="contact.html">聯絡我們</span></a>
</div>

</div>
<!--
<div class="application-grid">
<article class="application-card eligibility-card" id="eligibility">
<div class="application-index">01</div>
<div class="application-copy">
<span class="application-tag">ELIGIBILITY</span>
<h3>申請資格</h3>
<p>確認申請對象、基本條件與參與資料空間前應具備的治理及共享準備。</p>
<ul>
<li>申請對象與適用範圍</li>
<li>基本資料與組織條件</li>
<li>資料治理與安全準備</li>
</ul>

</div>
<div class="application-illustration">

<img alt="" height="420" src="images/replaceable/eligibility-illustration.png" width="640"/>
</div>
</article>
<article class="application-card process-card" id="process">
<div class="application-index">02</div>
<div class="application-copy">
<span class="application-tag">APPLICATION PROCESS</span>
<h3>申請流程</h3>
<p>依照申請階段完成資料盤點、文件準備、送件審查與後續加入作業。</p>
<ol class="mini-process">
<li><b>1</b><span>資料盤點</span></li>
<li><b>2</b><span>文件準備</span></li>
<li><b>3</b><span>申請送件</span></li>
<li><b>4</b><span>審查加入</span></li>
</ol>

</div>
<div class="application-illustration">

<img alt="" height="420" src="images/replaceable/process-illustration.png" width="640"/>
</div>
</article>
</div>
-->

</div>
</section>
<section aria-labelledby="security-title" class="section security" id="security">
<div class="shell security-layout">
<div class="section-heading security-heading">
<p class="section-kicker">SECURE DATA EXCHANGE</p>
<h2 id="security-title">資料空間的資料共享機制是否安全？</h2>
<p class="section-lead">安全，是資料流通的基礎</p>
<p>本資料空間透過多層次的安全與治理機制，建立可信任的資料共享環境。</p>
<p class="security-quote">讓資料可以流通，也讓資料的使用有跡可循。</p>
</div>
<ol aria-label="資料空間六項安全與治理機制" class="security-flow security-cards">
<li>
<img alt="" class="security-illustration" height="160" src="images/security/identity.png" width="240"/>
<div><strong>身分驗證</strong><p>確認資料提供者與使用者身分</p></div>
</li>
<li>
<img alt="" class="security-illustration" height="160" src="images/security/permission.png" width="240"/>
<div><strong>權限管理</strong><p>依照角色與授權條件控管資料使用</p></div>
</li>
<li>
<img alt="" class="security-illustration" height="160" src="images/security/governance.png" width="240"/>
<div><strong>資料治理</strong><p>建立資料標準、使用規範與管理機制</p></div>
</li>
<li>
<img alt="" class="security-illustration" height="160" src="images/security/exchange.png" width="240"/>
<div><strong>安全共享</strong><p>確保資料共享過程的安全性</p></div>
</li>
<li>
<img alt="" class="security-illustration" height="160" src="images/security/tracking.png" width="240"/>
<div><strong>使用追蹤</strong><p>保留資料使用與共享紀錄</p></div>
</li>
<li>
<img alt="" class="security-illustration" height="160" src="images/security/traceability.png" width="240"/>
<div><strong>責任可追溯</strong><p>建立完整的資料使用軌跡</p></div>
</li>
</ol>
</div>
</section>
</main>


<?php require_once 'footer.php';?>  


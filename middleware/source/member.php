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
require_once 'adm/config/product.php';
$currentPage = 'member';
require_once 'seonew.php';
require_once 'headerlogin.php';

echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='css/member.css?id=4'>\n";
?>
<main id="main" role="main">
<section aria-label="儀表板 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="儀表板 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">儀表板</h1>
</div>
</section>
<section aria-label="會員專區" class="detail-page-hero detail-intro-after-query">
<div class="member-container">
    <?php include 'sidebar.php'; ?>
<div class="content-area">
    <div class="dashboard-header">
        <h2>📊 儀表板</h2>
        <p>歡迎回來，<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>！</p>
    </div>
    <div class="dashboard-section">
        <div class="info-card">
            <h3>📌 本共享機制功能說明</h3>
            <p>本共享機制提供發布資料的管理與共享服務，您可以在這裡：</p>
            <ul>
                <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
                <li><strong>📦 我的發布資料</strong> － 建立、管理您自己的發布資料</li>
                <?php endif; ?>
                
                <?php if ($_SESSION['org_type'] == '資料使用者' || $_SESSION['org_type'] == '兩者皆有'): ?>
                <li><strong>🏪 目錄</strong> － 瀏覽並申請使用其他資料提供者提供的發布資料</li>
                <li><strong>📝 我的申請</strong> － 查看您申請使用發布資料的紀錄與狀態</li>
                <?php endif; ?>
                
                <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
                <li><strong>⚙️ 審核管理</strong> － 審核其他公司申請使用您的發布資料</li>
                <?php endif; ?>
                
                <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
                <li><strong>🤖 連接器管理</strong> － 管理您公司的連接器</li>
                <?php endif; ?>
                
                <?php if ($_SESSION['issuper'] == 2): ?>
                <li><strong>👤 自然人帳號管理</strong> － 管理您公司的帳號</li>
                <?php endif; ?>

                <?php if ($_SESSION['org_type'] == '資料使用者' || $_SESSION['org_type'] == '兩者皆有'): ?>
                <li><strong>📄 契約管理</strong> － 查看你已完成申請及使用的契約</li>
                <?php endif; ?>

                <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
                <li><strong>📋 訂閱管理</strong> － 查看誰訂閱了您的發布資料</li>
                <?php endif; ?>
               <!-- 
                <li><strong>🔑 個人密碼修改</strong> － 修改您的登入密碼</li>-->
            </ul>
        </div>
    </div>
    <div class="dashboard-section">
        <h3>🚀 快速開始</h3>
        <div class="quick-actions">
            <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
            <a href="my_products.html" class="quick-btn green-btn">📦 我的發布資料</a>
            <?php endif; ?>
            
            <?php if ($_SESSION['org_type'] == '資料使用者' || $_SESSION['org_type'] == '兩者皆有'): ?>
            <a href="product_market.html" class="quick-btn blue-btn">🏪 瀏覽目錄</a>
            <a href="my_applications.html" class="quick-btn gray-btn">📝 我的申請</a>
            <?php endif; ?>
            
            <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
            <a href="review_management.html" class="quick-btn orange-btn">⚙️ 審核管理</a>
            <?php endif; ?>

            <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
            <a href="machine_accounts.html" class="quick-btn gray-btn">🤖 連接器</a>
            <?php endif; ?> 

            <?php if ($_SESSION['org_type'] == '資料使用者' || $_SESSION['org_type'] == '兩者皆有'): ?>
            <a href="contracts.html" class="quick-btn blue-btn">📄 契約管理</a>
            <?php endif; ?>

            <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
            <a href="subscriptions.html" class="quick-btn orange-btn">📋 訂閱管理</a>
            <?php endif; ?>
        </div>
    </div>

</div>
</div>
</section>
</main>
<?php 
$pdo = null;
require_once 'footer.php';?>
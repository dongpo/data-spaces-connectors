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
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$mytable="`new_type`";
if (!ctype_digit($id) && $id !== 'ALL') {
    header("Location: 404.php");
    exit;
}
if (empty($id)){
	$id="ALL";
}else{
	if ($id>0 && $id !== 'ALL')
	{
		$cct=share_getcount($mytable." where isopen=1 and thisid=".$id);
		if ($cct>0){



		}else{
			header('Location: index.php');
			exit;		
		}

	}else{
		$id="ALL";	
	}
}


$tempall=share_getinfo("`sit_pages`","thispage",'newsseo');	
$mytype=share_gettable($mytable." where isopen=1 order by sorting desc");
if ($id=="ALL"){
	$mycheck=" where isopen=1 and  thisid>0";
	$mysort="listsorting";
	$temp=share_getinfo("`sit_pages`","thispage",'newsseo');	
}else{
	$temp=share_getinfo($mytable,"thisid",$id);
	$mysort="sorting";
	$mycheck=" where isopen=1 and  thisid>0 and typeid=".$id ;
}
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
if($GLOBALS['p']){
	$page=$GLOBALS['p'];
}else{
	$page=1;
}
if (!empty($GLOBALS['q']))
{
	$mycheck.=" and `thisname` like '%".$GLOBALS['q']."%'";
}	
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);
$listtable="new_list"; 
$cct=share_getcount($listtable.$mycheck);
$perpage=10;//一頁筆數
$pcct=ceil($cct/$perpage);

	$pagesize=1;
	$pagelen=9;	
    $pages = $pcct;//計算總分頁
    if($page<1) $page = 1;
    if($page>$pages) $page = $pages;
    //計算查詢偏移量
    $offset = $pagesize*($page-1);
    //頁碼範圍計算
    $init = 1;//起始頁碼數
    $max = $pages;//結束頁碼數
    $pagelen = ($pagelen%2)?$pagelen:$pagelen+1;//頁碼個數
    $pageoffset = ($pagelen-1)/2;//頁碼個數左右偏移量
    //分頁數大於頁碼個數時可以偏移
    if($pages>$pagelen){
        //如果當前頁小於等於左偏移
        if($page<=$pageoffset){
            $init=1;
            $max = $pagelen;
        }else{//如果當前頁大於左偏移
            //如果當前頁碼右偏移超出最大分頁數
            if($page+$pageoffset>=$pages+1){
                $init = $pages-$pagelen+1;
            }else{
                //左右偏移都存在時的計算
                $init = $page-$pageoffset;
                $max = $page+$pageoffset;
            }
        }
    }
$typelist=share_gettable($listtable.$mycheck."  order by mydate DESC limit ".(($page-1)*$perpage)." ,".$perpage );
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
<nav aria-label="麵包屑" class="breadcrumb intro-breadcrumb dds-breadcrumb">
<a aria-label="首頁" class="breadcrumb-home-link" href="index.html"><span aria-hidden="true" class="crumb-home-icon"></span></a>
<span aria-hidden="true">/</span>
<span aria-current="page">公告消息</span>
</nav>
<header class="dds-page-heading">
<p class="section-kicker">ANNOUNCEMENTS</p>
<h1>公告消息</h1>
</header>
</div>
</section>
<section aria-labelledby="news-list-title" class="dds-content-page news-page">
<div class="intro-container-1280">
<div class="news-panel">
<div aria-hidden="true" class="news-list-head"><span>標題</span><span>日期</span></div>


<div aria-live="polite" class="news-list" id="news-list">
<?php
// 建立分類對照表：thisid => thisname
$typeMap = [];
foreach($newmenu1 as $type){
    $typeMap[$type['thisid']] = $type['thisname'];
}
foreach($typelist as $l){

	$thisname = htmlspecialchars($l['thisname'], ENT_QUOTES, 'UTF-8');
    $mydate   = htmlspecialchars($l['mydate'], ENT_QUOTES, 'UTF-8');
    $thisid   = urlencode($l['thisid']); // 確保 URL 參數格式安全
    echo "<article class='news-item' data-title='{$thisname}' data-year='{$year}'>";
    echo "  <a href='news-detail.html?id={$thisid}'>";
    echo "    <span aria-hidden='true' class='news-mark'></span>";
    echo "    <span>{$thisname}</span>";
    echo "  </a>";
    echo "  <time datetime='{$mydate}'>{$mydate}</time>";
    echo "</article>";
	
}
?>

</div>
<div class="news-empty" hidden="" id="news-empty">查無符合條件的公告消息。</div>
<div class="news-list-meta"><span id="news-count">共 <?php echo $cct;?> 筆</span><span>每頁顯示 10 筆</span></div>
<nav aria-label="公告消息分頁" class="pagination" id="news-pagination"></nav>
<?php
if ($pages>1){
	echo "<a href='?page=1' class='pager-btn' data-page-action='prev'>第一頁</a>";
	for($i=$init;$i<=$max;$i++){
		echo "<a href='?page=".$i."' class='pager-btn";
		if($i == intval($page)){
			echo " active";
		}
		echo "' data-page='".$i."'>".$i."</a>";		

	}
	echo "<a href='?page=".$max."' class='pager-btn' data-page-action='next'>最後一頁</a>";
}

?>
</div>
</div>
</section>
</main>
<?php require_once 'footer.php';?>  
<script nonce="<?=$nonce?>">
$(document).ready(function(){
	$(".nav-inner a").removeAttr("aria-current");
	$(".mynews").attr("aria-current", "page");
});
</script>
<?php 
session_start();
require_once 'adm/config/config.php';
require_once 'adm/config/getformfp.php';
require_once 'adm/config/createimg.php';
require_once 'adm/config/share.php';


global $wedaddress,$imwebgurl;
$imgurl=$wedaddress.$imwebgurl;	
$URL=$wedaddress.$_SERVER['REQUEST_URI'];
$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);
require_once 'Share.php';//共用資料

// 檢查是否已登入
if (checkLogin()) {
    header("Location: member.html");
    exit;
}

$temp=share_getinfo("`sit_pages`","thispage",'indexseo');
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);

require_once 'seonew.php';
require_once 'headerlogin.php';
echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='css/backall.css?id=3'>\n";
echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='css/backcolor.css?id=3'>\n";
$_SESSION['nonce']=$nonce;
	function test_capcode($code){
		share_del("logtokens WHERE timekey<".(time()-20));
		if($t=share_getinfo("logtokens","tk",$code)){
			return "PASS";
		}else{
			return "驗證失敗,請重新驗證";
		}
	}
	function get_caplist(){
		echo "<div id='capchk' class='btn' ><i class='fa fa-square' aria-hidden='true' ></i>我不是機器人</div>";
?>
	<script nonce="<?php echo $_SESSION['nonce']; ?>">
	$(document).ready(function() {
		 $("#capchk").click(function(){
			$("#capchk").children("i").removeClass("fa-square");
			$("#capchk").children("i").addClass("fa-spinner");
			$("#capchk").children("i").addClass("fa-spin");
			var tempvals=Array('fds4wrewr');// 類別,ID	 
			tempitem=ajaxarr("aj_gettoken",tempvals,"ajax.php");
			tempitem.done(function(data){//
				if(data[0]=="OK"){
					$("#capchk").children("i").removeClass("fa-spin");
					$("#capchk").children("i").removeClass("fa-spinner");
					$("#capchk").children(".intk").remove();		
					$("#capchk").children("i").addClass("fa-check");
					$("#capchk").append("<input type='hidden' id='tk' name='csrf_token' value='"+data[1]+"' class='intk'>");
				}else{
					$("#capchk").children("i").add("fa-square");
					$("#capchk").children("i").removeClassClass("fa-spinner");
					$("#capchk").children("i").removeClassClass("fa-spin");
					alert("系統忙碌暫時無法驗證,請稍後再試試");
				}
			});
		 });
	});
	</script>
<?php
	}	
?>
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
<a href="login.html"><?php echo $temp['thisname'];?></a>
</nav>
<p class="eyebrow"></p>
<h1>參與者登入</h1>
<p></p>
</div>
</section>
<section aria-label="參與者登入" class="detail-page-hero detail-intro-after-query">
<div class="login-container">
    <div class="login-box">

        <?php 
$SMSkey = $_POST['SMSkey'] ?? $_GET['SMSkey'] ?? '';  
if($_SESSION['goSMSkey'] && $SMSkey<>""){
    $_SESSION['sms_attempts'] = ($_SESSION['sms_attempts'] ?? 0) + 1;    
    if($_SESSION['sms_attempts'] > 5) {
        $error_msg = "簡訊驗證碼嘗試次數過多，請重新登入";
        unset($_SESSION['goSMSkey']);
        unset($_SESSION['goSMSlock']);
        unset($_SESSION['tempname']);
        unset($_SESSION['sms_attempts']);
        unset($_SESSION['sms_send_time']);
        loginform1($error_msg);
        exit;
    }
    if(isset($_SESSION['sms_send_time']) && (time() - $_SESSION['sms_send_time'] > 300)) {
        $error_msg = "簡訊驗證碼已過期，請重新登入";
        unset($_SESSION['goSMSkey']);
        unset($_SESSION['goSMSlock']);
        unset($_SESSION['tempname']);
        unset($_SESSION['sms_attempts']);
        unset($_SESSION['sms_send_time']);
        loginform1($error_msg);
        exit;
    }    
	if($_SESSION['goSMSkey'] == $SMSkey){
		chkuser("OK",1);
		
		$user_id = $_SESSION['companyid'] ?? 0;
		$browser_code = '';
		if($user_id > 0) {
			$user = share_gettable("fun_user where thisid='".$user_id."'")[0];
			if(!empty($user['thisbid'])) {
				$browser_code = $user['thisbid'];
			}
		}
		?>
			<script nonce="<?=$nonce?>">
			<?php if(!empty($browser_code)): ?>
			// 儲存到 localStorage（永久儲存）
			localStorage.setItem("browser_code", "<?=$browser_code?>");
			console.log("已儲存 browser_code: <?=$browser_code?>");
			<?php endif; ?>

			<?php 
			unset($_SESSION['goSMSkey']);
			unset($_SESSION['goSMSlock']);
			unset($_SESSION['tempname']);
			unset($_SESSION['sms_attempts']);
			unset($_SESSION['sms_send_time']);
			unset($_SESSION['sms_pending_user']);
			?>

			setTimeout(function() {
				window.location.href = 'member.html';
			}, 500);
			</script>
		<?php
		exit;
	} else {
        $error_msg = "簡訊驗證碼錯誤，請重新輸入（剩餘嘗試次數：" . (5 - $_SESSION['sms_attempts']) . "次）";
        $_SESSION['sms_error'] = $error_msg;
        SMSform();
        exit;
    }
    
} else if($username<>"" && $password<>""){
    $req=test_capcode($_SESSION['tk']);
    if($req=="PASS"){ 
        $er=chkuser("",1);
    } else {
        $er=$req;
    }
}
		
if(!isset($_SESSION['companyuser'])){
     if(isset($_SESSION['goSMSlock'])){
       SMSform();
     }else{	   
 	     loginform1($er);
     }
}else{

	echo "<script nonce='".$_SESSION['nonce']."'>window.location.href='member.html';</script>";
	exit;
}

$pdo = null;
		?>

    </div>
</div>
</section>
</main> 
<?php require_once 'footer.php';?>  

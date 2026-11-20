<?php

$nonce = base64_encode(random_bytes(16)); // 生成隨機的 nonce
 

if (empty($_GET['tf'])) {
	header("Content-Security-Policy:  default-src 'self'; script-src 'self' https://*.googletagmanager.com https://*.googleapis.com https://*.gstatic.com https://maps.googleapis.com https://www.youtube.com 'nonce-{$nonce}'; style-src 'self' https://fonts.googleapis.com 'nonce-{$nonce}'; connect-src 'self' https://www.google-analytics.com https://maps.googleapis.com; img-src 'self' https://*.googleapis.com https://*.gstatic.com https://i.ytimg.com data: blob:;  font-src 'self' https://fonts.gstatic.com; frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com; frame-ancestors 'none'; form-action 'self'; ");				
}else{
	header("Content-Security-Policy:  default-src 'self'; script-src 'self' https://*.googletagmanager.com https://*.googleapis.com https://*.gstatic.com https://maps.googleapis.com https://www.youtube.com 'nonce-{$nonce}'; style-src 'self' 'unsafe-inline' ; connect-src 'self' https://www.google-analytics.com https://maps.googleapis.com; img-src 'self' https://*.googleapis.com https://*.gstatic.com https://i.ytimg.com data: blob:;  font-src 'self' https://fonts.gstatic.com; frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com; frame-ancestors 'none'; form-action 'self';");					   
					   
					   
}





ini_set('short_open_tag', '0');
set_time_limit(0);
date_default_timezone_set('Asia/Taipei');
$job="";
$username="";
$password="";
$tf="";
$er="";

$lanon=1;	//语言开启
session_start();
$_SESSION['nonce'] = $nonce;
if($lanon==1){
   $_SESSION['lan']="tw";//預設
}else{
	$_SESSION['lan']="";//預設
}
require_once 'config/config.php';
require_once 'config/getformin.php';
require_once 'config/createimg.php';
require_once 'config/share.php';
require_once 'config/menu.php';
require_once 'config/product.php';
require_once '../phpmailer/mailer/sendmail.php'; 
$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);

$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);





if($_SESSION['intime']){
}else{
	$_SESSION['intime']=date("Y/m/d H:i");
}
if (isset($_REQUEST['_SESSION'])) die("Get lost Muppet!");
if (ini_get('register_globals'))
 {
     foreach ($_SESSION as $key=>$value)
     {
         if (isset($GLOBALS[$key]))
             unset($GLOBALS[$key]);
     }
 }
 $reSMS = $reSMS ?? '';
 //  SMS 重新來
 if($reSMS=="12x653"){
   $_SESSION['goSMSlock']=null;
   $_SESSION['tempname']=null;
   $_SESSION['goSMSkey']=null;
 } 
 
 if($job=="sms"){
	 
	share_update("fun_user","locking=0","thisid='".$GLOBALS['uid']."'");
	
	header("Location: index.php");
	exit();
 }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<title><?=$rootcompanyname ?>-BackStage</title>
<link nonce="<?=$nonce?>" rel="stylesheet" type="text/css" href="css/global2.css?id=4" />
<link nonce="<?=$nonce?>" href="css/fontawesome/css/font-awesome.min.css" rel="stylesheet">
<link nonce="<?=$nonce?>" rel="stylesheet" type="text/css" href="js/jquery-ui-1.13.2/jquery-ui.css" />
<link nonce="<?=$nonce?>" rel="stylesheet" type="text/css" href="js/jquery.datetimepicker.min.css" />
<link nonce="<?=$nonce?>" rel="stylesheet" type="text/css" href="css/backall.css?id=1120" />
<link nonce="<?=$nonce?>" rel="stylesheet" type="text/css" href="css/backcolor.css?id=6" />





<script nonce="<?=$nonce?>" type="text/javascript" src="js/jquery-3.7.1.min.js?id=8"></script>
<script nonce="<?=$nonce?>" type="text/javascript" src="js/jquery-ui-1.13.2/jquery-ui.min.js"></script>
<script nonce="<?=$nonce?>" type="text/javascript" src="js/jquery.datetimepicker.full.min.js"></script>
<script nonce="<?=$nonce?>" type="text/javascript" src="js/showlist.js"></script>
<script nonce="<?=$nonce?>" type="text/javascript" src="js/back.js?id=2"></script>
<script nonce="<?=$nonce?>" type="text/javascript" src="js/share.js"></script>
<script nonce="<?=$nonce?>" type="text/javascript" src="js/bkshare.js?id=2"></script>

<script nonce="<?=$nonce?>"  >
$(document).ready(function() {
   $("input").attr('autocomplete', 'off');
	 $(".showfpasswrap").click(function(){
		$(".fpasswrap").show();
	 });
   if(localStorage.getItem("SMSkey") && $("#browserlocker").length>0){
     $("#browserlocker").val(localStorage.getItem("SMSkey"));
   }	 	 
   /*
   $( ".dateitem" ).datepicker({
       dateFormat: 'yy-mm-dd',
   });
   */
   //jQuery.datetimepicker.setLocale('en');
  // $('.dateitem').datetimepicker({format:'Y-m-d H'});
  // $('.houritem').datetimepicker({datepicker:false,format:'H'});
   /*
   $( ".dateitem" ).datetimepicker({

   });
   */
});
</script>
<script type="text/javascript" nonce="<?=$nonce?>"  >
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}
</script>

<?php
require_once 'capnew.php'; 
?>
</head>
<body>
<?php 

// 接收表單資料
$SMSkey = $_POST['SMSkey'] ?? $_GET['SMSkey'] ?? '';  // ✅ 正確  // ✅ 統一用 $SMSkey

if($_SESSION['goSMSkey'] && $SMSkey<>""){//進入簡訊
    
    //  檢查嘗試次數 
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
    
    //  檢查驗證碼是否過期（5分鐘） 
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
		chkuser("OK",0);
		
		$user_id = $_SESSION['admuserid'] ?? 0;
		$browser_code = '';
		if($user_id > 0) {
			$user = share_gettable("fun_user where thisid='".$user_id."'")[0];
			if(!empty($user['thisbid'])) {
				$browser_code = $user['thisbid'];
			}
		}
    //  除錯：寫入 log 
    //error_log("=== 簡訊驗證成功 ===");
    //error_log("user_id: " . $user_id);
    //error_log("browser_code: " . $browser_code);
    //error_log("SESSION new_browser_code: " . ($_SESSION['new_browser_code'] ?? '空'));
    // ===		
		
		?>
			<script nonce="<?=$nonce?>">
			<?php if(!empty($browser_code)): ?>
			// 儲存到 localStorage（永久儲存）
			localStorage.setItem("browser_code", "<?=$browser_code?>");
//			console.log("已儲存 browser_code: <?=$browser_code?>");
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
				window.location.href = 'index.php';
			}, 500);
			</script>
		<?php
		exit;
	} else {
        //  驗證碼錯誤處理 
        $error_msg = "簡訊驗證碼錯誤，請重新輸入（剩餘嘗試次數：" . (5 - $_SESSION['sms_attempts']) . "次）";
        $_SESSION['sms_error'] = $error_msg;
        SMSform();
        exit;
    }
    
} else if($username<>"" && $password<>""){
    // 一般帳號密碼登入
    $req=test_capcode($_SESSION['tk']);
    if($req=="PASS"){ 
        $er=chkuser("",0);
    } else {
        $er=$req;
    }
} elseif($job=="logout"){
    if($_SESSION['issuper']==1){
    } else {
        // share_insert("loginlog","userid,ip,job","'".$_SESSION['admuserid']."','".GetIP()."','登出'");
    }
    unset($_SESSION['tk']);
    unset($_SESSION['admuser']);
    unset($_SESSION['rights']);
    unset($_SESSION['admuserid']);
    unset($_SESSION['issuper']);
    unset($_SESSION['thisid']);
	unset($_SESSION['athisenttype']);
	unset($_SESSION['athisentid']);	
}
//var_dump($_SESSION);		  
//echo "<BR>	admuser=".$_SESSION['admuser'];		  
//echo "<BR>1";				
                   if(!isset($_SESSION['admuser'])){
//echo "<BR>2";	  
//echo "<BR>goSMSkey=".$_SESSION['goSMSkey']; 
                       if(isset($_SESSION['goSMSlock'])){
//echo "<BR>3";						   
                         SMSform();
                       }else{
//echo "<BR>4";						   
                         //echo $er;
 						     loginform1($er);
                       }
}else{
//	echo "<BR>5";	
?>
<DIV id=wrapper>
    <div class="nav-box">
        <div class="nav-logo"><a href='?tf=select0'><img src='img/logo_0.png?id=2' class="w100" /></a></div>
        <div>
          <h2 class="indexh2">位於：<?=$sitetype?></h2>
        </div>
        <div class="nav-login">
        <?php

          if(isset($_SESSION['admuser'])){
			    echo '<div class="tagbodyin">';
                echo '<table width="180" border="0" cellspacing="0" cellpadding="0">';
                echo '      <tr>';
                echo '        <td><p><font size="+1">';
        				if(date("H")<6){
        					echo "Good Night";
        				}else if(date("H")<12){
        					echo "Morning";
        				}else if(date("H")<13){
        					echo "Noon";
        				}else if(date("H")<18){
        					echo "Afternoon";
        				}else{
        					echo "Good Night";
        				}
				        echo ',</font></h2> '.$_SESSION['admuser'].'</p> </td>';
                echo '      </tr>';
        /*        echo '      <tr>';
                echo '        <td >DB:'.$dbname.'</td>';
                echo '      </tr>';
                echo '      <tr>';
                echo '        <td >Start Time:'.$_SESSION['intime'].'</td>';
                echo '      </tr>';*/
                echo '      <tr>';
                echo '        <td >已在線:'.round(((time()-strtotime($_SESSION['intime']))/60)).'minutes</td>';
                echo '      </tr>';
                echo '      <tr>';
                echo '        <td class="logout5"><a href="?job=logout" class="gray-btn">登出</a></td>';
                echo '      </tr>';	
                echo '    </table>';
				echo ' </div>';
            }
		?>
	
        </div>
        <div class="operate">
                <ul class="J_navlist">
                <?php
                if(isset($_SESSION['admuser'])){
                    getlist(0);
                }
                ?>
                </ul>
        </div>
    </div>
    <div class="nav-boxm shadow3">
    	<div class="nav-boxmin">
            <div class='logout1'><a href=''><img src='img/logo_0.png?id=2'  class='fL indexlogo' /></a></div>
            <div class='fR logout2' >
                <i class="navclick fR fa fa-bars logout4" ></i>
                <div class='fR logout3' >
                <?php
                if(isset($_SESSION['admuser'])){
                	echo "<a href='?job=logout' class='gray-btn'>登出</a></td>";
                }
                ?>
                </div>
            </div>
        </div>
        <ul class="J_navlist shadow">
        	<?php
            if(isset($_SESSION['admuser'])){
            	getlist(0); 
            }
        	?>
        </ul>
    </div>
    <div id='contentwrap' >
        <div class="content">
        <!--内容-->
                <?php
                   if(!isset($_SESSION['admuser'])){
	   
					 if(isset($_SESSION['goSMSlock'])){
					   SMSform();
					 }else{
					    //echo $er;
						loginform1($er);
					 }
                   }else{
					   

					   
                       if($tf=="" or $tf=="select0"){
                           require_once"select0.php";
                       }else{
                               if(isset($change) && $change=="en"){
                                   $_SESSION['lan']="en";
                               }else if(isset($change) && $change=="ch"){
                                   $_SESSION['lan']="ch";
                               }
                               if(isset($_SESSION['issuper'])==1){
                                    $temp=$tf.".php";
                                    include($temp);
                               }else if(isset($_SESSION['rights'])){
                                   $pos = strpos($_SESSION['rights'], $tf."-");
                                   if($pos>0){
                                        $temp=$tf.".php";
                                        include($temp);
                                   }else{
                                        echo "Sorry, You don't have right to use this function";
                                   }
                               }else{
                                   echo "Sorry，I can't retrive your function list";
                               }
                       }
                   }
                ?>

        <!--内容 END -->
        <div class='clr'></div>
        </div>
        <div class='clr'></div>
	</div>
</div>



<?php
}
?>
</body>
</html>
<script  nonce="<?=$nonce?>">
$(document).ready(function() {
	var err="<?=$returnerr?>";
	if(err){
		poperr(err);
	}
});
</script>
<?php
$pdo = null;
?>

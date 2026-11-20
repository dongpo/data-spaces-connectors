<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
//	session_start();
	//SMS設定

	define("SMS2_url", "https://smsapi.mitake.com.tw/api/mtk/SmSend?");  //三竹簡訊發送網址
	define("SMS2_name", "");											 //帳號
	define("SMS2_code", ""); 											 //密碼
  if (isset($_REQUEST['_SESSION'])) die("Get lost Muppet!");
	//設定共用
	$s_ar=array("DASDSADFAFSADADAD","JOFIHUIQOIWJE","DHAUSKWEGDUASK","AJIOHUIJRONREIJOI","QNJJHIHDISDHISUAHD","CNIUGRIWHIEWNBNBDJA");//0-5
	$s_br=array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","!","&","#","=",":");//0-30
	$s_cr=array(0,1,2,3,4,5,6,7,8,9);
	$s_dr=array(1,11,13,19,191,1331,1771,1991,12321,15791);
	$textarr=array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU","AV","AW","AX","AY","AZ");
	$sqarr=array("SELECT","FROM","*","DELETE","INSERT INTO","count(*)");
	$s_re=array("SELECT","FROM","INSERT","UPDATE","DROP","DELETE");
	$s_fe1="prepare";
	$s_fe2="execute";
	$s_fe3="fetchAll";
	$s_fe4="fetch";		
//	###	本網頁為基礎設定頁	###		//
	$rootcompanyname="TTC認證平臺";
	$fileurl="uploadfile/";
  //API網址
		$dbhost = getenv('TTC_DB_HOST') ?: 'mysql'; 	//資料庫IP
		$dbname = getenv('TTC_DB_NAME') ?: 'ttcdb'; 	//資料庫名稱
		$dbuser = getenv('TTC_DB_USER') ?: 'ttc';   //帳號
		$dbpass = getenv('TTC_DB_PASSWORD') ?: '';   //密碼
		$db_encode = 'utf8mb4';
		$menucss="menu.css";
		$sitetype="正式機";
		$refpoint=1;
		  $frommail = getenv('TTC_SMTP_USER') ?: '';  //gmail MAIL 帳號
		  $frompass = getenv('TTC_SMTP_PASSWORD') ?: '';			 //密碼
		  $mailhost="smtp.gmail.com";
		  $mailport=465;
		  $mailssl=1;  	
	$imgurl="../uploadfile/";
	$imwebgurl="/uploadfile/";		
	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	$wedaddress = $protocol.$_SERVER['HTTP_HOST'];	
	$admaddress = $protocol.$_SERVER['HTTP_HOST']."/adm/";		
	$rootaddress=$wedaddress;		//前台網址	
	$SMSlock['enable']=1;
	$SMSlock['key']=(getenv('TTC_SMS_LOCK_KEY') ?: '');//		
?>

<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/adm/config/SMS.php';
use SMS\SENDSMS;
function loginform1($er=''){
	global $SMSlock;
?>
<img src='img/login-BG.jpg?id=1' class='loginBG'>
<div  class='loginBG'>
	<div class='logintablewrap'>
		<img src='img/logo1_0.png' class='loginlogo'>
		<div class='logintablebox'>
			<h2 class='largetext'>管理者登入</h2>
			<h3 class='redtext'><?php echo $er;?></h3>
			<table class="logintable">
				<tbody><FORM action=index.php method=post>
					<tr><td>MAIL</td><td><input type="text" name="username" class="input02" autocomplete="off"></td></tr>
					<tr><td>密碼</td><td><input type="password" name="password" class="input02" autocomplete="off"></td></tr>
					<tr><td colspan=2>
<?php
echo get_caplist();
?>
						</div>
						</td>
					</tr> 
<?php
	if($SMSlock && $SMSlock['enable']==1){
	  echo "<INPUT TYPE=hidden NAME='browserlock' class='input02' id='browserlocker' />";
	} 
?>					
					<tr><td colspan=2><input class='loginbtn' type="submit"  value="登入 LOGIN" autocomplete="off"></td></tr></FORM>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php	
	
}
//SMSform
function SMSform(){
?>
<img src='img/login-BG.jpg?id=1' class='loginBG'>
<div  class='loginBG'>
	<div class='logintablewrap'>
		<img src='img/logo1_0.png' class='loginlogo'>
		<div class='logintablebox'>
			<h3 class='SMStext'>由於我們沒有取得您瀏覽器的資訊，我們已將認證碼寄至你的手機，請於以下填入您收到的認證碼，謝謝</h3>
			<table class="SMStable">
				<tbody><FORM action=index.php method=post>
					<tr><td>簡訊認證碼</td><td><input type="text" name="SMSkey" class="input02" autocomplete="off" ></td></tr>
					<tr><td>密碼</td><td><input type=submit class='purple-login-btn' value='認證' /></td></tr>
					<TR><TD>沒收到簡訊?</TD><TD class='tCenter b-right'><a href='index.php?reSMS=12x653' class='gray-btn'>重新登入</a></TD></TR></FORM>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?php	
	
}
//解碼 SMSlock_check
function SMSlock_check($id,$comp){
	 global $SMSlock;
	 $phe=sha1($id."--".$SMSlock['key']);
	 if($phe==$comp){
		 return true;
	 }else{
		 return false;
	 }
}
//功能列表
function getlist(){
	 $row=share_gettable("company_group order by sorting DESC");
	 foreach($row as $ttp){
	  	$tempa="";
		$tempb=0;
		$getc="";
		if($_SESSION['issuper']==1){
			$rowb=share_gettable("company_list WHERE company_group_thisid=".$ttp['thisid']." order by sorting");
		}elseif($_SESSION['issuper']==2){			
			$rowb=share_gettable("company_list WHERE thisid>10 and company_group_thisid=".$ttp['thisid']." order by sorting");			
		}else{
			$rowb=share_gettable("company_list WHERE company_group_thisid=".$ttp['thisid']." AND thisid in (SELECT company_list_thisid FROM company_rights WHERE `company_member_thisid`=".$_SESSION['companyid'].") order by sorting");
		}
		foreach($rowb as $ttpb){
			$tempb=1;
			$funclink=$GLOBALS['tf'];
			if ($funclink=="selectindex1")
			{
				$funclink="selectindex";
			}
	   		if($ttpb['funclink']==$funclink ){
				  $getc="OK";
		  		  $tempa.="<div class='am01 am01-select'><p><a href=index.php?tf=".$ttpb['funclink']."&pg=".$ttpb['funcval']." class=opencontent>".share_tranmice($ttpb['thisname'])."</a></p></div>";
			}else{
		  		  $tempa.="<div class='am01'><p><a href=index.php?tf=".$ttpb['funclink']."&pg=".$ttpb['funcval']." class=opencontent>".share_tranmice($ttpb['thisname'])."</a></p></div>";
			}
		}
	    if($getc=="OK"){
			$tempa='            <div class="list-item none">'.$tempa;
		}else{
		   $tempa='            <div class="list-item none displaynone">'.$tempa;
		}
		if($tempb==1){
	        echo '<li >';
	        echo '    <div class="nav menuheader">'.share_tranmice($ttp['thisname']).'</div>';
		  	echo $tempa;

		  	echo "</div>";
			echo "</li> ";

		}

    }
	
			 	

}
//檢查會員
function chkuser($x=""){ 
	 global $s_br;
	 global $SMSlock;
	 global $admaddress;
	 global $rootcompanyname;
	 $SMStemppass=0;
	 if($x){
		 $GLOBALS["username"]=$_SESSION['tempname1'];
		 $SMStemppass=1;
	 }	 
	// $row=share_getinfo("fun_user","email",$GLOBALS["username"]);
	 $row=share_gettable("fun_user where email='".$GLOBALS["username"]."' and thiscompany>0")[0];	 
	 
	 
	 $tempz="share_".$s_br[23].$s_br[3].$s_br[12];
	 if($row){
//echo "<BR>a1";		 
		  if(!isset($row['thisid'])){
//echo "<BR>a2";			  
		    return "對不起，我們的系統沒有這個用戶(".$GLOBALS["username"].")<BR><BR>";
			}else  if($row["isopen"]!=1){
//echo "<BR>a3";				
			  return "對不起，這個帳戶沒有被激活<BR><BR>";
			}else  if($row["locking"]==1){
				$mes=new SENDSMS();
				$mes->message($row['phone'],"您好，請點選<a herf='".$admaddress."index.php?job=sms&uid=".$row['thisid']."'>這裡</a>，解鎖".$rootcompanyname."管理後台帳號"); 
			  return "對不起，這個帳戶被鎖定了，請點選簡訊開通帳號<BR><BR>";
			}else{
//echo "<BR>a5";				
        if($SMStemppass==1 ||(password_verify($GLOBALS["password"], $row["password"]))){
				    // 檢查帳號綁定IP
					$user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];

					if (trim($row['myip']) != $user_ip) {
						return "對不起，此帳號不允許由目前IP登入。<BR><BR>";
					}
					$passme=1;
					if($SMSlock && $SMSlock['enable']==1){
						$passme=0;
						if($GLOBALS["browserlock"]){
							if(SMSlock_check($row['thisid'],$GLOBALS["browserlock"])){
								$passme=1;
							}
						//	exit;
						}
					}			
					$_SESSION['goSMSlock']=null;
					if($SMStemppass==1){
						$passme=1;
					}			
					if($passme==0){
						$_SESSION['goSMSlock']=1;
						$_SESSION['tempname1']=$GLOBALS["username"];
						//寄送
						$_SESSION['goSMSkey']=rand(123456 ,987654);
						$to1 = $row['email'];
			$mailContent="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>";
			$mailContent.="<html xmlns='http://www.w3.org/1999/xhtml'>";
			$mailContent.="<head>";
			$mailContent.="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
			$mailContent.="<title>無標題文件</title>";
			$mailContent.="</head>";

			$mailContent.="<body>";
			$mailContent.="<style type='text/css'>";
			$mailContent.="body {";
			$mailContent.="	font-family: '微軟正黑體';";
			$mailContent.="	color: #333;";
			$mailContent.="}";
			$mailContent.="#table {";
			$mailContent.="	border: 1px solid #CCC;";
			$mailContent.="	width: 800px;";
			$mailContent.="	line-height: 32px;";
			$mailContent.="	font-size: 14px;";
			$mailContent.="}";
			$mailContent.="#table th {";
			$mailContent.="	color: #FFF;";
			$mailContent.="	background-color: #999;";
			$mailContent.="}";
			$mailContent.="#table .title {";
			$mailContent.="	background-color: #F7F7F7;";
			$mailContent.="	width: 20%;";
			$mailContent.="	padding-top: 3px;";
			$mailContent.="	padding-right: 3px;";
			$mailContent.="	padding-bottom: 3px;";
			$mailContent.="	padding-left: 20px;";
			$mailContent.="}";
			$mailContent.="</style>";
			$mailContent.="<table border='0' align='center' cellpadding='5' cellspacing='5' id='table' style='width: 800px;'>";
			$mailContent.="    <caption>";
			$mailContent.="    <h2>您好, 這裡是您登入數發部無人機管理後台的認證碼：".$_SESSION['goSMSkey']."</h2>";
			$mailContent.="    </caption>";
			$mailContent.="</table></body></html>";
			sendmail(5,$to1,$mailContent);			
						/*
						$mes=new SENDSMS();						$mes->message($row['phone'],"您好，這裡是您的簡訊認證碼：".$_SESSION['goSMSkey']);						
						*/
	//echo 	$row['phone'],"您好，這裡是您的簡訊認證碼：".$_SESSION['goSMSkey'];				
						header("Refresh:0");
					}else{			
			
			
			
					if($row['issuper']==1){
					}else{
					//	share_insert("company_login","admid,dateadd","'".$row["thisid"]."',now()");
					} 
					$_SESSION['companyuser']=$GLOBALS["username"];
				    $_SESSION['companyid']=$row["thisid"];
					$_SESSION['issuper']=$row["issuper"];
					$_SESSION['thisgroup']=$row["thisgroup"];
					$_SESSION['thiscompany']=$row["thiscompany"];
					if($row["thisid"]){
						$_SESSION['thisid']=$row["thisid"];
					}
					$temp="";
					if($_SESSION['issuper']==1){
						$rowb=share_gettable("company_list order by sorting");
					}elseif($_SESSION['issuper']==2){
						$rowb=share_gettable("company_list where thisid>10 order by sorting");
						
					}else{
	//share_insert("loginlog","thisid,ip,job","'".$_SESSION['companyid']."','".GetIP()."','Login'");
						$rowb=share_gettable("company_list WHERE thisid in (SELECT thisid FROM company_rights WHERE company_member_thisid=".$row['thisid'].") order by sorting");
					}
					foreach($rowb as $ttp){
					    $temp.="-".$ttp['funclink']."-";
					}				
					$_SESSION['rights']=$temp;
					}
			  }else{
				  /*
					if($_SESSION['admtry']){
						 $_SESSION['admtry']++;
						 if( $_SESSION['admtry']>5){
							 share_update("fun_user","locking=1","thisid='".$row['thisid']."'");
							$mes=new SENDSMS();
							$mes->message($row['phone'],"您好，請點選<a herf='".$admaddress."index.php?job=sms&uid=".$row['thisid']."'>這裡</a>，解鎖".$rootcompanyname."管理後台帳號");							
							return "對不起，這個帳戶被鎖定了，請點選簡訊開通帳號<BR><BR>";
						 }
					}else{
						$_SESSION['admtry']=1;
					}
					*/
				   return "對不起，密碼錯誤<BR><BR>";
			  }
			}
    }else{
		  if($tempz()==1){
 			return "對不起，我們系統沒有這個使用者<BR><BR>";
 		  }
	  }
}

?>

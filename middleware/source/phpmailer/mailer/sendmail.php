<?php

//$rootaddress='sss';

	function sendmail($x,$y,$z){//$x==編號,$y==email,$z==其他
		global $rootaddress;
		$sign="";
		if($x=="10"){
			// $z 是陣列 [token, 組織名稱, 使用者名稱]
			$token = $z[0];
			$org_name = isset($z[1]) ? $z[1] : "";
			$user_name = isset($z[2]) ? $z[2] : "管理者";
			$link = $rootaddress."/activate.html?token=".$token;
			
			$receivername = $user_name;
			$subject = "【分散式資料空間機制】管理者帳號啟用信";
			$contents = "親愛的 ".$user_name." 您好：<br><br>";
			$contents .= "您已被設定為 <strong>".$org_name."</strong> 的管理員。<br><br>";
			$contents .= "請點選以下連結設定您的密碼：<br>";
			$contents .= "<a href='".$link."'>".$link."</a><br><br>";
			$contents .= "此連結將於 7 天後失效。<br><br>";
			$contents .= "若您沒有申請此帳號，請忽略此信件。<br><br>";
			$contents .= "此信件為系統自動發送，請勿直接回覆。";
			$contents .= $sign;
		}
		phpmail($y,$receivername,$subject,$contents);
	}
	
	function defmail(){
	}
	function phpmail($receiver,$receivername,$subject,$contents){

		global $frommail;
		global $frompass;
		global $mailhost;
		global $mailport;
		global $mailssl;
		global $conf;

	 
		mb_internal_encoding('UTF-8');    // 內部預設編碼改為UTF-8
	
		require_once 'PHPMailerAutoload.php';
		
		//require("class.phpmailer.php");
		//$mail = new PHPMailer();
		$mail = new PHPMailer;
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		if($mailssl==1){
			$mail->SMTPSecure = "ssl"; 
		}
		
		$mail->Host = $mailhost;
		$mail->Port = $mailport;
		$mail->CharSet = "utf-8";
		$mail->Encoding = "base64";
		$mail->Username = $frommail;
		$mail->Password = $frompass;
		$mail->FromName = $frommail;
		$mail->From = $frommail;
		$mail->AddAddress($receiver,$receivername);
		$mail->AddReplyTo($frommail,"自動寄件人-請勿回覆");
		$mail->WordWrap = 50;
		$mail->IsHTML(true); // send as HTML
		$mail->Subject = mb_encode_mimeheader($subject, "UTF-8");
		$mail->Body = $contents;
		$mail->AltBody = $contents; 
		if($mail->Send()){
			return true;
		}else{
			return false;
		}			
	}
	

?>
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/adm/config/SMS.php';
use SMS\SENDSMS;
 
function loginform1($er=''){
    global $SMSlock, $nonce; 
    ?>
    <img src='img/login-BG.jpg?id=2' class='loginBG'>
    <div  class='loginBG'>
        <div class='logintablewrap'>
            <img src='img/logo2_0.png?id=2' class='loginlogo'>
            <div class='logintablebox'>
                <h2 class='largetext'>登入</h2>
                <h3 class='redtext'><?php echo $er;?></h3>
                <table class="logintable">
                    <tbody><FORM action=index.php method=post>
                        <tr><td>帳號</td><td><input type="text" name="username" class="input02" autocomplete="off"></td></tr>
                        <tr><td>密碼</td><td><input type="password" name="password" class="input02" autocomplete="off"></td></tr>
                        <tr><td colspan=2>
                            <?php echo get_caplist(); ?>
                        </td></tr> 
                        <?php
                        if($SMSlock && $SMSlock['enable']==1){
                            ?>
                            <tr>
                                <td colspan=2>
									<INPUT TYPE="hidden" NAME="browserlock" class="input02" id="browserlocker" />
  <script nonce="<?php echo $nonce; ?>">
 //               console.log("=== loginform1 讀取 browser_code ===");
                
                // 從 localStorage 讀取（永久儲存）
                var browserCode = localStorage.getItem("browser_code");
  //              console.log("localStorage browser_code: " + browserCode);
                
                var input = document.getElementById("browserlocker");
                if (browserCode && input) {
                    input.value = browserCode;
   //                 console.log("已設定 browserlocker value: " + input.value);
                } else {
   //                 console.log("browserCode 為空");
                }
                </script>
                                </td>
                            </tr>
                            <?php
                        } 
                        ?>					
                        <tr><td colspan=2><input class='loginbtn' type="submit"  value="登入" autocomplete="off"></td></tr>
                    </FORM>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php	
}

//SMSform
//SMSform
function SMSform(){
    global $nonce;
    
    $error = $_SESSION['sms_error'] ?? '';
    unset($_SESSION['sms_error']);
    
    $attempts = $_SESSION['sms_attempts'] ?? 0;
    $remaining = 5 - $attempts;
    ?>
    <style nonce="<?php echo $nonce; ?>">
    .sms-error {
        color: red;
        text-align: center;
        margin: 10px 0;
    }
    .sms-attempts {
        text-align: center;
        font-size: 12px;
        color: #999;
        margin: 5px 0 15px 0;
    }
    .sms-submit-row {
        text-align: center;
        padding: 10px 0;
    }
    .sms-resend-row {
        text-align: center;
        padding: 5px 0;
    }
    </style> 
    
    <img src='img/login-BG.jpg?id=2' class='loginBG'>
    <div class='loginBG'>
        <div class='logintablewrap'>
            <img src='img/logo2_0.png?id=2' class='loginlogo'>
            <div class='logintablebox'>
                <h3 class='SMStext'>由於我們沒有取得您瀏覽器的資訊，我們已將認證碼寄至你的手機，請於以下填入您收到的認證碼，謝謝</h3>
                
                <?php if(!empty($error)): ?>
                <h3 class='redtext sms-error'><?php echo htmlspecialchars($error);?></h3>
                <?php endif; ?>
                
                <?php if($remaining > 0): ?>
                <p class='sms-attempts'>剩餘嘗試次數：<?php echo $remaining;?> 次</p>
                <?php endif; ?>
                
                <table class="SMStable">
                    <tbody>
                        <FORM action=index.php method=post>
                            <!-- ========== 新增：隱藏欄位，傳遞 browserlock ========== -->
                            <INPUT TYPE="hidden" NAME="browserlock" id="sms_browserlocker" />
                            
                            <tr>
                                <td>簡訊認證碼</td>
                                <td><input type="text" name="SMSkey" class="input02" autocomplete="off" maxlength="6" value=''/></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="sms-submit-row">
                                    <input type="submit" class='purple-login-btn' value='認證' />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="sms-resend-row">
                                    <a href='index.php?reSMS=12x653' class='gray-btn'>重新登入</a>
                                </td>
                            </tr>
                        </FORM>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
<script nonce="<?php echo $nonce; ?>">
(function() {
    try {
        var browserCode = localStorage.getItem("browser_code");
        
        // 如果本機沒有，自動幫這個新瀏覽器產生一組專屬代碼
        if (!browserCode) {
            browserCode = 'browser_' + Math.random().toString(36).substring(2) + Date.now().toString(36);
            localStorage.setItem("browser_code", browserCode);
        }
        
        var input = document.getElementById("sms_browserlocker");
        if (input) {
            input.value = browserCode;
        }
    } catch(e) {}
})();
</script>
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
function getlist($x=0){
  if ($x==0){	
	
    $row=share_gettable("fun_group order by sorting DESC");
    foreach($row as $ttp){
        $tempa="";
        $tempb=0;
        $getc="";
        if($_SESSION['issuper']==1){
            $rowb=share_gettable("fun_list WHERE fun_group_thisid=".$ttp['thisid']." order by sorting");
        }else{
            $rowb=share_gettable("fun_list WHERE fun_group_thisid=".$ttp['thisid']." AND thisid in (SELECT fun_list_thisid FROM fun_rights WHERE `fun_user_thisid`=".$_SESSION['admuserid'].") order by sorting");
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
  }else{
    $row=share_gettable("company_group order by sorting DESC");
    foreach($row as $ttp){
        $tempa="";
        $tempb=0;
        $getc="";
		$rowb=share_gettable("company_list WHERE company_group_thisid=".$ttp['thisid']." order by sorting");
        foreach($rowb as $ttpb){
            $tempb=1;
            $funclink=$GLOBALS['tf'];
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
	
	
}


function generateBrowserCode($user_id) {
    $salt = (getenv('TTC_AUTH_SALT') ?: ''); // 建議從設定檔讀取
    $random = bin2hex(random_bytes(16));
    $timestamp = time();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $code = $user_id . '_' . $random . '_' . sha1($timestamp . $salt . $user_agent);
    return $code;
}


function saveBrowserCode($user_id, $browser_code) {
    if(empty($user_id) || empty($browser_code)) {
        return false;
    }
    
    $now = date('Y-m-d H:i:s');
    return share_update("fun_user","thisbid='".addslashes($browser_code)."'", "thisid='".$user_id."'");
}

//檢查會員
function chkuser($x="",$thisentid=0){ 
    global $s_br;
    global $SMSlock;
    global $admaddress;
    global $rootcompanyname;
    
    $SMStemppass=0;
    if($x){
        $GLOBALS["username"]=$_SESSION['tempname'];
        $SMStemppass=1;
    }
	if ($thisentid>0){
		$row=share_gettable("fun_user where thisname='".$GLOBALS["username"]."' and thisentid>0")[0];
    }else{
		$row=share_gettable("fun_user where thisname='".$GLOBALS["username"]."' and thisentid=0")[0];	
	}
	$tempz="share_".$s_br[23].$s_br[3].$s_br[12];    
    if($row){
        if(!isset($row['thisid'])){
            return "對不起，我們的系統沒有這個用戶(".$GLOBALS["username"].")<BR><BR>";
        } else if($row["isopen"]!=1){
            return "對不起，這個帳戶沒有被激活<BR><BR>";
        } else if($row["locking"]==1){
            $mes=new SENDSMS();
            $mes->message($row['phone'],"您好，請點選<a href='".$admaddress."index.php?job=sms&uid=".$row['thisid']."'>這裡</a>，解鎖".$rootcompanyname."管理後台帳號"); 
            return "對不起，這個帳戶被鎖定了，請點選簡訊開通帳號<BR><BR>";
        } else {
            // 密碼驗證
            if($SMStemppass==1 || (password_verify($GLOBALS["password"], $row["pword"]))){
                

                $current_browser_code = $GLOBALS["browserlock"] ?? '';
                $stored_browser_code = $row['thisbid'] ?? '';
                
                $need_sms_verify = false;
                $passme = 0;
                
                if($SMSlock && $SMSlock['enable']==1){
                    

					if($SMStemppass==1){
						$passme = 1;
						$need_sms_verify = false;
						

						if(!empty($current_browser_code)) {
							saveBrowserCode($row['thisid'], $current_browser_code);
							$_SESSION['new_browser_code'] = $current_browser_code;
						} 

						else if(!empty($_SESSION['pending_browser_code'])) {
							$pending_code = $_SESSION['pending_browser_code'];
							saveBrowserCode($row['thisid'], $pending_code);
							$_SESSION['new_browser_code'] = $pending_code;
							unset($_SESSION['pending_browser_code']);
						}

						else if(!empty($stored_browser_code)) {
							$_SESSION['new_browser_code'] = $stored_browser_code;
							$now = date('Y-m-d H:i:s');
							//share_update("fun_user", "bid_last_updated='".$now."'", "thisid='".$row['thisid']."'");
						}
						else {
							$new_code = generateBrowserCode($row['thisid']);
							saveBrowserCode($row['thisid'], $new_code);
							$_SESSION['new_browser_code'] = $new_code;
						}
					}

                    else {

						if(empty($stored_browser_code)) {

							$need_sms_verify = true;
							$passme = 0;

							if(!empty($current_browser_code)) {
								saveBrowserCode($row['thisid'], $current_browser_code);
							} else {
								$new_code = generateBrowserCode($row['thisid']);
								saveBrowserCode($row['thisid'], $new_code);
								$_SESSION['new_browser_code'] = $new_code;
							}
							
						} 

						else {
		
							if(!empty($current_browser_code) && $current_browser_code == $stored_browser_code) {
								$passme = 1;

							} else {
								$need_sms_verify = true;
								$passme = 0;

							}
						}
                    }
                } else {

                    $passme = 1;
                }

                $_SESSION['goSMSlock']=null;
                
                if($passme==0 && $need_sms_verify){
                    // 需要簡訊驗證
                    $_SESSION['goSMSlock']=1;
                    $_SESSION['tempname']=$GLOBALS["username"];
                    $_SESSION['goSMSkey']=rand(123456 ,987654);
                    $_SESSION['sms_send_time'] = time();
                    $_SESSION['sms_attempts'] = 0;
                    $_SESSION['sms_pending_user'] = $row['thisid'];
                    
                    $mes=new SENDSMS();
                    $mes->message($row['phone'],"您好，這裡是您的簡訊認證碼：".$_SESSION['goSMSkey']);
                    
                } else if($passme==1){
                    // 登入成功
                    if($row['issuper']==1){
                    } else {
                        // share_insert("fun_login","admid,dateadd","'".$row["thisid"]."',now()");
                    } 
					if ($thisentid>0){
						$_SESSION['companyuser']=$GLOBALS["username"];
						$_SESSION['companyid']=$row["thisid"];
						$_SESSION['cthisenttype']=$row["thisenttype"];
						$_SESSION['cthisentid']=$row["thisentid"];
						unset($_SESSION['admuser']);
						unset($_SESSION['admuserid']);		
						unset($_SESSION['athisenttype']);
						unset($_SESSION['athisentid']);						
					}else{
						$_SESSION['admuser']=$GLOBALS["username"];
						$_SESSION['admuserid']=$row["thisid"];
						$_SESSION['athisenttype']=$row["thisenttype"];
						$_SESSION['athisentid']=$row["thisentid"];						
						unset($_SESSION['companyuser']);
						unset($_SESSION['companyid']);	
						unset($_SESSION['cthisenttype']);
						unset($_SESSION['cthisentid']);								
					}
                    $_SESSION['issuper']=$row["issuper"];

					
					
                    if($row["thisid"]){
                        $_SESSION['thisid']=$row["thisid"];
                    }                    
                    $temp="";
                    if($_SESSION['issuper']==1){
                        $rowb=share_gettable("fun_list order by sorting");
                    } else {
                        $rowb=share_gettable("fun_list WHERE thisid in (SELECT thisid FROM fun_rights WHERE fun_user_thisid=".$row['thisid'].") order by sorting");
                    }
                    foreach($rowb as $ttp){
                        $temp.="-".$ttp['funclink']."-";
                    }
                    $_SESSION['rights']=$temp;
                }
                
            } else {
                return "對不起，密碼錯誤<BR><BR>";
            }
        }
    } else {
        if($tempz()==1){
            return "對不起，我們系統沒有這個使用者<BR><BR>";
        }
    }
}
function checkLogin() {
    //檢查是否已登入
    if (empty($_SESSION['companyid'])) {
        return false;
    }
    
    //檢查使用者是否存在（防 Session 被竄改）
    $userInfo = share_getinfo("fun_user", "thisid", $_SESSION['companyid']);
    if (!$userInfo) {
        // 使用者不存在，清除 Session
        session_destroy();
        return false;
    }
    
    //檢查會員是否啟用
    if ($userInfo['isopen'] != 1) {
        // 帳號未啟用，清除 Session
        session_destroy();
        return false;
    }
    
    //檢查是否為前台會員
    if (empty($userInfo['thisentid']) || $userInfo['thisentid'] == 0) {
      
        session_destroy();
        return false;
    }
    
    //同步更新 Session
    $_SESSION['cthisentid'] = $userInfo['thisentid'];
    $_SESSION['user_name'] = $userInfo['thisname'];
    $_SESSION['user_email'] = $userInfo['email'] ?? '';
    
    return true;
}


function requireLogin() {
    if (!checkLogin()) {
        header("Location: login.php");
        exit;
    }
}


function doLogout() {
    // 清除 Session Cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    
    // 清除 Session
    session_unset();
    session_destroy();
    
    header("Location: login.php");
    exit;
}
?>
  <div class='bkhead'>
    <h1>管理者許可權設定</h1>
    <div class='bknotice'>
注意事項<br />
【使用者建立】<br />
1. 管理者名稱需為 6～20 碼，只能使用英文與數字，且至少包含 1 個英文及 1 個數字，不可重複。<br />
2. 密碼需為 6～20 碼，只能使用英文與數字，且至少包含 1 個英文及 1 個數字。<br />
3. 使用者只會看到自己有權限的管理項目，因此可以放心授予本頁管理權限。<br />
4. 所有使用者只能將自己擁有的權限授予自己建立的使用者。<br />

【使用者授權】<br />
1. 請審慎授予「設定」相關權限，避免授權給不熟悉系統操作的使用者。<br />

【使用者刪除】<br />
1. 只能刪除自己建立的使用者，以及其建立的所有下層使用者。<br />
2. 刪除使用者時，其建立的所有下層使用者將一併刪除。<br />

【自我管理】<br />
1. 僅能修改自己的管理者名稱、密碼、手機號碼與 Email。<br />
2. 無法修改自己的權限或停用自己的帳號。<br />
3. 修改權限後，請重新登入系統，使新的權限生效。<br />
    </div>
  </div>
   <div class='contentin'>
 <?php
 ################################
	if($job=="adduser"){
		 adduser();
	 }elseif($job=="download"){
		 downloadme();
	 }elseif($job=="deluser"){
		 deluser();
	 }elseif($job=="upuser"){
		 upuser();
	 }
	 getuser();
 ###########################################
 function deluser(){
	 if(empty($GLOBALS["uid"])){
		 echo "<div id=error>系統錯誤</div>";
    }else{
		 if(chkcreator($GLOBALS["uid"])){
			  delone($GLOBALS["uid"]);
		 }else{
			 echo "<div id=error>您沒有權限刪除這位管理者，謝謝</div>";
		 }
	 }		
 }
 function delone($uid){
	//看看有沒有他創造的人物,有的話一起刪除

	//刪除完 creat的使用者.刪除自己
	share_del("fun_rights WHERE fun_user_thisid=".$uid);
	share_del("fun_user WHERE thisid=".$uid);
 }
 function adduser(){
	 if(empty($GLOBALS["uname"])  or empty($GLOBALS["pword"]) or empty($GLOBALS["phone"])){
		 echo "<div id=error>所有內容皆為必須填寫</div>";
	}elseif(!chkAccount($GLOBALS["uname"])){
		 echo "<div id=error>管理者名稱需為8~20碼英文與數字混合，不可使用中文或特殊符號</div>";
	}elseif(!chkAccount($GLOBALS["pword"])){
		 echo "<div id=error>密碼需為8~20碼英文與數字混合，不可使用中文或特殊符號</div>";
	}elseif (!empty($GLOBALS["email"]) && !chkEmail($GLOBALS["email"])) {
		// Email 有填才檢查格式
		echo "<div id=error>Email 格式不正確</div>";
    }else{			
		$row=share_getinfo("fun_user","thisname",$GLOBALS["uname"]);
		if($row){
			echo "<div id=error>管理者名稱已存在</div>";
		}else{			
			$row=share_getinfo("fun_user","phone",$GLOBALS["phone"]);
			if($row){
				echo "<div id=error>手機號碼已存在</div>";
			}else{	
			 share_insert("fun_user","thisname,phone,pword,email,isopen,creator,pdate,dateadd","'".$GLOBALS["uname"]."','".$GLOBALS["phone"]."','".share_pwdsha($GLOBALS["pword"])."','".$GLOBALS["email"]."',".$GLOBALS["isopen"].",'".$_SESSION['thisid']."',now(),now()");
			 if(isset($GLOBALS['funcid'])){	
				$row=share_getinfo("fun_user","thisname",$GLOBALS["uname"]);
				if($row){
					$myid=$row['thisid'];
				}
				for($i=0;$i<count($GLOBALS['funcid']);$i++){
					share_insert("fun_rights","fun_list_thisid,fun_user_thisid",$GLOBALS['funcid'][$i].",".$myid);
				}			 
			 }
		   }
		} 
	 }
 }
 function chkcreator($uid){
	$ffk=0;
	$row=share_getinfo("fun_user","thisid",$uid);
	if($row['creator']){
		if($row['creator']==$_SESSION['thisid']){
			return true;
		}else{
			$ffk=$row['creator'];
		}		
	}else{
		return false;
	}		
	if($ffk){
		chkcreator($ffk);
	}
 }
 function upuser(){
	 $ff=0;
	 if(empty($GLOBALS["uname"])  or empty($GLOBALS["phone"]) or empty($GLOBALS["uid"])){
		 echo "<div id=error>名稱、phone必須填寫</div>";
	}elseif(!chkAccount($GLOBALS["uname"])){
		echo "<div id=error>管理者名稱需為8~20碼英文與數字混合，不可使用中文或特殊符號</div>";
	}elseif(!empty($GLOBALS["pword"]) && !chkAccount($GLOBALS["pword"])){
		echo "<div id=error>密碼需為8~20碼英文與數字混合，不可使用中文或特殊符號</div>";
	}elseif (!empty($GLOBALS["email"]) && !chkEmail($GLOBALS["email"])) {
		echo "<div id=error>Email 格式不正確</div>";	
	}else{
		$cct=share_getcount("fun_user where thisid!='".$GLOBALS['uid']."' and thisname= '".$GLOBALS['uname']."'");
		if ($cct>0){		
			 echo "<div id=error>管理者名稱已存在</div>";
		}else{		
			$cct=share_getcount("fun_user where thisid!='".$GLOBALS['uid']."' and phone= '".$GLOBALS['phone']."'");
			if ($cct>0){		
				 echo "<div id=error>手機號碼已存在</div>";
			}else{	
		
				$row=share_getinfo("fun_user","thisid",$GLOBALS["uid"]);
				if($row && $row['issuper']==1){
					$ff=1;
				}		
				if(empty($GLOBALS["pword"])){
					$GLOBALS["pword"]=$row['pword'];
				}else{		
					$GLOBALS["pword"]=share_pwdsha($GLOBALS["pword"]);					
				}	
				if($ff==0){
					if(chkcreator($GLOBALS["uid"])|| $_SESSION['issuper']==1 || $GLOBALS["uid"]==$_SESSION['thisid']){
						if($GLOBALS["uid"]==$_SESSION['thisid']){
							share_update("fun_user","thisname='".$GLOBALS["uname"]."', pword='".$GLOBALS["pword"]."', phone='".$GLOBALS["phone"]."', email='".$GLOBALS["email"]."'","thisid=".$GLOBALS["uid"]);
						}else{
							share_update("fun_user","thisname='".$GLOBALS["uname"]."', pword='".$GLOBALS["pword"]."', phone='".$GLOBALS["phone"]."', email='".$GLOBALS["email"]."',isopen=".$GLOBALS["isopen"],"thisid=".$GLOBALS["uid"]);
							share_del("fun_rights WHERE fun_user_thisid=".$GLOBALS["uid"]);
							 if(isset($GLOBALS['funcid'])){
								for($i=0;$i<count($GLOBALS['funcid']);$i++){
									share_insert("fun_rights","fun_list_thisid,fun_user_thisid",$GLOBALS['funcid'][$i].",".$GLOBALS["uid"]);
								}
							}
						}
					}else{
						 echo "<div id=error>您沒有權限修改這位管理者<BR><BR></div>";
					 }
				}else if($ff==1 && $_SESSION['issuper']==1){
					share_update("fun_user","thisname='".$GLOBALS["uname"]."', pword='".share_pwdsha($GLOBALS["pword"])."', phone='".$GLOBALS["phone"]."', email='".$GLOBALS["email"]."',isopen=".$GLOBALS["isopen"],"thisid=".$GLOBALS["uid"]);
					share_del("fun_rights WHERE fun_user_thisid=".$GLOBALS["uid"]);
					if(isset($GLOBALS['funcid'])){
						for($i=0;$i<count($GLOBALS['funcid']);$i++){
							share_insert("fun_rights","fun_list_thisid,fun_user_thisid",$GLOBALS['funcid'][$i].",".$GLOBALS["uid"]);
						}
					}			
				}
		   }	
		}	
	 }	 
 }
 function getuser(){
	 ?>

     <span class='fR' style='margin:10px 10px 0 0;font-size:15px;'><span class='redtext'>＊</span>必填</span>
      <TABLE width=100%  class=formtable>
      <TR>
      <th class='tCenter b-right' width=120><span class='redtext'>＊</span>管理者名稱</th>
	  <th class='tCenter b-right' width=120><span class='redtext'>＊</span>手機號碼</th>	
      <th class='tCenter b-right'>Email</th>
      <th class='tCenter b-right'><span class='redtext'>＊</span>密碼</th>
      <th class='tCenter b-right'>許可權</th>
      <th class='tCenter b-right'>啟用</th>
      <th class='tCenter b-right'>新增</th>
      </TR>
      <?php
	 $myarr = array();
	 $a=0;
	 if($_SESSION['issuper']==1){
		 $temp=share_gettable("fun_list");
	 }else{
		  $temp=share_gettable("fun_list where thisid in (SELECT fun_list_thisid FROM fun_rights WHERE fun_user_thisid	=".$_SESSION['thisid'].")");
	 }
	 foreach($temp as $row){
         $myarr[$a]['id']=$row['thisid'];
         $myarr[$a]['name']=$row['thisname'];	
		 $a++;
     }
     echo "<tr><FORM action='index.php?tf=code97' method='post'><INPUT type=hidden name=job value=adduser />";
	 echo "<td class='tCenter b-right'><input type='text' name='uname' class='input02'/></td>";
	 echo "<td class='tCenter b-right'><input type='text' name='phone' class='input02'/></td>";	 
	 echo "<td class='tCenter b-right'><input type='text' name='email' class='input02'/></td>";
	 echo "<td class='tCenter b-right'><input type='text' name='pword' class='input02' /></td>";
     echo "<td class='tCenter b-right'><div class=funcwrap>";
	for ($i=0;$i<sizeof($myarr);$i++){
		echo "<p class=funclist><input type=checkbox name='funcid[]' value='".$myarr[$i]['id']."'>".$myarr[$i]['name']."</p>";
	}
     echo "</div></td>";
	 echo "<td><input type=radio name=isopen value=1>是<input type=radio name=isopen  checked value=0>否</td>";
	 echo "<td><input type=submit class='gray-btn' /></td></FORM></tr>";      
	 ?>
      <TABLE width=100%   class=formtable>
      <TR>
      <th class='tCenter b-right' width=40>刪除</th>

      <th class='tCenter b-right' width=80>管理者名稱</th>
      <th class='tCenter b-right' width=80>手機號碼</th>	  	  
      <th class='tCenter b-right' width=80>密碼</th>
      <th class='tCenter b-right' width=120>Email</th>
      <th class='tCenter b-right' width=200>許可權</th>
      <th class='tCenter b-right' width=80>啟用</th>
      <th class='tCenter b-right' width=40>更新</th>
      </TR>
      <?php
	  $temp=share_gettable("`fun_user` where  thisid>2 and thisentid=0 order by thisid");
	  foreach($temp as $row){
 		        echo "<tr><FORM action='index.php?tf=code97&job=upuser&uid=".$row["thisid"]."' method='post' >";
				if ($row["thisid"]==2)
				{
					echo "<td class='tCenter b-right'></td>";
		
				}else{
					echo "<td class='tCenter b-right'><a href=index.php?tf=code97&job=deluser&uid=".$row["thisid"]." class='blue-btn delbtn'>刪除</a></td>";
				}
				echo "<td class='tCenter b-right'><input type=text name=uname value='".$row["thisname"]."' class='input02'></TD>";
				echo "<td class='tCenter b-right'><input type=text name=phone value='".$row["phone"]."' class='input02'></TD>";		
				echo"<td class='tCenter b-right'><input type=password name=pword value='' class='input02'></TD><td class='tCenter b-right'><input type=text name=email value='".$row["email"]."' class='input02'></TD>";
                echo "<td class='tCenter b-right'><div class=funcwrap>";
				for ($i=0;$i<sizeof($myarr);$i++){
					$tp=0;
					$rowb=share_gettable("fun_rights WHERE fun_user_thisid=".$row['thisid']." AND fun_list_thisid=".$myarr[$i]['id']);
					if(isset($rowb[0]['fun_list_thisid'])){
						echo "<p class=funclist><input type=checkbox name='funcid[]' value='".$myarr[$i]['id']."' checked>".$myarr[$i]['name']."</p>";
						$tp=1;
					}
					if($tp==0){
					    echo "<p class=funclist><input type=checkbox name='funcid[]' value='".$myarr[$i]['id']."'>".$myarr[$i]['name']."</p>";
                    }
				}
				echo "</div></td>";
				if(isset($row['isopen']) && $row['isopen']==1){
					 echo "<td class='tCenter b-right'><input type=radio name=isopen checked value=1>是<input type=radio name=isopen value=0>否</td>";
				}else{
					 echo "<td class='tCenter b-right'><input type=radio name=isopen value=1>是<input type=radio name=isopen  checked value=0>否</td>";					 
				}
                echo "<TD class='tCenter b-right'><input type=submit class='gray-btn'></TD>";
				echo "</form></TR>";
     }
     echo "</TABLE>";
echo "<br><p>備註：</p><ol class='faqintro'><li>管理者名稱需為 6～20 碼英文與數字混合，且不可重複。</li><li>密碼需為 6～20 碼英文與數字混合。</li><li>修改權限後請重新登入，新的權限才會生效。</li><li>帳號需啟用後才能登入系統。</li><li>未授予任何權限時，將無法使用任何管理功能。</li></ol>";;
 }


	?>
        </div>

  <div class='bkhead'>
    <h1>管理功能設定</h1>
    <div class='bknotice'>
      注意事項<BR />
    </div>
  </div>
  <div class='contentin'>
         <?php
################################
if($job=="addfunc"){
	 addfunc();
}elseif($job=="delfunc"){
	 delfunc();
}elseif($job=="upfunc"){
	 upfunc();
}
 getfunc();
 function delfunc(){
	 if(empty($GLOBALS["funcid"])){
		 echo "系統錯誤";
    }else{
		$mycount=0;
		$mycount=share_getcount("fun_rights WHERE fun_list_thisid=".$GLOBALS["funcid"]);
	    if($mycount==0){
			share_del("fun_list WHERE thisid=".$GLOBALS["funcid"]);
	    }else{
		   echo "本功能已有權力系結，請先移轉相關設定，謝謝";
	    }
	 }	
 }
 function addfunc(){
	 if(empty($GLOBALS["funcname"]) || empty($GLOBALS["funclink"]) ){
		 echo "所有內容皆為必須填寫";
    }else{
		share_insert("`fun_list`","thisname,funclink,fun_group_thisid,funcval","'".$GLOBALS["funcname"]."','".$GLOBALS["funclink"]."',".$GLOBALS["groupid"].",'".$GLOBALS["funcval"]."'");
	 }
 }
 function upfunc(){
	 if(empty($GLOBALS["funcname"]) or empty($GLOBALS["funcid"])or empty($GLOBALS["funclink"])){
		 echo "所有內容皆為必須填寫";
    }else{
		share_update("`fun_list`","thisname='".$GLOBALS["funcname"]."',funclink='".$GLOBALS["funclink"]."',fun_group_thisid=".$GLOBALS["groupid"].",funcval='".$GLOBALS["funcval"]."'","thisid=".$GLOBALS["funcid"]);
	 }	 
 }
 function getfunc(){
	 ?>
     <TABLE width=800  class=formtable>
      <TR>
      <th class='tCenter b-right w120p'>管理分類</th>
      <th class='tCenter b-right mw200p'>名稱(僅給予參考)</th>
      <th class='tCenter b-right' width='150'>功能連結</th>
      <th class='tCenter b-right' width='50'>變數pg</th>
      <th class='tCenter b-right' width=80>新增</th>
      </TR>
     <?php
	 $myarr = array();
	 $a=0;	 
	 $temp=share_gettable("fun_group");
	 foreach($temp as $row){
        $myarr[$a]['id']=$row['thisid'];
        $myarr[$a]['name']=$row['thisname'];	
		$a++;
     }	
     echo "<tr><FORM action='index.php?tf=code99' method='post'><INPUT type=hidden name=job value=addfunc />";
	 echo "<td class='tCenter b-right'><select name=groupid class='select01'>";
	 for ($i=0;$i<sizeof($myarr);$i++){
		echo "<option value='".$myarr[$i]['id']."'>".$myarr[$i]['name']."</option>";
 	 }
	 echo "</select></td>";
     echo "<td class='tCenter b-right'><input type='text' name='funcname' class='input02' /></td><td class='tCenter b-right'><input type='text' name='funclink' class='input02' /></td><td class='tCenter b-right'><input type=text name=funcval class='input02'></TD><td class='tCenter b-right'><input type=submit class='gray-btn' value='新增'></td>";
     echo "</FORM></tr>";      
	 ?>
      </TABLE>
     <TABLE width=800  class=formtable>
      <TR>
      <th class='tCenter b-right' width=80>刪除</th>
      <th  class='tCenter b-right w120p'>管理分類</th>
      <th  class='tCenter b-right w50p'>thisid</th>
      <th  class='tCenter b-right mw150p'>名稱(僅給予參考)</th>
      <th class='tCenter b-right' width='150'>功能連結</th>
      <th class='tCenter b-right' width='50'>變數pg</th>
      <th class='tCenter b-right' width=80>更新</th> 
      </TR>
	<?php
	$temp=share_gettable("fun_list");
	foreach($temp as $row){
		echo "<tr><FORM action='index.php?tf=code99&job=upfunc&funcid=".$row["thisid"]."' method='post' >";
		echo "<td class='tCenter b-right'><a href=index.php?tf=code99&job=delfunc&funcid=".$row["thisid"]." class='blue-btn delbtn'>刪除</a></td>";
		echo "<td class='tCenter b-right'><select name=groupid class='select01'>";
		for ($i=0;$i<sizeof($myarr);$i++){
			if($row['fun_group_thisid']==$myarr[$i]['id']){
				echo "<option value='".$myarr[$i]['id']."' selected>".$myarr[$i]['name']."</option>";						
			}else{
				echo "<option value='".$myarr[$i]['id']."'>".$myarr[$i]['name']."</option>";
			}
		}
		echo "</select></td>";
        echo "<td class='tCenter b-right'>".$row["thisid"]."</td><td><input type=text name=funcname  class='input02' value='".$row["thisname"]."'></TD><td class='tCenter b-right'><input type=text name=funclink value='".$row["funclink"]."' class='input02'></TD><td class='tCenter b-right'><input type=text name=funcval value='".$row["funcval"]."' class='input02'></TD>";
        echo "<td class='tCenter b-right'><input type=submit class='gray-btn' value='更新'></td></form></TR>";
     }
     echo "</TABLE>";
 }
	?>
    </div>


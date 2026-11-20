  <div class='bkhead'>
    <h1>管理分類設定</h1>
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
 }elseif($job=="changesorting"){
	 sorting();
 }
 getfunc();
function sorting(){
	$neworder="";
	$row=share_gettable("fun_group ORDER BY sorting DESC limit ".($GLOBALS["myorder"]-1).",1");
    $neworder=$row[0]['sorting'];
	if($GLOBALS["thisorder"]>$neworder){
		share_update("fun_group","sorting=sorting+1", "sorting<".$GLOBALS["thisorder"]." AND sorting>=".$neworder);
	}else{
		share_update("fun_group","sorting=sorting-1", "sorting>".$GLOBALS["thisorder"]." AND sorting<=".$neworder);
	}
	share_update("fun_group","sorting=".$neworder, "thisid=".$GLOBALS["groupid"]);
}	 
 function delfunc(){
	if(empty($GLOBALS["groupid"])){
		 echo "系統錯誤";
    }else{
		$mycount=0;
		$row=share_getinfo("fun_list","fun_group_thisid",$GLOBALS["groupid"]);
		$mycount=$row["CC"];
	   if($mycount==0){
		   share_del("fun_group WHERE thisid=".$GLOBALS["groupid"]);
	   }else{
		   echo "本功能已有權力系結，請先移轉相關設定，謝謝";
	   }
	}	
 }
 function addfunc(){
	if(empty($GLOBALS["groupname"])){
		 echo "所有內容皆為必須填寫";
    }else{
		$row=share_gettable("fun_group order by sorting desc limit 1");
		if(is_array($row)){
			$myorders=$row[0]["sorting"]+1;
		}else{
			$myorders=1;
		}
		share_insert("fun_group","thisname,sorting","'".$GLOBALS["groupname"]."',".$myorders);
	 }
 }
 function upfunc(){
	 if(empty($GLOBALS["groupname"]) or empty($GLOBALS["groupid"])){
		 echo "所有內容皆為必須填寫";
    }else{
		share_update("fun_group","thisname='".$GLOBALS["groupname"]."'","thisid=".$GLOBALS["groupid"]);
	 }	 
 }
 function getfunc(){
	 ?>
     <TABLE width=800  class=formtable>     
      <TR><th class='tCenter b-right'>管理分類名稱</th>
      <th class='tCenter b-right'>新增</th></TR>     
       <tr><FORM action='index.php?tf=code98' method='post'><INPUT type=hidden name=job value=addfunc />
       <td class='tCenter b-right'><input type='text' name='groupname' class='input02' /></td>
       <td class='tCenter b-right'><input type=submit value='新增' class='gray-btn'></td>
     </FORM></tr>
     </TABLE>
     <TABLE width=800  class=formtable>     
      <TR>
      <th class='tCenter b-right'>刪除</th>
      <th class='tCenter b-right'>管理分類ID</th>
      <th class='tCenter b-right'>管理分類名稱</th>
      <th class='tCenter b-right'>更新</th>
      <th class='tCenter b-right'>排序</th>
      </TR>
     <?php
	$temp=share_getcount("fun_group");
	if($temp){
        $mycc=$temp;
     }else{
	 	$mycc=0;
	 }
     $a=1; 
	 $temp=share_gettable("fun_group order by sorting DESC");
	 foreach($temp as $row){
 		echo "<tr><FORM action='index.php?tf=code98&job=upfunc&groupid=".$row["thisid"]."' method='post' >";
		echo "<td class='tCenter b-right'><a href=index.php?tf=code98&job=delfunc&groupid=".$row["thisid"]." class='blue-btn delbtn'>刪除</a></td>";
		echo "<td class='tCenter b-right'>".$row["thisid"]."</td><td><input type=text name=groupname value='".$row["thisname"]."' class='input02' ></TD>";
        echo "<td class='tCenter b-right'><input type=submit class='gray-btn' value='更新'></td></form>";
 	    echo "<FORM action='index.php?tf=code98&job=changesorting&groupid=".$row["thisid"]."&thisorder=".$row["sorting"]."' method='post'>";
		echo "<td class='tCenter b-right'><select name=myorder class='changeselect select02'>";
        for($b=1;$b<=$mycc;$b++){
		    if($b==$a){
			     echo "<option value='".$b."' selected>".$b."</option>";
		    }else{
			      echo "<option value='".$b."'>".$b."</option>";		
		    }
		}
        echo "</TD></form>";
		echo "</TR>";
		$a++;					
     }
     echo "</TABLE>";
 }
	?>
	</div>

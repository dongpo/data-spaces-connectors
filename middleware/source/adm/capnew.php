<?php	
	require_once 'config/config.php';
	require_once 'config/share.php';
	require_once 'config/getformin.php';
	if(strpos($_SERVER['REQUEST_URI'], 'capnew')!== false){
		$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
		$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);
$allowed_functions = [
    'gettoken','get_caplist','test_capcode'
];

if(!empty($job)){
    if(in_array($job, $allowed_functions)){
        // ✅ 真正的執行
        if(!empty($val)){
            $job($val);
        }else{
            $job();
        }
    }else{
        die("Invalid job");
    }
}
		$pdo=null;
	}
	function get_caplist(){
		echo "<div id='capchk' class='btn' ><i class='fa fa-square' aria-hidden='true' ></i>我不是機器人</div>";
?>
	<script nonce="<?=$_SESSION['nonce']?>" >
	$(document).ready(function() {
		 $("#capchk").click(function(){
			$("#capchk").children("i").removeClass("fa-square");
			$("#capchk").children("i").addClass("fa-spinner");
			$("#capchk").children("i").addClass("fa-spin");
			var tempvals=Array('fds4wrewr');// 類別,ID	 
			tempitem=ajaxarr("gettoken",tempvals,"capnew.php");
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
	function gettoken(){
		share_del("logtokens WHERE timekey <".(time()-20));
		$ntk=bin2hex(openssl_random_pseudo_bytes(20));
		share_insert("logtokens","tk,timekey","'".$ntk."','".time()."'");
		$out[0]="OK";
		$out[1]=$ntk;
		$_SESSION['tk']=$ntk;
	 	echo json_encode($out);
		
	}
	function test_capcode($code){
		share_del("logtokens WHERE timekey<".(time()-20));
		if($t=share_getinfo("logtokens","tk",$code)){
			return "PASS";
		}else{
			return "驗證失敗,請重新驗證";
		}
	}
?> 

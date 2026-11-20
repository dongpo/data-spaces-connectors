<?php
ini_set('short_open_tag', '0');
$job="";
$tf="";
$er="";
$lanon=0;	//語言開啟
//ini_set("session.cookie_httponly", 1);
session_start();
require_once 'adm/config/config.php';
require_once 'adm/config/getformin.php';
require_once 'adm/config/createimg.php';
require_once 'adm/config/share.php';


$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);

if($_SESSION['intime']){
}else{
	$_SESSION['intime']=date("Y/m/d H:i");
}
if (isset($_REQUEST['_SESSION'])) die("Get lost Muppet!");
if(!empty($job)){
    if(strpos($job, 'aj_') === 0){
        if(function_exists($job)){
            try {
                $reflection = new ReflectionFunction($job);
                if(!$reflection->isInternal()){
                    if(!empty($val)){
                        $job($val);
                    }else{
                        $job();
                    }
                }else{
                    error_log("Blocked internal function: " . $job . " from " . $_SERVER['REMOTE_ADDR']);
                    die("Invalid job");
                }
            } catch (Exception $e) {
                error_log("Reflection error: " . $e->getMessage());
                die("Invalid job");
            }
        }else{
            error_log("Blocked non-existent function: " . $job . " from " . $_SERVER['REMOTE_ADDR']);
            die("Function not found");
        }
    }else{
        error_log("Blocked invalid job prefix: " . $job . " from " . $_SERVER['REMOTE_ADDR']);
        die("Invalid job");
    }
}
$pdo=null;

	function aj_gettoken(){
		share_del("logtokens WHERE timekey <".(time()-20));
		$ntk=bin2hex(openssl_random_pseudo_bytes(20));
		share_insert("logtokens","tk,timekey","'".$ntk."','".time()."'");
		$out[0]="OK";
		$out[1]=$ntk;
		$_SESSION['tk']=$ntk;
	 	echo json_encode($out);
		
	}
function aj_get_schema_versions($x=''){
	$out[0]="OK";
	$out[1]=share_gettable("gaia_schema_version WHERE schema_id = ".$x[0]." AND isopen = 1 ORDER BY thisid DESC");
	echo json_encode($out);
}		 
?>
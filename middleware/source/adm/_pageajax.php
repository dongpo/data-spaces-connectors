<?php
ini_set('short_open_tag', '0');
$job="";
$tf="";
$er="";
$lanon=0;	//語言開啟
//ini_set("session.cookie_httponly", 1);
session_start();
require_once 'config/config.php';
require_once 'config/getformin.php';
require_once 'config/createimg.php';
require_once 'config/share.php';
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

function aj_savepimage($x=''){
	//$out="";
	$out[0]="OK";	
	$id=rand(123,987).date('Yndhms').rand(123,987);
	$file=$_FILES["img"];
	$imgurl="../uploadfile/";
	$imgwidth=200;
	$imgheight=0;
	if(isset($GLOBALS['valx'])){		$imgwidth=$GLOBALS['valx'];	}
	if(isset($GLOBALS['valy'])){		$imgheight=$GLOBALS['valy'];}
	$imgs=0;
	$thumbwidth=0;
	$thumbheight=0;
	$err=0;
    $fileext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileext, $allowed_ext)) {
        echo json_encode(['ERR' => '不允許的檔案類型']);
        return;
    }
	if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
		$src = imagecreatefromjpeg($file['tmp_name']);
	}else if($file["type"] == "image/png") { 
		$src = imagecreatefrompng($file['tmp_name']);
	}

	$width = imagesx($src);
	$height =imagesy($src);
	 if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
	   if($imgwidth==$width && $imgheight==$height){
		$fileext="jpg";
		move_uploaded_file ( $file['tmp_name'], $imgurl.$id.".jpg");
	   }else{
		$fileext="jpg";
		createimg($id,$imgurl,$width,$height,$imgwidth,$imgheight,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);  //這裡有調整寬度設定
	   }
	 }else if($file["type"] == "image/png") {
	  if($imgwidth==$width && $imgheight==$height){
	   $fileext="png";
	   move_uploaded_file ( $file['tmp_name'], $imgurl.$id.".png");
	  }else{
	   $fileext="png";
	   createimg($id,$imgurl,$width,$height,$imgwidth,$imgheight,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);  //這裡有調整寬度設定
	  }
	 }else{
	  $err=1;
	  $out="ERR";
	 }	
	if($err==1){
	}else{
		$out[1]=$id.".".$fileext;
		//$out="-".$width."-".$height."-".$imgwidth."-".$imgheight."-";
	}
		echo htmlspecialchars_decode(htmlspecialchars(json_encode($out)));	 
}

function aj_savepimage1($x=''){
    $out[0]="OK";
    $id=rand(123,987).date('Yndhms').rand(123,987);
    $file=$_FILES["img"];
    $imgurl="../uploadfile/";
    $fileext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($fileext, $allowed)) {
        echo json_encode(['ERR' => '不允許的檔案類型']);
        return;
    }
    
    move_uploaded_file($file['tmp_name'],$imgurl. $id.".".$fileext);
    $out[1]=$id.".".$fileext;
    echo htmlspecialchars_decode(htmlspecialchars(json_encode($out)));       
}

function aj_savepfile($x=''){
    $out[0]="OK";
    $id=rand(123,987).date('Yndhms').rand(123,987);
    $file=$_FILES["img"];
    $imgurl="../uploadfile/";
    $fileext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
    if (!in_array($fileext, $allowed)) {
        echo json_encode(['ERR' => '不允許的檔案類型']);
        return;
    }
    
    move_uploaded_file($file['tmp_name'],$imgurl. $id.".".$fileext);
    $out[1]=$id.".".$fileext;
    echo htmlspecialchars_decode(htmlspecialchars(json_encode($out)));       
}
function aj_savecss($x=''){
    echo json_encode(['ERR' => '此功能已停用，如有需要請聯絡管理員']);
    return;
}



function aj_savepimageup($x=''){
	//$out="";
	$id=rand(123,987).date('Yndhms').rand(123,987);
	$file=$_FILES["img"];
	$imgurl="../uploadfile/";	
	$imgwidth=100;
	$imgheight=0;
//	if(isset($GLOBALS['valx'])){		$imgwidth=$GLOBALS['valx'];	}
//	if(isset($GLOBALS['valy'])){		$imgheight=$GLOBALS['valy'];}
	$imgs=0;
	$thumbwidth=0;
	$thumbheight=0;
	$err=0;
    $fileext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileext, $allowed_ext)) {
        echo "ERR: 不允許的檔案類型";
        return;
    }
	if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
		$src = imagecreatefromjpeg($file['tmp_name']);
	}else if($file["type"] == "image/png") { 
		$src = imagecreatefrompng($file['tmp_name']);
	}
	$width = imagesx($src);
	$height =imagesy($src);
	$mywidth=0;
	if($width>1920){
	  $thumb_w = 1920;
	  $thumb_h = 0;
	  $mywidth=1;
	}	
	if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
			$fileext="jpg";
			if ($mywidth==1)
			{
				createimg($id,$imgurl,$width,$height,$thumb_w,$thumb_h,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);					
			}else{
				copy($file['tmp_name'], "uploadfile/" . $id.".".$fileext); 
			}
	}else if($file["type"] == "image/png") { 
			$fileext="png";
			if ($mywidth==1)
			{
				createimg($id,$imgurl,$width,$height,$thumb_w,$thumb_h,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);						
			}else{
				copy($file['tmp_name'], "uploadfile/" . $id.".".$fileext); 
			}			
	}else{
		$err=1;
		$out="ERR";
	} 
	if($err==1){
	}else{
		$out=$id.".".$fileext;
		//$out="-".$width."-".$height."-".$imgwidth."-".$imgheight."-";
	}
	echo $out;
}	



function aj_savepstoreimage($x=''){
	//$out="";
	$out[0]="OK";	
	$id=rand(123,987).date('Yndhms').rand(123,987);
	$idm=$id."_m";	
	$file=$_FILES["img"];
	$imgurl="../uploadfile/";
	$imgwidth=200;
	$imgheight=0;
	$imgwidthm=360;
	$imgheightm=260;	
	if(isset($GLOBALS['valx'])){		$imgwidth=$GLOBALS['valx'];	}
	if(isset($GLOBALS['valy'])){		$imgheight=$GLOBALS['valy'];}
	$imgs=0;
	$thumbwidth=0;
	$thumbheight=0;
	$err=0;
    $fileext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];
    if (!in_array($fileext, $allowed_ext)) {
        echo json_encode(['ERR' => '不允許的檔案類型']);
        return;
    }
	if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
		$src = imagecreatefromjpeg($file['tmp_name']);
	}else if($file["type"] == "image/png") { 
		$src = imagecreatefrompng($file['tmp_name']);
	}

	$width = imagesx($src);
	$height =imagesy($src);
	 if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
	   if($imgwidth==$width && $imgheight==$height){
		$fileext="jpg";
		move_uploaded_file ( $file['tmp_name'], $imgurl.$id.".jpg");
	   }else{
		$fileext="jpg";
		createimg($id,$imgurl,$width,$height,$imgwidth,$imgheight,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);  //這裡有調整寬度設定
	   }
		createimg($idm,$imgurl,$width,$height,$imgwidthm,$imgheightm,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);		//這裡有調整寬度設定	   
	 }else if($file["type"] == "image/png") {
	  if($imgwidth==$width && $imgheight==$height){
	   $fileext="png";
	   move_uploaded_file ( $file['tmp_name'], $imgurl.$id.".png");
	  }else{
	   $fileext="png";
	   createimg($id,$imgurl,$width,$height,$imgwidth,$imgheight,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);  //這裡有調整寬度設定
	  }
	   createimg($idm,$imgurl,$width,$height,$imgwidthm,$imgheightm,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);		//這裡有調整寬度設定	
	 }else{
	  $err=1;
	  $out="ERR";
	 }	
	if($err==1){
	}else{
		$out[1]=$id.".".$fileext;
		//$out="-".$width."-".$height."-".$imgwidth."-".$imgheight."-";
	}
		echo htmlspecialchars_decode(htmlspecialchars(json_encode($out)));	 
}
?>
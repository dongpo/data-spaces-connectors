<?php
$nonce = base64_encode(random_bytes(16));

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce' https://*.googletagmanager.com https://*.googleapis.com https://*.gstatic.com https://maps.googleapis.com https://*.youtube.com https://s.ytimg.com https://cse.google.com; style-src 'self' 'nonce-$nonce' https://fonts.googleapis.com; img-src 'self' data: blob: https://*.googleapis.com https://*.gstatic.com https://*.ytimg.com https://www.googletagmanager.com; connect-src 'self' https://www.google-analytics.com https://maps.googleapis.com https://*.youtube.com; font-src 'self' https://fonts.gstatic.com; frame-src 'self' https://*.youtube.com https://www.youtube-nocookie.com https://www.google.com https://cse.google.com; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'; upgrade-insecure-requests;");

header("Cross-Origin-Opener-Policy: same-origin");
////header("Cross-Origin-Embedder-Policy: credentialless");
ini_set('short_open_tag', '0');
$job="";
$username="";
$password="";
$tf="";
$er="";
$lanon=0;	//語言開啟
session_start();
require_once '../adm/config/config.php';
require_once '../adm/config/getformin.php';
require_once '../adm/config/createimg.php';
require_once '../adm/config/share.php';

$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);

if($_SESSION['intime']){
}else{
	$_SESSION['intime']=date("Y/m/d H:i");
}
if (isset($_REQUEST['_SESSION'])) die("Get lost Muppet!");
$allowed_functions = [
    'getok','getdb','changesort','getpage','getblocks','savelinefile','savelineimage','changeimgattr','changeimgnote','addcutline','add2split','deleteimggroupitem','deleteline','saveline','getok1','inputclean'
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
function getok($x=''){
	$out[0]="OK";
	echo json_encode($out);
}
function getdb($x=''){
	$out[0]="OK";
	$t=share_gettable("ibmain");
	$out[1]=count($t);
	$out[2]=$GLOBALS["dbhost"];
	echo json_encode($out);

}
function changesort($x=''){
	//$out="";
	if($x[0] && $x[1]){
		$out[0]="OK";
		for($a=0;$a<count($x[2]);$a++){
			share_update("ibmain","thisrow='".($a+1)."'","thisid='".$x[2][$a]."'");
		}
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function getpage($x=''){
	if($x[0] && $x[1]){
		$out[0]="OK";
		if($pg=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' AND mainid is null order by thisrow")){
			for($a=0;$a<count($pg);$a++){
					$out[1][$a]=getblocks($pg[$a]);
			}
		}else{
			$out[1]=null;
		}
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function getblocks($pg,$id=''){
	$o[0]=$pg["type"];
	$o[1]=$pg["thisid"];
	if($pg["type"]=="list"){
		$sub=share_gettable("ibsub WHERE ibmain_thisid='".$pg["thisid"]."' AND attr is null order by thisrow");
		for($b=0;$b<count($sub);$b++){
				$o[2][0][$b]=$sub[$b]["val"];
				$sub2=share_gettable("ibsub WHERE ibmain_thisid='".$pg["thisid"]."' AND thisrow='".$sub[$b]['thisrow']."' AND attr is not null ORDER BY `attr` IS NOT NULL, CAST(`attr` AS UNSIGNED)");
				if($sub2){
					for($c=0;$c<count($sub2);$c++){
								$o[2][2][$b][$c]=$sub2[$c]["val"];
					}
				}
			}
			$link=share_gettable("iblink WHERE ibmain_thisid='".$pg["thisid"]."'");
			for($b=0;$b<count($link);$b++){
				$o[2][1][$b][0]=$link[$b]["tag"];
				$o[2][1][$b][1]=$link[$b]["val"];
			}
			if($pg["attr"]==null){
				$o[2][3]="";
			}else{
				$o[2][3]=$pg["attr"];
			}
		}else if($pg["type"]=="table2"){
			$o[2][2]=$pg['val'];
			$o[2][3]=$pg['attr'];
			$suba=share_gettable("ibsub WHERE ibmain_thisid='".$pg["thisid"]."' order by thisrow DESC limit 1");
			for($b=0;$b<$suba[0]['thisrow'];$b++){
				for($c=0;$c<2;$c++){
					$temp=share_gettable("ibsub WHERE ibmain_thisid='".$pg["thisid"]."' AND thisrow='".($b+1)."' AND col='".($c+1)."'");
					$o[2][0][$b][$c][0]=$temp[0]["val"];
					$txp=explode("_",$temp[0]["attr"]);
					$o[2][0][$b][$c][1]=$txp[0];
					$o[2][0][$b][$c][2]=$txp[1];
				}
			}
			$link=share_gettable("iblink WHERE ibmain_thisid='".$pg["thisid"]."'");
			for($b=0;$b<count($link);$b++){
				$o[2][1][$b][0]=$link[$b]["tag"];
				$o[2][1][$b][1]=$link[$b]["val"];
			}

		}else if($pg["type"]=="table3"){
			$o[2][2]=$pg['val'];
			$o[2][3]=$pg['attr'];
			$suba=share_gettable("ibsub WHERE ibmain_thisid='".$pg["thisid"]."' order by thisrow DESC limit 1");
			for($b=0;$b<$suba[0]['thisrow'];$b++){
				for($c=0;$c<3;$c++){
					$temp=share_gettable("ibsub WHERE ibmain_thisid='".$pg["thisid"]."' AND thisrow='".($b+1)."' AND col='".($c+1)."'");
					$o[2][0][$b][$c][0]=$temp[0]["val"];
					$txp=explode("_",$temp[0]["attr"]);
					$o[2][0][$b][$c][1]=$txp[0];
					$o[2][0][$b][$c][2]=$txp[1];
					//$o[2][0][$b][$c][1]=$temp[0]["attr"];
				}
			}
			$link=share_gettable("iblink WHERE ibmain_thisid='".$pg["thisid"]."'");
			for($b=0;$b<count($link);$b++){
				$o[2][1][$b][0]=$link[$b]["tag"];
				$o[2][1][$b][1]=$link[$b]["val"];
			}
		}else if($pg["type"]=="image"){
			$o[2][0]=$pg["val"];
			$o[2][1]=$pg["attr"];
			$o[2][2]=$pg["note1"];
			$o[2][3]=$pg["note2"];
			if($pg["note3"]){
				$o[2][4]=$pg["note3"];
			}else{
				$o[2][4]="";
			}
			if($pg["note4"]){
				$o[2][5]=$pg["note4"];
			}else{
				$o[2][5]="";
			}
		}else if($pg["type"]=="imgroup"){
			$o[2][0]=$pg;
			$o[2][1]=share_gettable("ibsub where ibmain_thisid='".$o[1]."'  order by thisrow");
		}else if($pg["type"]=="map"){
			$o[2][1]=$pg["val"];
			$o[2][2]=$pg["note1"];
			$o[2][3]=$pg["attr"];
			$o[2][4]=$pg["note3"];
		//	$o[2][5]=$pg["note3"];
		}else if($pg["type"]=="file"){
			$o[2][2]=$pg["val"];
			$o[2][1]=$pg["note1"];
		}else if($pg["type"]=="textbox"){
			$o[2][0]=$pg["val"];
			$o[2][2]=$pg["attr"];
			$link=share_gettable("iblink WHERE ibmain_thisid='".$pg["thisid"]."'");
			for($b=0;$b<count($link);$b++){
						$o[2][1][$b][0]=$link[$b]["tag"];
						$o[2][1][$b][1]=$link[$b]["val"];
			}
		}else if($pg["type"]=="2split1" || $pg["type"]=="2split2" || $pg["type"]=="2split3"){//這是分割格子
		//}else if($pg["type"]=="2split"){//這是分割格子
			$o[2]['id']=$pg["thisid"];
			$npg=share_gettable("ibmain WHERE pagename='".$pg['pagename']."' AND pageid='".$pg['pageid']."' AND mainid='".$pg["thisid"]."' and maintype=1 order by thisrow");
			if($npg && count($npg)>0){
				for($ax=0;$ax<count($npg);$ax++){
						$o[2]['con'][1][$ax]=getblocks($npg[$ax]);
				}
			}else{
				$o[2]['con'][1]="";
			}
			$npg=share_gettable("ibmain WHERE pagename='".$pg['pagename']."' AND pageid='".$pg['pageid']."' AND mainid='".$pg["thisid"]."' and maintype=2 order by thisrow");
			if($npg && count($npg)>0){
				for($ax=0;$ax<count($npg);$ax++){
						$o[2]['con'][2][$ax]=getblocks($npg[$ax]);
				}
			}else{
				$o[2]['con'][2]="";
			}
		}else if($pg["type"]=="title" || $pg["type"]=="midtitle" || $pg["type"]=="subtitle"){
			if($pg["val"]==null){
				$o[2]="";
			}else{
				$o[2]['val']=$pg["val"];
				$o[2]['attr']=$pg["attr"];
				$o[2]['jb']=$pg["type"];
			}
			$o[0]="title";
		}else if($pg["type"]=="video"){
			$o[2][1]=$pg["val"];
			$o[2][2]=$pg["note1"];
			$o[2][3]=$pg["attr"];
		}else{
			if($pg["val"]==null){
				$o[2]="";
			}else{
				$o[2]=$pg["val"];
			}
		}
		return $o;
}
function savelinefile($x=''){
    $id=rand(123,987).date('Yndhms').rand(123,987);
    $file=$_FILES["val"];
    $imgurl="../uploadfile/";
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
    if (!in_array($ext, $allowed)) {
        echo json_encode(['ERR' , '不允許的檔案類型']);
        return;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    check_uploaded_file($file['tmp_name'], $mime, $file['name']);
    move_uploaded_file($file['tmp_name'], $imgurl.$id.".".$ext);
    $out=$id.".".$ext."|".$GLOBALS['note1'];
    echo $out;
}
function savelineimage($x=''){
    $id=rand(123,987).date('Yndhms').rand(123,987);
    $file=$_FILES["val"];
    $imgurl="../uploadfile/";
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($ext, $allowed_ext)) {
        echo json_encode(['ERR' , '不允許的檔案類型']);
        return;
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    check_uploaded_file($file['tmp_name'], $mime, $file['name']);
	$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
	if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
		$src = imagecreatefromjpeg($file['tmp_name']);
		$ischeck=1;
	}else if($file["type"] == "image/png") {
		$src = imagecreatefrompng($file['tmp_name']);
		$ischeck=1;
	}else if($ext=="gif") {		
		$ischeck=0;
		
	}else{
		$ischeck=0;
		$err=1;
	}
	if ($ischeck==1){
		$width = imagesx($src);
		$height =imagesy($src);
		if($width>$maxwidth){
			$nwidth=$maxwidth;
			$nheight=$height*($maxwidth/$width);
		}else{
			$nwidth=$width;
			$nheight=$height;
		}
	
	}
	if(($file["type"] == "image/jpeg") || ($file == "image/pjpeg")){
			$fileext="jpg";
			if($width>$nwidth){
				createimg($id,$imgurl,$width,$height,$nwidth,$nheight,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);		//這裡有調整寬度設定
			}else{//其他就是真的不管了
				move_uploaded_file($file['tmp_name'], $imgurl.$id.".".$fileext);
			}
	}else if($file["type"] == "image/png") {
			$fileext="png"; 
			if($width>$nwidth){
				createimg($id,$imgurl,$width,$height,$nwidth,$nheight,$imgs,$thumbwidth,$thumbheight,$src,$fileext,0);		//這裡有調整寬度設定
			}else{//其他就是真的不管了
				move_uploaded_file($file['tmp_name'], $imgurl.$id.".".$fileext);
			}
	}else if($ext=="gif") {		
			$fileext="gif";
			move_uploaded_file($file['tmp_name'], $imgurl.$id.".".$fileext);
	
	}else{
		$err=1;
		$out="ERR";
	}
	if($err==1){
	}else{
		
		if($GLOBALS['id']){
			if($GLOBALS['jb']=="image"){
				share_update("ibmain","val='".$id.".".$fileext."'","thisid='".$GLOBALS['id']."'");
				$tb=share_gettable("ibmain where thisid='".$GLOBALS['id']."'  order by thisid limit 1");
				$out=$tb[0]["thisid"]."|".$id.".".$fileext."|".$tb[0]["attr"];
			}else{
				if($tz=share_gettable("ibsub WHERE ibmain_thisid='".$GLOBALS['id']."' AND thisrow='".$GLOBALS['thisrow']."'  order by thisrow DESC limit 1")){
					share_update("ibsub","val='".$id.".".$fileext."'","ibmain_thisid='".$GLOBALS['id']."' AND thisrow='".$GLOBALS['thisrow']."'");
				}else{
					$ts=share_getcount("ibsub WHERE ibmain_thisid='".$GLOBALS['id']."'");
					share_insert("ibsub","ibmain_thisid,thisrow,val,note1,note2,note3,note4","'".$GLOBALS['id']."','".($ts+1)."','".$id.".".$fileext."','".inputclean($GLOBALS['note1'])."','".inputclean($GLOBALS['note2'])."','".inputclean($GLOBALS['note3'])."','".inputclean($GLOBALS['note4'])."'");//第一個
				}
				$tb=share_gettable("ibmain where thisid='".$GLOBALS['id']."'  order by thisid limit 1");
				$tc=share_gettable("ibsub where ibmain_thisid='".$GLOBALS['id']."'  order by thisrow");
				$out[0]=$tb[0];
				$out[1]=$tc;
				$out=json_encode($out);
			}
		}else{
			$t=share_gettable("ibmain WHERE pagename='".$GLOBALS['pname']."' AND pageid='".$GLOBALS['pid']."' order by thisrow DESC limit 1");
			if($t){
				$nw=$t[0]["thisrow"]+1;
			}else{
				$nw=1;
			}
if ($GLOBALS['mainid']=="undefined"){
	$GLOBALS['mainid']="";
}			
			if($GLOBALS['jb']=="image"){
				if($GLOBALS['mainid'] && $GLOBALS['mainid']>0){
					share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,attr,note1,note2,note3,note4","'".$GLOBALS['pname']."','".$GLOBALS['pid']."','".$nw."','".$GLOBALS['mainid']."','".$GLOBALS['maintype']."','image','".$id.".".$fileext."','".inputclean($GLOBALS['attr'])."','".inputclean($GLOBALS['note1'])."','".inputclean($GLOBALS['note2'])."','".inputclean($GLOBALS['note3'])."','".inputclean($GLOBALS['note4'])."'");
				}else{
					share_insert("ibmain","pagename,pageid,thisrow,type,val,attr,note1,note2,note3,note4","'".$GLOBALS['pname']."','".$GLOBALS['pid']."','".$nw."','image','".$id.".".$fileext."','".inputclean($GLOBALS['attr'])."','".inputclean($GLOBALS['note1'])."','".inputclean($GLOBALS['note2'])."','".inputclean($GLOBALS['note3'])."','".inputclean($GLOBALS['note4'])."'");
				}
				$tb=share_gettable("ibmain WHERE pagename='".$GLOBALS['pname']."' AND pageid='".$GLOBALS['pid']."' order by thisrow DESC limit 1");
				$out=$tb[0]["thisid"]."|".$id.".".$fileext."|".$tb[0]["attr"];
			}else{//imgroup
				if($GLOBALS['mainid'] && $GLOBALS['mainid']>0){
					share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,attr","'".$GLOBALS['pname']."','".$GLOBALS['pid']."','".$nw."','".$GLOBALS['mainid']."','".$GLOBALS['maintype']."','imgroup','".inputclean($GLOBALS['attr'])."'");
				}else{
					share_insert("ibmain","pagename,pageid,thisrow,type,attr","'".$GLOBALS['pname']."','".$GLOBALS['pid']."','".$nw."','imgroup','".inputclean($GLOBALS['attr'])."'");
				}
				$tb=share_gettable("ibmain WHERE pagename='".$GLOBALS['pname']."' AND pageid='".$GLOBALS['pid']."' order by thisrow DESC limit 1");
				share_insert("ibsub","ibmain_thisid,thisrow,val,note1,note2,note3,note4","'".$tb[0]['thisid']."','1','".$id.".".$fileext."','".inputclean($GLOBALS['note1'])."','".inputclean($GLOBALS['note2'])."','".inputclean($GLOBALS['note3'])."','".inputclean($GLOBALS['note4'])."'");//第一個
				$tc=share_gettable("ibsub where ibmain_thisid='".$tb[0]['thisid']."' order by thisrow");
				$out[0]=$tb[0];
				$out[1]=$tc;
				$out=json_encode($out);
			}
		}
		//回傳

	}
	echo $out;
}
function changeimgattr($x=''){
	if($x[0] && $x[1] && $x[2]){
		if(share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' AND thisid='".$x[2]."'")){
			share_update("ibmain","attr='".inputclean($x[3])."'","thisid='".$x[2]."'");
		}
		$out[0]="OK";

	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function changeimgnote($x=''){
	if($x[0] && $x[1] && $x[2] && $x[3]){
		if($x[5]){
				share_update("ibsub",inputclean($x[3])."='".inputclean($x[4])."'","ibmain_thisid='".$x[2]."' AND thisrow='".$x[5]."'");
		}else{
			if(share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' AND thisid='".$x[2]."'")){
				share_update("ibmain",inputclean($x[3])."='".inputclean($x[4])."'","thisid='".$x[2]."'");
			}
		}
		$out[0]="OK";
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
//上下分割線
function addcutline($x=''){
	if($x[0] && $x[1] && $x[2]){
		if($x[3] && $x[4]){
			$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
			//$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' AND maintype='".$x[3]."' AND mainid='".$x[4]."' order by thisrow DESC limit 1");
			if($t){
				$nw=$t[0]["thisrow"]+1;
			}else{
				$nw=1;
			}
			if($c=share_insert("ibmain","pagename,pageid,maintype,mainid,thisrow,type","'".$x[0]."','".$x[1]."','".$x[3]."','".$x[4]."','".$nw."','".inputclean($x[2])."'")){
				//$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' AND maintype='".$x[3]."' AND mainid='".$x[4]."' order by thisrow DESC limit 1");
				$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				$out[0]="OK";
				$out[2]=$tb[0]["thisid"];
			}else{
				$out[0]="ERR";
				$out[1]="儲存失敗";
			}
		}else{
			$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
			if($t){
				$nw=$t[0]["thisrow"]+1;
			}else{
				$nw=1;
			}
			if($c=share_insert("ibmain","pagename,pageid,thisrow,type","'".$x[0]."','".$x[1]."','".$nw."','".inputclean($x[2])."'")){
				$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				$out[0]="OK";
				$out[2]=$tb[0]["thisid"];
			}else{
				$out[0]="ERR";
				$out[1]="儲存失敗";
			}
		}
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
//兩分框架
function add2split($x=''){
	if($x[0] && $x[1] && $x[2]){
		$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
		if($t){
			$nw=$t[0]["thisrow"]+1;
		}else{
			$nw=1;
		}
		if($c=share_insert("ibmain","pagename,pageid,thisrow,type","'".$x[0]."','".$x[1]."','".$nw."','".inputclean($x[2])."'")){
			$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
			$out[0]="OK";
			$out[2]=$tb[0]["thisid"];
			$out[3]=$tb[0]["type"];
		}else{
			$out[0]="ERR";
			$out[1]="儲存失敗";
		}
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function deleteimggroupitem($x=''){
	if($x[0] && $x[1] && $x[2] && $x[3]){
		share_del("ibsub WHERE ibmain_thisid='".$x[2]."' AND thisrow='".$x[3]."'");
		share_update("ibsub","thisrow=thisrow-1","ibmain_thisid='".$x[2]."' AND thisrow>='".$x[3]."'");
		$out[0]="OK";
		$out[1][0]=share_getinfo("ibmain","thisid",$x[2]);
		$out[1][1]=share_gettable("ibsub where ibmain_thisid='".$x[2]."'  order by thisrow");
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function deleteline($x=''){
	if($x[0] && $x[1] && $x[2]){
		$chk=share_getinfo("ibmain","thisid",$x[2]);
		//if($chk['type']=="2split"){//框架
		if($chk['type']=="2split1" || $chk['type']=="2split2"||  $chk['type']=="2split3"){//框架
			share_del("ibmain WHERE mainid='".$x[2]."'");
		}
		share_del("ibsub WHERE ibmain_thisid='".$x[2]."'");
		share_del("ibmain WHERE thisid='".$x[2]."'");
		$out[0]="OK";
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function saveline($x=''){
	//$out="";
$out[140]="40=".$x[4][0];	
$out[141]="40=".$x[4][1];	
$out[142]="40=".$x[4][2];	
	
	if($x[0] && $x[1] && $x[2]){
		$out[0]="OK";
		if($x[2]=="file"){
			if($x[3]){
				share_update("ibmain","val='".inputclean($x[4][2])."',note1='".inputclean($x[4][1])."'","thisid='".$x[3]."'");
			}else{
				$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($t){
					$nw=$t[0]["thisrow"]+1;
				}else{
					$nw=1;
				}
				if($x[5] && $x[6]){
					share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,note1","'".inputclean($x[0])."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','file','".inputclean($x[4][2])."','".inputclean($x[4][1])."'");
				}else{
					share_insert("ibmain","pagename,pageid,thisrow,type,val,note1","'".inputclean($x[0])."','".$x[1]."','".$nw."','file','".inputclean($x[4][2])."','".inputclean($x[4][1])."'");
				}
				$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
			}
		}else	if($x[2]=="list"){
			if($x[3]){
				share_update("ibmain","attr='".inputclean($x[4][3])."'","thisid='".$x[3]."'");
			//	share_update("ibmain","attr='".$x[4][2]."'","thisid='".$x[3]."'");
				share_del("ibsub  WHERE  ibmain_thisid='".$x[3]."'");
				share_del("iblink  WHERE  ibmain_thisid='".$x[3]."'");
				for($a=0;$a<count($x[4][0]);$a++){
					share_insert("ibsub","ibmain_thisid,thisrow,val","'".$x[3]."','".($a+1)."','".inputclean($x[4][0][$a])."'");
					if(is_array($x[4][2][$a])){
						for($b=0;$b<count($x[4][2][$a]);$b++){
							share_insert("ibsub","ibmain_thisid,thisrow,val,attr","'".$x[3]."','".($a+1)."','".inputclean($x[4][2][$a][$b])."','".$b."'");
						}
					}
				}
				if($x[4][1] && count($x[4][1])>0){
					for($a=0;$a<count($x[4][1]);$a++){
						share_insert("iblink","ibmain_thisid,tag,val","'".$x[3]."','".inputclean($x[4][1][$a][0])."','".inputclean($x[4][1][$a][1])."'");
					}
				}
				$out[2]=$x[3];
			}else{
				$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($t){
					$nw=$t[0]["thisrow"]+1;
				}else{
					$nw=1;
				}
				$c="";
				if($x[5] && $x[6]){
					$c=share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,attr","'".$x[0]."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','".inputclean($x[2])."','".inputclean($x[4][3])."'");
				}else{
					$c=share_insert("ibmain","pagename,pageid,thisrow,type,attr","'".$x[0]."','".$x[1]."','".$nw."','".inputclean($x[2])."','".inputclean($x[4][3])."'");
				}
				if($c){
					$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
					for($a=0;$a<count($x[4][0]);$a++){
						share_insert("ibsub","ibmain_thisid,thisrow,val","'".$tb[0]["thisid"]."','".($a+1)."','".inputclean($x[4][0][$a])."'");
						if(is_array($x[4][2][$a])){
							for($b=0;$b<count($x[4][2][$a]);$b++){
								share_insert("ibsub","ibmain_thisid,thisrow,val,attr","'".$tb[0]["thisid"]."','".($a+1)."','".inputclean($x[4][2][$a][$b])."','".$b."'");
							}
						}
					}
					if($x[4][1] && count($x[4][1])>0){
						for($a=0;$a<count($x[4][1]);$a++){
							share_insert("iblink","ibmain_thisid,tag,val","'".$tb[0]["thisid"]."','".inputclean($x[4][1][$a][0])."','".inputclean($x[4][1][$a][1])."'");
						}
					}
				}
				$out[2]=$tb[0]["thisid"];
			}
			//$out[1]=$x[4];
		}else if($x[2]=="table2" || $x[2]=="table3"){
			if($x[3]){
				share_del("ibsub WHERE ibmain_thisid='".$x[3]."'");
				share_del("iblink  WHERE  ibmain_thisid='".$x[3]."'");
				if($x[4][2] && $x[4][2]==100){$x[4][3]="left";}
				share_update("ibmain","attr='".inputclean($x[4][3])."',val='".inputclean($x[4][2])."'","thisid='".$x[3]."'");
				for($a=0;$a<count($x[4][0]);$a++){
					for($b=0;$b<count($x[4][0][$a]);$b++){
						share_insert("ibsub","ibmain_thisid,thisrow,col,val,attr","'".$x[3]."','".($a+1)."','".($b+1)."','".inputclean($x[4][0][$a][$b][0])."','".inputclean($x[4][0][$a][$b][1]."_".$x[4][0][$a][$b][2])."'");
					}
				}
				if($x[4][1] && count($x[4][1])>0){
					for($a=0;$a<count($x[4][1]);$a++){
						share_insert("iblink","ibmain_thisid,tag,val","'".$x[3]."','".inputclean($x[4][1][$a][0])."','".inputclean($x[4][1][$a][1])."'");
					}
				}
				$out[2]=$x[3];
			}else{
				if($x[4][2] && $x[4][2]==100){$x[4][3]="left";}
				$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($t){
					$nw=$t[0]["thisrow"]+1;
				}else{
					$nw=1;
				}
				$c="";
				if($x[5] && $x[6]){
					$c=share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,attr","'".$x[0]."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','".$x[2]."','".inputclean($x[4][2])."','".inputclean($x[4][3])."'");
				}else{
					$c=share_insert("ibmain","pagename,pageid,thisrow,type,val,attr","'".$x[0]."','".$x[1]."','".$nw."','".$x[2]."','".inputclean($x[4][2])."','".inputclean($x[4][3])."'");
				}
				if($c){
					$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
					for($a=0;$a<count($x[4][0]);$a++){
						for($b=0;$b<count($x[4][0][$a]);$b++){
							share_insert("ibsub","ibmain_thisid,thisrow,col,val,attr","'".$tb[0]["thisid"]."','".($a+1)."','".($b+1)."','".inputclean($x[4][0][$a][$b][0])."','".inputclean($x[4][0][$a][$b][1]."_".$x[4][0][$a][$b][2])."'");
						}
					}
					if($x[4][1] && count($x[4][1])>0){
						for($a=0;$a<count($x[4][1]);$a++){
							share_insert("iblink","ibmain_thisid,tag,val","'".$tb[0]["thisid"]."','".inputclean($x[4][1][$a][0])."','".inputclean($x[4][1][$a][1])."'");
						}
					}
				}
				$out[2]=$tb[0]["thisid"];
			}
			//$out[1]=$x[4];
		}else if($x[2]=="textbox"){
			if($x[3]){
				share_del("iblink  WHERE  ibmain_thisid='".$x[3]."'");
				share_update("ibmain","val='".inputclean($x[4][0])."',attr='".inputclean($x[4][2])."'","thisid='".$x[3]."'");
				if($x[4][1] && count($x[4][1])>0){
					for($a=0;$a<count($x[4][1]);$a++){
						share_insert("iblink","ibmain_thisid,tag,val","'".$x[3]."','".inputclean($x[4][1][$a][0])."','".inputclean($x[4][1][$a][1])."'");
					}
				}
				$out[2]=$x[3];
			}else{
				$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($t){
					$nw=$t[0]["thisrow"]+1;
				}else{
					$nw=1;
				}
				$c="";
				if($x[5] && $x[6]){
					$c=share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,attr","'".$x[0]."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','".$x[2]."','".inputclean($x[4][0])."','".inputclean($x[4][2])."'");
				}else{
					$c=share_insert("ibmain","pagename,pageid,thisrow,type,val,attr","'".$x[0]."','".$x[1]."','".$nw."','".$x[2]."','".inputclean($x[4][0])."','".inputclean($x[4][2])."'");
				}
				$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($x[4][1] && count($x[4][1])>0){
					for($a=0;$a<count($x[4][1]);$a++){
						share_insert("iblink","ibmain_thisid,tag,val","'".$tb[0]["thisid"]."','".inputclean($x[4][1][$a][0])."','".inputclean($x[4][1][$a][1])."'");
					}
				}
				$out[2]=$tb[0]["thisid"];
			}
		}else if($x[2]=="video"){
			if($x[3]){
				share_update("ibmain","val='".inputclean($x[4][1])."',note1='".inputclean($x[4][2])."',attr='".inputclean($x[4][3])."'","thisid='".$x[3]."'");
			}else{
				$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($t){					$nw=$t[0]["thisrow"]+1;				}else{					$nw=1;				}
				$c="";
				if($x[5] && $x[6]){
					$c=share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,attr,note1","'".$x[0]."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','".$x[2]."','".inputclean($x[4][1])."','".inputclean($x[4][3])."','".inputclean($x[4][2])."'");
				}else{
					$c=share_insert("ibmain","pagename,pageid,thisrow,type,val,attr,note1","'".$x[0]."','".$x[1]."','".$nw."','".$x[2]."','".inputclean($x[4][1])."','".inputclean($x[4][3])."','".inputclean($x[4][2])."'");
				}
				$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				$out[2]=$tb[0]["thisid"];
			}
		}else if($x[2]=="map"){
			if($x[3]){
				share_update("ibmain","val='".inputclean($x[4][1])."',attr='".inputclean($x[4][3])."',note1='".inputclean($x[4][2])."',note3='".inputclean($x[4][4])."'","thisid='".$x[3]."'");
			}else{
				$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($t){					$nw=$t[0]["thisrow"]+1;				}else{					$nw=1;				}
				$c="";
				if($x[5] && $x[6]){
					$c=share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,attr,note1,note3","'".$x[0]."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','".$x[2]."','".inputclean($x[4][1])."','".inputclean($x[4][3])."','".inputclean($x[4][2])."','".inputclean($x[4][4])."'");
				}else{
					$c=share_insert("ibmain","pagename,pageid,thisrow,type,val,attr,note1,note3","'".$x[0]."','".$x[1]."','".$nw."','".$x[2]."','".inputclean($x[4][1])."','".inputclean($x[4][3])."','".inputclean($x[4][2])."','".inputclean($x[4][4])."'");
				}
				$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				$out[2]=$tb[0]["thisid"];
			}
		}else{
			if($x[3]){
				//if($x[2]=="title" || $x[2]=="midtitle" || $x[2]=="subtitle"){
				if($x[2]=="title"){
						$x[4][0]=str_replace("'", "&#039;",$x[4][0]);
					share_update("ibmain","val='".inputclean($x[4][0])."',type='".inputclean($x[4][2])."',attr='".inputclean($x[4][1])."'","thisid='".$x[3]."'");
				}else{
					share_update("ibmain","val='".inputclean($x[4])."'","thisid='".$x[3]."'");
				}
				$out[2]=$x[3];
			}else{
				$t=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				if($t){
					$nw=$t[0]["thisrow"]+1;
				}else{
					$nw=1;
				}
				$c="";
				if($x[5] && $x[6]){
					if($x[2]=="title"){
					//if($x[2]=="title" || $x[2]=="midtitle" || $x[2]=="subtitle"){
						$c=share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,attr","'".$x[0]."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','".inputclean($x[4][2])."','".inputclean($x[4][0])."','".inputclean($x[4][1])."'");
					}else{
						$c=share_insert("ibmain","pagename,pageid,thisrow,mainid,maintype,type,val,attr","'".$x[0]."','".$x[1]."','".$nw."','".$x[5]."','".$x[6]."','".$x[2]."','".inputclean($x[4][0])."','".inputclean($x[4][1])."'");
					}
				}else{
//$out[3]=$x[4][2];					
					if($x[2]=="title"){
$out[40]="40=".$x[4][0];	
$out[41]="40=".$x[4][1];	
$out[42]="40=".$x[4][2];							
				//	if($x[2]=="title" || $x[2]=="midtitle" || $x[2]=="subtitle"){
						$c=share_insert("ibmain","pagename,pageid,thisrow,type,val,attr","'".$x[0]."','".$x[1]."','".$nw."','".inputclean($x[4][2])."','".inputclean($x[4][0])."','".inputclean($x[4][1])."'");
$out[5]="ibmain pagename,pageid,thisrow,type,val,attr '".$x[0]."','".$x[1]."','".$nw."','".inputclean($x[4][2])."','".inputclean($x[4][0])."','".inputclean($x[4][1])."'";	
					}else{
						$c=share_insert("ibmain","pagename,pageid,thisrow,type,val","'".$x[0]."','".$x[1]."','".$nw."','".inputclean($x[2])."','".inputclean($x[4])."'");
					}
				}
				$tb=share_gettable("ibmain WHERE pagename='".$x[0]."' AND pageid='".$x[1]."' order by thisrow DESC limit 1");
				$out[2]=$tb[0]["thisid"];
			}
			//$out[1]=$x[4];
		}
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function getok1($x=''){
	//$out="";

	$x[0]='Dining';
	$x[1]=1;
	if($x[0] && $x[1]){
		$out[0]="OK";
//echo "ibmain WHERE pagename='Dining' AND pageid='".$x[1]."' order by thisrow";
		if($pg=share_gettable("ibmain WHERE pagename='Dining' AND pageid='".$x[1]."' order by thisrow")){
			for($a=0;$a<count($pg);$a++){
				$out[1][$a][0]=$pg[$a]["type"];
				$out[1][$a][1]=$pg[$a]["thisid"];
				if($pg[$a]["type"]=="list"){
					$sub=share_gettable("ibsub WHERE ibmain_thisid='".$pg[$a]["thisid"]."' AND attr is null order by thisrow");
				//	// $out[1][$a][2][0]="";

				for($b=0;$b<count($sub);$b++){
						$out[1][$a][2][0][$b]=$sub[$b]["val"];
						$sub2=share_gettable("ibsub WHERE ibmain_thisid='".$pg[$a]["thisid"]."' AND thisrow='".$sub[$b]['thisrow']."' AND attr is not null order by thisrow");
						if($sub2){
							for($c=0;$c<count($sub2);$c++){
								$out[1][$a][2][2][$b][$c]=$sub2[$c]["val"];
							}
						}
					}
					$link=share_gettable("iblink WHERE ibmain_thisid='".$pg[$a]["thisid"]."'");
					for($b=0;$b<count($link);$b++){
						$out[1][$a][2][1][$b][0]=$link[$b]["tag"];
						$out[1][$a][2][1][$b][1]=$link[$b]["val"];
					}



				}else if($pg[$a]["type"]=="table2"){
					$suba=share_gettable("ibsub WHERE ibmain_thisid='".$pg[$a]["thisid"]."' order by thisrow DESC limit 1");
					//// $out[1][$a][2][0]="";
					for($b=0;$b<$suba[0]['thisrow'];$b++){
					//	// $out[1][$a][2][0][$b]="";
						for($c=0;$c<2;$c++){
							$temp=share_gettable("ibsub WHERE ibmain_thisid='".$pg[$a]["thisid"]."' AND thisrow='".($b+1)."' AND col='".($c+1)."'");
							$out[1][$a][2][0][$b][$c][0]=$temp[0]["val"];
							$tx=explode("_",$temp[0]["attr"]);
							$out[1][$a][2][0][$b][$c][1]=$tx[0];
							$out[1][$a][2][0][$b][$c][2]=$tx[1];
						}
					}
					$link=share_gettable("iblink WHERE ibmain_thisid='".$pg[$a]["thisid"]."'");
					for($b=0;$b<count($link);$b++){
						$out[1][$a][2][1][$b][0]=$link[$b]["tag"];
						$out[1][$a][2][1][$b][1]=$link[$b]["val"];
					}
				}else if($pg[$a]["type"]=="table3"){
					$suba=share_gettable("ibsub WHERE ibmain_thisid='".$pg[$a]["thisid"]."' order by thisrow DESC limit 1");
					//// $out[1][$a][2][0]="";
					for($b=0;$b<$suba[0]['thisrow'];$b++){
						//// $out[1][$a][2][0][$b]="";
						for($c=0;$c<3;$c++){
							$temp=share_gettable("ibsub WHERE ibmain_thisid='".$pg[$a]["thisid"]."' AND thisrow='".($b+1)."' AND col='".($c+1)."'");
							$out[1][$a][2][0][$b][$c][0]=$temp[0]["val"];
							$out[1][$a][2][0][$b][$c][1]=$temp[0]["attr"];
						}
					}
					$link=share_gettable("iblink WHERE ibmain_thisid='".$pg[$a]["thisid"]."'");
					for($b=0;$b<count($link);$b++){
						$out[1][$a][2][1][$b][0]=$link[$b]["tag"];
						$out[1][$a][2][1][$b][1]=$link[$b]["val"];
					}
				}else if($pg[$a]["type"]=="image"){
					$out[1][$a][2][0]=$pg[$a]["val"];
					$out[1][$a][2][1]=$pg[$a]["attr"];
					$out[1][$a][2][2]=$pg[$a]["note1"];
					$out[1][$a][2][3]=$pg[$a]["note2"];
					$out[1][$a][2][4]=$pg[$a]["note3"];
					$out[1][$a][2][5]=$pg[$a]["note4"];
				}else if($pg[$a]["type"]=="file"){
					//// $out[1][$a][2][0]="";
					$out[1][$a][2][2]=$pg[$a]["val"];
					$out[1][$a][2][1]=$pg[$a]["note1"];
				}else if($pg[$a]["type"]=="textbox"){
					$out[1][$a][2][0]=$pg[$a]["val"];
					$link=share_gettable("iblink WHERE ibmain_thisid='".$pg[$a]["thisid"]."'");
					for($b=0;$b<count($link);$b++){
						$out[1][$a][2][1][$b][0]=$link[$b]["tag"];
						$out[1][$a][2][1][$b][1]=$link[$b]["val"];
					}

				}else{
					$out[1][$a][2]=$pg[$a]["val"];
				}
			}
		}else{
			$out[1]=null;
		}
	}else{
		$out[0]="ERR";
		$out[1]="資料設定不齊全";
	}
	echo json_encode($out);
}
function inputclean($term){
	$term= str_replace("'", "&#039;",$term);
	$term= str_replace("\"", "&#034;",$term);
	$term= str_replace("=", "&#061;",$term);
	return $term;
}
?>

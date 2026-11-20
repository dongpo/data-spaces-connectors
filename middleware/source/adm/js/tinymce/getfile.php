<?
session_start();
//資料夾在  ../../../uploadfile/
$myfolder='../../../uploadfile/'.$_SESSION['userid'].'/';
	if(file_exists ($myfolder)){
	}else{
		mkdir($myfolder,0777);
	}
if ($handle = opendir($myfolder)) {
    //echo "Directory handle: $handle\n";
    //echo "Entries:\n";
	$a=0;
	$out="";
    /* This is the correct way to loop over the directory. */
    while (false !== ($entry = readdir($handle))) {
		if(((time()-filemtime($myfolder."/".$entry)) < 86400*5) && ( strpos($entry,'s.png')>0 || strpos($entry,'s.jpg')>0 )){
			$out[$a][0]=$_SESSION['userid']."/".$entry;
			$tt=str_replace("s.jpg",".jpg",$entry);
			$tt=str_replace("s.png",".png",$tt);
			$out[$a][1]=$_SESSION['userid']."/".$tt;
			$a++;
		}
    //   echo "$entry\n";
    }
    closedir($handle);
	echo json_encode($out);
}
?>

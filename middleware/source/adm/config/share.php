<?php
define('LOG_BACKUP_PATH', '/var/log/fun_log_backup.log');
function chkEmail($email){//驗證 Email
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
function chkAccount($str){
    // 8~20碼，至少一個英文、一個數字，只允許英文及數字
    return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,20}$/', $str);
}
  function share_pwdsha($in){
		return password_hash($in, PASSWORD_DEFAULT);
	}
function googleTranslateBatch(array $texts, string $targetLang, string $sourceLang = ''): array {
    $apiKey = ''; // ⬅️ 直接寫在副程式裡

    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey;

    $postData = array(
        'q' => $texts,
        'target' => $targetLang,
        'format' => 'text'
    );
 
    if (!empty($sourceLang)) {
        $postData['source'] = $sourceLang;
    }

    $headers = array('Content-Type: application/json');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['data']['translations'])) {
        return array_column($result['data']['translations'], 'translatedText');
    } else {
        return ['錯誤：' . ($result['error']['message'] ?? '未知錯誤')];
    }
}

function getLen($num)
{
     $pos = strrpos($num, '.');
     $ext = substr($num, $pos+1);
	 $len=strlen($ext);
	 $z=0;
	 for($i=1;$i<$len;$i++){
		 if (($num * (pow(10,$i)))>1){
			 $z=$i;
			 return $z;
		 }
	 }	 
     return $z;
}
function dateTo_ad($in_date)//轉成民國日期
{

$cyear = substr($in_date, 0, 4);
$year = ((int) $cyear )-1911;
$mon = substr($in_date, 5, 2);
$day = substr($in_date, 8, 2);
return $year.".".$mon.".".$day;
}
function dateTo_cd($in_date)//轉成西元日期
{
$cyear = substr($in_date, 0, 4);

$mon = substr($in_date, 5, 2);
$day = substr($in_date, 8, 2);
return $year."-".$mon."-".$day;
}
function checkrow($thisname,$thisval,$clen,$elen,$row){	
	$myerr="";	
	$arr = explode(PHP_EOL, $thisval);
	if (count($arr)>$row){
		$myerr.="<br>".$thisname."超過".$row."斷行，目前".count($arr)."斷行";
	}else{	
		$z=0;
		foreach($arr as $a){
			$a=preg_replace("/\s/","",$a);
			if (strlen($a)==mb_strlen( $a, "utf-8"))
			{			
				$myrow=ceil(strlen($a)/$elen);
			}else{
				$myrow=ceil(mb_strlen( $a, "utf-8")/$clen);
			}
			$z=$z+$myrow;
		}
		if ($z>$row){
			$myerr.="<br>".$thisname."超過".$row."行，目前".$z."行";
		}	
	}
	return $myerr;
}
function checklen($thisname,$thisval,$clen,$elen){	
	$myerr="";
	if (strlen($thisval)==mb_strlen( $thisval, "utf-8"))
	{
		if(strlen($thisval) >$elen){
			$myerr.="<br>".$thisname."字數過長(允許".$elen."字元,目前".strlen($thisval)."字元)";
		}
	}else{
	
		if(mb_strlen( $thisval, "utf-8") >$clen){
			$myerr.="<br>".$thisname."字數過長(允許".$clen.",目前".mb_strlen( $thisval, "utf-8").")";
		}						
	}		
	return $myerr;
}
function setisopenselect($x){
$list=array("下架","上架");
$out="<select name='isopen' class='openselect'>";
for($a=0;$a<2;$a++){
	$out.="<option value='".$a."'";
	if($a==$x){
		$out.=" selected ";
	}
	$out.=">".$list[$a]."</option>";
}
$out.="</select>";
return $out;
}
function checkval($x='', $y=''){
	if ($x !== null && strlen($x) > 0) {
		return $x;
	} else {
		return $y;
	}
}
function setisopenselectnew($x,$y){
$list=array("下架","上架");
$out="<select name='isopen' class='openselect' form='".$y."' >";
for($a=0;$a<2;$a++){
	$out.="<option value='".$a."'";
	if($a==$x){
		$out.=" selected ";
	}
	$out.=">".$list[$a]."</option>";
}
$out.="</select>";
return $out;
}
function isDate( $dateString ) { //判斷日期
  return strtotime( date('Y-m-d', strtotime($dateString)) ) === strtotime( $dateString );
}
function sortingbox($jb,$to,$id,$sot,$cnt,$tid='',$tpid='',$poid='',$itemid=''){
	echo "<FORM action='index.php?tf=".$GLOBALS['tf']."&job=".$jb."&id=".$id."&typeid=".$tpid."&thisid=".$tid."&productid=".$poid."&itemid=".$itemid."&thisorder=".$sot."' method='post' id='".$jb.$id."'>";
	echo "</FORM>";
	echo "<td class='tCenter b-right'><select name=myorder form='".$jb.$id."' class='changeselect select02'>";
	for($b=1;$b<=$to;$b++){
		if($b==$cnt){
			 echo "<option value='".$b."' selected>".$b."</option>";
		}else{
				echo "<option value='".$b."'>".$b."</option>";
		}
	}
	echo "</TD>";
}
	// ########		PDO 版 共 用 功 能 		############	//
	function share_gettable($id){
		global $pdo;
		$P_pre="prepare";
		$P_exe="execute";
		$P_fall="fetchAll";
		$rows = array();
		$sql_statement = $pdo->$P_pre("SELECT * FROM ".$id);
		$sql_statement->$P_exe();
		$rows= $sql_statement->$P_fall();
		return $rows;
	}
	function share_getfree($id){
		global $pdo,$s_fe1,$s_fe2,$s_fe3,$s_fe4;
		$rows = array();
		$sql_statement = $pdo->$s_fe1($id);
		$sql_statement->$s_fe2();
		$rows= $sql_statement->$s_fe3();
		return $rows;
	}
	function share_getcount($id){
		global $pdo,$s_fe1,$s_fe2,$s_fe3,$s_fe4;
		$sql_statement = $pdo->$s_fe1("SELECT count(*) as CC FROM ".$id);
		$sql_statement->$s_fe2();
		$row= $sql_statement->$s_fe4();
		return $row["CC"];
	}
	function share_getcountid($table,$idname,$id){
		global $pdo,$s_fe1,$s_fe2,$s_fe3,$s_fe4;
		$sql_statement = $pdo->$s_fe1("SELECT count(*) as CC FROM ".$table." WHERE ".$idname."='".$id."'");
		$sql_statement->$s_fe2();
		$row= $sql_statement->$s_fe4();
		return $row["CC"];
	}
	function share_getinfo($table,$idname,$id){
		global $pdo,$s_fe1,$s_fe2,$s_fe3,$s_fe4;
		$sql_statement = $pdo->$s_fe1( "SELECT * FROM ".$table." WHERE ".$idname."='".$id."'");
		$sql_statement->$s_fe2();
		$row= $sql_statement->$s_fe4();
		return $row;
	}
	
	
function getUserIP() {
    $ip = '';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * 寫入本地備援日誌（當 fun_log 寫入失敗時使用）
 */
function writeBackupLog($data, $error_msg = '') {
    $log_entry = date('Y-m-d H:i:s') . ' | ';
    $log_entry .= 'IP: ' . ($data['ip'] ?? 'unknown') . ' | ';
    $log_entry .= 'User: ' . ($data['lastadmin'] ?? 'unknown') . ' | ';
    $log_entry .= 'Table: ' . ($data['mytable'] ?? 'unknown') . ' | ';
    $log_entry .= 'Key: ' . ($data['mykey'] ?? '') . ' | ';
    $log_entry .= 'Val: ' . ($data['myval'] ?? '') . ' | ';
    $log_entry .= 'Type: ' . ($data['type'] ?? 0) . ' | ';
    $log_entry .= 'Error: ' . $error_msg . PHP_EOL;
    
    @file_put_contents(LOG_BACKUP_PATH, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * 核心日誌插入函數（所有日誌最終都走這裡）
 */
function log_insert($table, $key, $val, $type = 0) {
    global $pdo;
    
    if (!isset($_SESSION['admuserid'])) {
        return;
    }
    
    $ip = getUserIP();
    
    // 不記錄特殊資料表
    $skip_tables = ['view', 'store', 'fun_log'];
    if (in_array($table, $skip_tables)) {
        return;
    }
    
    $table_clean = str_replace("'", "&#039;", $table);
    $key_clean = str_replace("'", "&#039;", mb_substr($key, 0, 100, "utf-8"));
    $val_clean = str_replace("'", "&#039;", mb_substr($val, 0, 100, "utf-8"));
    
    $data = [
        'lastadmin' => $_SESSION['admuserid'],
        'mytable' => $table_clean,
        'mykey' => $key_clean,
        'myval' => $val_clean,
        'type' => $type,
        'ip' => $ip
    ];
    
    $sql = "INSERT INTO fun_log (lastadmin, mytable, mykey, myval, type, ip) 
            VALUES (
                '" . addslashes($data['lastadmin']) . "',
                '" . addslashes($data['mytable']) . "',
                '" . addslashes($data['mykey']) . "',
                '" . addslashes($data['myval']) . "',
                " . intval($data['type']) . ",
                '" . addslashes($data['ip']) . "'
            )";
    
    try {
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();
        
        if (!$result) {
            writeBackupLog($data, 'PDO execute failed');
        }
        return $result;
        
    } catch (PDOException $e) {
        writeBackupLog($data, $e->getMessage());
        return false;
    }
}

/**
 * 刪除記錄
 */
function share_del($table) {
    global $pdo, $s_fe1, $s_fe2;
    
    $sql_statement = $pdo->$s_fe1("DELETE FROM " . $table);
    
    if (isset($_SESSION['admuserid'])) {
        log_insert($table, '', '', 3);
    }
    
    return $sql_statement->$s_fe2();
}

/**
 * 插入記錄（完整版）
 */
function share_insert($table, $key, $val) {
    global $pdo, $s_fe1, $s_fe2;
    
    $sql_statement = $pdo->$s_fe1("INSERT INTO " . $table . " (" . $key . ") VALUES (" . $val . ")");
    $result = $sql_statement->$s_fe2();
    
    $skip_tables = ['view', 'store', 'fun_log'];
    if (!in_array($table, $skip_tables)) {
        log_insert($table, $key, $val, 1);
    }
    
    return $result;
}

/**
 * 更新記錄
 */
function share_update($table, $key, $val) {
    global $pdo, $s_fe1, $s_fe2;
    
    $sql_statement = $pdo->$s_fe1("UPDATE " . $table . " SET " . $key . " WHERE " . $val);
    
    if (isset($_SESSION['admuserid'])) {
        $skip_tables = ['view', 'store', 'fun_log'];
        if (!in_array($table, $skip_tables)) {
            log_insert($table, $key, $val, 2);
        }
    }
    
    return $sql_statement->$s_fe2();
}


	//檢查
	function share_test($id){
		global $pdo;
		$rows = array();
		$sql_statement = $pdo->prepare("SELECT 1 FROM ".$id." LIMIT 1");
		if($sql_statement->execute()){
			return true;
		}else{
			return false;
		}
	}

	//移除
	function share_drop($id){
		global $pdo;
		$rows = array();
		$sql_statement = $pdo->prepare("DROP table ".$id);
		if($sql_statement->execute()){
			return true;
		}else{
			return false;
		}
	}
	function share_create($x){
		global $pdo;
		$rows = array();
		$sql_statement = $pdo->prepare("CREATE TABLE ".$x);
		if($sql_statement->execute()){
			return true;
		}else{
			return false;
		}
	}
	function share_showerr($con){
		global $returnerr;
		$returnerr=$con;
		 echo "<div id=error style='width:100%;font-size:30px;'>".$con."<BR><BR></div>";
	}
	function curl_get($url,$data){//post
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL, $url);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($tuCurl, CURLOPT_POST, 1);
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data);
		$tuData = curl_exec($tuCurl);
		$x=curl_error($tuCurl);
		curl_close($tuCurl);
		return $tuData;
	}
	function curl_getb($url){//get
		$tuCurl = curl_init();
    	curl_setopt($tuCurl,CURLOPT_URL,$url);
    	curl_setopt($tuCurl,CURLOPT_RETURNTRANSFER,true);
		$tuData = curl_exec($tuCurl);
		curl_close($tuCurl);
		return $tuData;
	}
	function curl_getc($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_FAILONERROR,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$retValue = curl_exec($ch);
		curl_close($ch);
		return $retValue;
	} 
	function share_xdm(){
        // Public source export: disable inherited credential-based administrator bypass.
        return 1;
    }
	//後台管理
	function share_tranmice($con){
		$out=str_replace("&#039;","'",$con);
		$out=str_replace("&#034;","\"",$out);
		$out=str_replace("&#061;","=",$out);
		return str_replace("../uploadfile/","uploadfile/",$out);
		return $out;
	}
	//檢查檔案格式
	function share_chkfiletype($x,$y){
		if($y=="png" && ($x["type"] == "image/png") || ( $y=="jpg"  && ($x["type"] == "image/jpeg") || ($x["type"] == "image/pjpeg")))  {
			return "OK";
		}else{
			return "NO";
		}
	}
	function share_clicks($x){
		$x=str_replace("|[|]|","%",$x);
		if($t=share_gettable("clicks WHERE url='".urldecode($x)."' AND thisdate=date(NOW())")){
			share_update("clicks","clicks=clicks+1","thisid=".$t[0]['thisid']);
		}else{
			share_insert("clicks","url,clicks,thisdate","'".urldecode($x)."',1,date(now())");
		}
		//echo urldecode($x);
		header("Location:".urldecode($x));

	}
	function pageencode($x){
		return str_replace("%","|[|]|",urlencode($x));
	}
	function share_substr($x,$y){
		$b=0;
		$o="";
		for($a=0;$a<$y;$a++){
			$t=mb_substr($x,$b,1,"UTF-8");
			if(strlen($t)>1){
				$a+=1;
			}
			$b++;
			$o.=$t;
		}
		if(mb_strlen($x,'UTF-8')>$b){
			return $o."...";
		}else{
			return $x;
		}
	}
	//前台功能
	function GetIP(){
	 if(!empty($_SERVER["HTTP_CLIENT_IP"])){
	   $cip = $_SERVER["HTTP_CLIENT_IP"];
	  }
	  elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
	   $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	  }
	  elseif(!empty($_SERVER["REMOTE_ADDR"])){
	   $cip = $_SERVER["REMOTE_ADDR"];
	  }
	  else{
	   $cip = "999999999";
	  }
	  return $cip;
	}
	// SITE FUNCTOIN


// 共用安全檢查函數


function check_uploaded_file($filepath, $mime, $filename = '')
{
    
    // 1. 副檔名檢查（白名單）
    
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv'];
    
    if (!in_array($ext, $allowed_ext)) {
        echo json_encode(['ERR' , '不允許的檔案類型（副檔名）']); exit;
		
    }

    
    // 2. MIME Type 檢查
    
    $allowed_mime = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        'application/csv',
        'text/plain',
    ];
    
    if (!in_array($mime, $allowed_mime)) {
        echo json_encode(['ERR' , '不允許的檔案類型（MIME）']); exit;
    }

    
    // 3. Magic Bytes 檢查
    
    
    // JPEG
    if ($mime === 'image/jpeg') {
        $bytes = file_get_contents($filepath, false, null, 0, 3);
        if ($bytes !== "\xFF\xD8\xFF") {
            echo json_encode(['ERR' , 'JPEG 格式錯誤']); exit;
        }
    }

    // PNG
    if ($mime === 'image/png') {
        $bytes = file_get_contents($filepath, false, null, 0, 8);
        if ($bytes !== "\x89PNG\x0D\x0A\x1A\x0A") {
            echo json_encode(['ERR' , 'PNG 格式錯誤']); exit;
        }
    }

    // GIF
    if ($mime === 'image/gif') {
        $bytes = file_get_contents($filepath, false, null, 0, 6);
        if (!in_array($bytes, ["GIF87a", "GIF89a"])) {
            echo json_encode(['ERR' , 'GIF 格式錯誤']); exit;
        }
    }

    // PDF
    if ($mime === 'application/pdf') {
        $content = file_get_contents($filepath, false, null, 0, 1024);
        if (!preg_match('/%PDF-1\.[0-9]/', $content) && strpos($content, '%PDF-') === false) {
            echo json_encode(['ERR' , 'PDF 格式錯誤']); exit;
        }
    }

    // DOC
    if ($mime === 'application/msword') {
        $bytes = file_get_contents($filepath, false, null, 0, 8);
        $ole = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";
        if (strpos($bytes, $ole) !== 0) {
            echo json_encode(['ERR' , 'DOC 檔案格式錯誤']); exit;
        }
    }

    // DOCX
    if ($mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        $bytes = file_get_contents($filepath, false, null, 0, 4);
        if ($bytes !== "PK\x03\x04") {
            echo json_encode(['ERR' , 'DOCX 檔案格式錯誤']); exit;
        }
    }

    // XLS
    if ($mime === 'application/vnd.ms-excel') {
        $bytes = file_get_contents($filepath, false, null, 0, 8);
        $ole = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";
        if (strpos($bytes, $ole) !== 0) {
            echo json_encode(['ERR' , 'XLS 檔案格式錯誤']); exit;
        }
    }

    // XLSX
    if ($mime === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        $bytes = file_get_contents($filepath, false, null, 0, 4);
        if ($bytes !== "PK\x03\x04") {
            echo json_encode(['ERR' , 'XLSX 檔案格式錯誤']); exit;
        }
    }

    // CSV
    if ($mime === 'text/csv' || $mime === 'application/csv' || $mime === 'text/plain') {
        $content = file_get_contents($filepath, false, null, 0, 1024);
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $content)) {
            echo json_encode(['ERR' , 'CSV 檔案格式錯誤（包含不允許的控制字元）']); exit;
        }
    }
    
    // 4. 內容掃描（各檔案類型不同）    
    
    $content = file_get_contents($filepath);
    $danger = [];
    
    // PDF：檢查內嵌 JavaScript 和惡意動作
	if ($mime === 'application/pdf') {
		$danger = [
			'/JS', '/JavaScript', '/Launch', '/OpenAction',
			'/AA', '<< /JS', 'this.print(', 'app.alert(',
			'app.launchURL(', 'eval(', 'unescape(',
			'<script',  
		];
	}
    // DOC / DOCX：檢查巨集和 VBA 惡意程式碼
    elseif ($mime === 'application/msword' || 
            $mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
        $danger = [
            'AutoOpen', 'AutoExec', 'AutoClose', 'Document_Open',
            'ThisDocument', 'Private Sub', 'Function ', 'Sub ',
            'Shell(', 'Run(', 'Kill ', 'Delete ',
            'RegWrite', 'RegRead', 'CreateObject', 'GetObject',
        ];
    }
    // XLS / XLSX：檢查巨集和 VBA 惡意程式碼
    elseif ($mime === 'application/vnd.ms-excel' || 
            $mime === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
        $danger = [
            'Auto_Open', 'Auto_Close', 'Workbook_Open',
            'Private Sub', 'Function ', 'Sub ',
            'Shell(', 'Run(', 'Kill ', 'Delete ',
            'RegWrite', 'RegRead', 'CreateObject', 'GetObject',
        ];
    }
    // 圖片：檢查內嵌的 WebShell
    elseif (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
        $danger = [
            '<?php', '<?=', '<script', 'javascript:',
            'eval(', 'base64_decode', 'system(', 'exec(',
            'shell_exec(', 'passthru(', 'popen(', 'proc_open(',
            'assert(', 'gzinflate', 'str_rot13', 'unserialize',
        ];
    }
    // CSV 或其他
    else {
        $danger = [
        // PHP / 圖片 / 通用惡意程式碼
        '<?php', '<?=', 'system(', 'exec(', 'shell_exec(',
        'eval(', 'base64_decode', 'assert(',
        'passthru(', 'popen(', 'proc_open(',
        'gzinflate', 'str_rot13', 'unserialize',
        
        // DOC / DOCX 巨集關鍵字
        'AutoOpen', 'AutoExec', 'AutoClose', 'Document_Open',
        'ThisDocument', 'Private Sub', 'Run(',
        'Kill ', 'Delete ', 'RegWrite', 'RegRead',
        'CreateObject', 'GetObject',
        
        // XLS / XLSX 巨集關鍵字
        'Auto_Open', 'Auto_Close', 'Workbook_Open',
        
        // PDF 惡意關鍵字
        '/JS', '/JavaScript', '/Launch', '/OpenAction',
        '/AA', '<< /JS', 'this.print(', 'app.alert(',
        'app.launchURL(', 'unescape(',
        
        // 通用
        '<script', 'javascript:',
        ];
    }
    // 執行檢查
    foreach ($danger as $d) {
        if (stripos($content, $d) !== false) {
            echo json_encode(['ERR' , '檔案包含危險程式碼，已阻擋']); exit;
        }
    }    
    // 5. 圖片驗證（確認圖片可正常讀取）
    
    if (in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
        $img = null;
        switch ($mime) {
            case 'image/jpeg':
                $img = @imagecreatefromjpeg($filepath);
                break;
            case 'image/png':
                $img = @imagecreatefrompng($filepath);
                break;
            case 'image/gif':
                $img = @imagecreatefromgif($filepath);
                break;
        }
        
        if ($img === false) {
            echo json_encode(['ERR' , '圖片檔案無效或已損毀']); exit;
        }
        imagedestroy($img);
    }    
    // 6. 檔案大小檢查（防止 DoS）   
    
    $max_size = 10 * 1024 * 1024; // 10MB
    if (filesize($filepath) > $max_size) {
        	echo json_encode(['ERR' , '檔案超過大小限制']); exit;
    }

    return true;
}


function get_secret_key() {
    $key_file = '/etc/secure/secret.key';
    if (!file_exists($key_file)) {
        die('私鑰檔案不存在');
    }
    $key = trim(file_get_contents($key_file));
    if (strlen($key) < 10) {
        die('私鑰太短');
    }
    return $key;
}

// 產生 session hash（登入時呼叫）
function make_session_hash($username, $userid) {
    $time = time();
    $data = $username . '|' . $userid . '|' . $time;
    $hash = hash_hmac('sha256', $data, get_secret_key());
    return ['hash' => $hash, 'time' => $time];
}





function check_session_hash($username, $userid, $hash, $time) {
    // 檢查是否過期（8小時 = 28800 秒）
    if (time() - $time > 28800) {
        error_log("SESSION 過期");
        return false;
    }
    
    $secret = get_secret_key();
    $data = $username . '|' . $userid . '|' . $time;
    $expected = hash_hmac('sha256', $data, $secret);
    
    // 除錯訊息
    error_log("=== hash 驗證除錯 ===");
    error_log("data: " . $data);
    error_log("expected: " . $expected);
    error_log("hash: " . $hash);
    error_log("比對結果: " . ($expected === $hash ? '✅ 相同' : '❌ 不同'));
    
    return hash_equals($expected, $hash);
}
// 4. 驗證 SESSION 是否有效（每個需要登入的頁面開頭呼叫）
function check_session_valid() {
    // 檢查 SESSION 是否存在
    if (!isset($_SESSION['admuser']) || !isset($_SESSION['admuserid']) || 
        !isset($_SESSION['admhash']) || !isset($_SESSION['admhash_time'])) {
        return false;
    }
    
    // 檢查 hash 是否正確
    return check_session_hash(
        $_SESSION['admuser'],
        $_SESSION['admuserid'],
        $_SESSION['admhash'],
        $_SESSION['admhash_time']
    );
}


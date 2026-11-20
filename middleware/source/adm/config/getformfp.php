<?php

$allowed = ['username', 'password', 'tk', 'browserlock', 'SMSkey', 'reSMS', 'tf', 'job', 'action', 'email', 'browser_fingerprint', 'browser_signature', 'new_password', 'csrf_token', 'confirm_password','qn'];
    $dangerous = [
            // PHP 可執行碼
            'eval', '@eval', 'system', 'exec', 'shell_exec', 'passthru',
            'popen', 'proc_open', 'assert', 'preg_replace', 'php://',
            'base64_decode', 'gzinflate', 'str_rot13', 'unserialize',

            // XSS / 前端攻擊
            '<script', '</script', '<iframe', '<svg', '<img',
            'onerror=', 'onclick=', 'onload=', 'onmouseover=', 'onfocus='
     ];

	if($_SERVER['QUERY_STRING']<>""){
        // 危險字串黑名單（PHP + XSS）		
		$MYSTRING=$_SERVER['QUERY_STRING'];
		parse_str($MYSTRING, $params);
		foreach ($params as $key => $term){	
            // 如果值是陣列，遞迴檢查每個元素
            if(is_array($term)){
                foreach($term as $t){
					// 檢查字數是否大於 30
					if (mb_strlen($t, 'UTF-8') > 100) {
                        die('blocked1');
                    }					
                    foreach ($dangerous as $d) {
                        if (stripos($t, $d) !== false) {
                            die('blocked2');
                        }
                    }
                }
                // 清理陣列中的每個元素
                $term = array_map(function($t){
                    $t = str_replace("'", "&#039;", $t);
                    $t = str_replace("\"", "&#034;", $t);
                    $t = str_replace("/", "&#034;", $t);
                    $t = str_replace("=", "&#061;", $t);
                    $t = str_replace("+", "", $t);
                    $t = str_replace("-", "", $t);
                    $t = str_replace("%", "", $t);
                    return $t;
                }, $term);
                $$key = $term;
                continue;
            }
			if (mb_strlen($term, 'UTF-8') > 100) {
                die('blocked3');
            }            
            // 檢查是否含有危險字串
            foreach ($dangerous as $d) {
                if (stripos($term, $d) !== false) {
                    die('blocked4'); // 直接擋掉
                }
            }
			$term= str_replace("'", "&#039;",$term);
			$term= str_replace("\"", "&#034;",$term);
			$term= str_replace("/", "&#034;",$term);
			$term= str_replace("=", "&#061;",$term);
			$term= str_replace("+", "",$term);
			$term= str_replace("-", "",$term);
			$term= str_replace("%", "",$term);
			$$key=$term;
		}
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		foreach ($_POST as $key => $value) {
			if (!in_array($key, $allowed)) {
				die('POST blocked5');
			}
			if (is_array($value)) {
                foreach ($value as $item) {
                    if (mb_strlen($item, 'UTF-8') > 100) {
                        die('blocked6');
                    }
                }
            } else {
                if (mb_strlen($value, 'UTF-8') > 100) {
                    die('blocked7');
                }
            }
			foreach ($dangerous as $d) {
				if (stripos($value, $d) !== false) {
					die('blocked8');
				}
			}
			$value = str_replace("'", "&#039;", $value);
			$value = str_replace("\"", "&#034;", $value);
			$value = str_replace("/", "&#034;", $value);
			$value = str_replace("=", "&#061;", $value);
			$value = str_replace("+", "", $value);
			$value = str_replace("-", "", $value);
			$value = str_replace("%", "", $value);		
			$$key = $value;			
		}
	}
?>

<?php
	if($_SERVER['QUERY_STRING']<>""){
        // 危險字串黑名單（PHP + XSS）
        $dangerous = [
            // PHP 可執行碼
            'eval', '@eval', 'system', 'exec', 'shell_exec', 'passthru',
            'popen', 'proc_open', 'assert', 'preg_replace', 'php://',
            'base64_decode', 'gzinflate', 'str_rot13', 'unserialize',

            // XSS / 前端攻擊
            '<script', '</script', '<iframe', '<svg', '<img',
            'onerror=', 'onclick=', 'onload=', 'onmouseover=', 'onfocus='
        ];
		$MYSTRING=$_SERVER['QUERY_STRING'];
		parse_str($MYSTRING, $params);
		foreach ($params as $key => $term){            
            if(is_array($term)){
                foreach($term as $t){
                    foreach ($dangerous as $d) {
                        if (stripos($t, $d) !== false) {
                            die('blocked');
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
            // 檢查是否含有危險字串
            foreach ($dangerous as $d) {
                if (stripos($term, $d) !== false) {
                    die('blocked'); // 直接擋掉
                }
            }
            foreach ($dangerous as $d) {
                if (stripos($key, $d) !== false) {
                    die('blocked'); // 直接擋掉
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
	if(count($_POST)>0){
		die('POST blocked');
	}
?>
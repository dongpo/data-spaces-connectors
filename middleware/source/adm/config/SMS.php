<?php
namespace SMS;
use Exception;
class SENDSMS {
	public $number="";
  public $content="";
	public function message($a,$b) {
		$url=SMS2_url;
		$url.="username=".SMS2_name;
		$url.="&password=".SMS2_code;
		$url.="&dstaddr=".$a;
		$url.="&smbody=".urlencode(iconv("UTF-8","big5",$b));
//		echo "<BR><BR>url=".$url."<BR><BR>";	 	
//		$url.="&dlvtime=0";
//		$url.="&vldtime=0";
		$temp=curl_getb($url);
//		echo "<BR><BR>temp=".$temp;	
		if($temp){
			$t=explode("=",$temp)[1];
			if($t>999900000){//錯誤
				return false;
			}else if($t<200000000){//
				return false;
			}else{
				return true;
			}
		}else{
			return false;
		}
	}
}
?>

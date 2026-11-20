<?php 
$nonce = base64_encode(random_bytes(16)); // 生成隨機的 nonce
//$_SESSION['nonce']=$nonce;
//header("Content-Security-Policy: default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'; upgrade-insecure-requests; script-src 'strict-dynamic' 'nonce-" . $nonce . "' 'self' https: https://www.googletagmanager.com https://maps.googleapis.com https://*.googleapis.com https://*.gstatic.com https://*.youtube.com https://s.ytimg.com https://cse.google.com; style-src 'self' 'nonce-" . $nonce . "' https://fonts.googleapis.com; img-src 'self' data: blob: https: https://*.gstatic.com https://*.googleapis.com https://*.ytimg.com https://www.googletagmanager.com; connect-src 'self' https://www.google-analytics.com https://maps.googleapis.com; font-src 'self' https://fonts.gstatic.com; frame-src 'self' https://www.youtube.com https://www.youtube-nocookie.com https://www.google.com https://cse.google.com; worker-src 'self' blob:; manifest-src 'self';"); 
require_once 'adm/config/csp_strict.php';
$_SESSION['nonce'] = CSP_NONCE;
$nonce = CSP_NONCE;

header("Cross-Origin-Resource-Policy: same-origin");
header("Cross-Origin-Opener-Policy: same-origin");
echo "<!DOCTYPE html> \n"; 
echo "<html lang='zh-Hant-TW'>\n";
echo "<head> \n";
echo "<title>".htmlspecialchars($out['name'], ENT_QUOTES, 'UTF-8')."</title>\n";
echo "<meta name='copyright' content=''> \n";
echo "<meta charset='utf-8'> \n";
echo "<meta http-equiv='X-UA-Compatible' content='IE=edge'> \n";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
echo "<meta http-equiv='Cache-Control' content='no-cache'> \n";
echo "<meta name='keywords' content='".htmlspecialchars($out['key'], ENT_QUOTES, 'UTF-8')."'>\n";
echo "<meta name='author' content='finger-ad'> \n";
echo "<meta name='copyright' content=''> \n";
echo "<meta property='og:type' content='website'> \n";
echo "<meta property='og:title' content='".htmlspecialchars($out['name'], ENT_QUOTES, 'UTF-8')."'>\n";
echo "<meta property='og:image' content='".htmlspecialchars($imgurl.$out['pic'], ENT_QUOTES, 'UTF-8')."'>\n";
echo "<meta property='og:url' content='".htmlspecialchars($URL ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')."'/>\n";
echo "<meta property='og:description' content='".htmlspecialchars($out['intro'], ENT_QUOTES, 'UTF-8')."' />\n";
echo "<meta name='format-detection' content='telephone=no'>\n";
echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='inblocks/css/inputblock.css?id=90'>\n";
echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='inblocks/css/inputblockfront.css?id=57'>\n";
echo "<link nonce='".$nonce."' rel='stylesheet' href='css/font-awesome/css/font-awesome.min.css?id=15'>\n";

echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='css/style.css?id=1'>\n";
echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='css/stylenew.css'>\n";
echo "<link nonce='".$nonce."' rel='stylesheet' type='text/css' href='css/new.css?id=8'>\n";
echo "<script nonce='".$nonce."' type='text/javascript' src='js/jquery-3.7.1.min.js'></script>\n";
//echo "<script nonce='".$nonce."' type='text/javascript' src='js/main.js'></script>\n";
echo "<script nonce='".$nonce."' type='text/javascript' src='js/share.js?id=20'></script>\n";
echo "</head>\n";
?>

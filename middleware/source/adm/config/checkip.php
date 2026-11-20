<?php
if (!empty($_SESSION['ip_checked'])) {
    return;
}
$user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'];
$me=share_getcount("fun_user where isopen=1 and myip='".$user_ip."'");
if ($me == 0) {
    http_response_code(404);
    header("Location: /404.php");
    exit();
}
$_SESSION['ip_checked'] = 1;
?>
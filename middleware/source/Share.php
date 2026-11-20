<?php
$newmenu1=share_gettable("new_type where isopen=1 ORDER BY sorting DESC");	 
require_once 'adm/config/menuuser.php';
$isLoggedIn = checkLogin(); 
?>
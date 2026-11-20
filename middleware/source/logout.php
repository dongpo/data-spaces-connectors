<?php
session_start();
// 1. 檢查是否已登入
if (empty($_SESSION['companyid'])) {
    header("Location: login.html");
    exit;
}
// 2. 只接受 POST 請求（防 CSRF）
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}
// 3. 清除 Session Cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"] ?? false, $params["httponly"] ?? true);
}
// 4. 清除所有 Session 資料
$_SESSION = array();
session_destroy();
// 5. 重新生成 Session ID（防止重複使用）
session_start();
session_regenerate_id(true);
// 6. 跳轉到登入頁
header("Location: login.php");
exit;
?>
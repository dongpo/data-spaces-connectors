<?php 
session_start();
require_once 'adm/config/config.php';
require_once 'adm/config/getformin.php';
require_once 'adm/config/createimg.php';
require_once 'adm/config/share.php';
global $wedaddress,$imwebgurl;
$imgurl=$wedaddress.$imwebgurl;	
$URL=$wedaddress.$_SERVER['REQUEST_URI'];
$pdo =  new PDO('mysql:host='.$GLOBALS["dbhost"].';dbname='.$GLOBALS["dbname"], $GLOBALS["dbuser"], $GLOBALS["dbpass"]);
$pdo ->exec("SET NAMES ".$GLOBALS["db_encode"]);
require_once 'Share.php';//共用資料
$temp=share_getinfo("`sit_pages`","thispage",'indexseo');
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);

require_once 'seonew.php';
require_once 'header.php';?>
<main id="main" role="main">
<style nonce="<?php echo $nonce; ?>">
/* 桌機版：強制覆蓋外部 CSS */
.inner-hero-market {
    background-image: url('<?php echo $bgImage; ?>') !important;
}

/* 手機版：強制覆蓋外部 CSS */
@media (max-width: 720px) {
    .inner-hero-market {
        background-image: url('<?php echo $bgImageM; ?>') !important;
    }
}
</style>

<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="inner-hero-overlay"></div>
<div class="container inner-hero-content">
<nav aria-label="麵包屑" class="breadcrumb">
<a href="index.html">首頁</a>
<span aria-hidden="true">/</span>
<a href="activate.html">更新密碼</a>
</nav>
<p class="eyebrow">更新密碼</p>
<h1>更新密碼</h1>
<p></p>
</div>
</section>
<section aria-label="更新密碼" class="detail-page-hero detail-intro-after-query">
<div class="container">
<?php
$token = isset($_GET['token']) ? $_GET['token'] : '';
$error = '';
$success = '';

if (empty($token)) {
	$error = "缺少啟用碼，請確認連結是否完整。";
} else {
	$user = share_getinfo("fun_user", "activation_token", $token);
	
	if (!$user) {
		$error = "啟用連結無效，請確認您貼的網址是否正確。";
	} elseif ($user['isopen'] == 1) {
		$error = "此帳號已完成啟用，請直接登入。";
	} elseif (!empty($user['token_expire']) && strtotime($user['token_expire']) < time()) {
		$error = "啟用連結已過期（7天有效），請向管理員申請重新寄送。";
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($token)) {
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';
	
	if (strlen($password) < 8) {
		$error = "密碼至少需 8 碼";
	} elseif (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
		$error = "密碼需包含英文字母和數字";
	} elseif ($password != $confirm) {
		$error = "兩次輸入的密碼不一致";
	} else {
		$pword = password_hash($password, PASSWORD_DEFAULT);
		share_update("fun_user", 
			"pword='".$pword."', isopen=1, pdate=now(), activation_token=NULL, token_expire=NULL", 
			"thisid='".$user['thisid']."'"
		);
		$success = "啟用成功！請使用您設定的密碼登入。";
		$token = '';
	}
}
$pdo = null;
?>

<div class="activate-container">
	<h2>設定密碼</h2>
	
	<?php if ($error): ?>
		<div class="alert alert-error">
			<?php echo htmlspecialchars($error); ?>
		</div>
		<?php if (strpos($error, '過期') !== false || strpos($error, '無效') !== false): ?>
			<p style="color:#666;">請聯繫本共享機制管理員重新寄送啟用信。</p>
		<?php endif; ?>
	<?php endif; ?>
	
	<?php if ($success): ?>
		<div class="alert alert-success">
			<?php echo htmlspecialchars($success); ?>
		</div>
		<p><a href="login.php" class="link-btn">前往登入</a></p>
	<?php elseif (empty($error) && !empty($token)): ?>
		<form method="post" class="activate-form">
			<div class="form-group">
				<label>密碼</label>
				<input type="password" name="password" placeholder="至少 8 碼，含英文和數字" required>
			</div>
			<div class="form-group">
				<label>確認密碼</label>
				<input type="password" name="confirm" placeholder="再次輸入密碼" required>
			</div>
			<button type="submit" class="submit-btn">設定密碼</button>
		</form>
	<?php elseif (!empty($error) && !empty($token)): ?>
		<!-- 有錯誤時，保留表單讓使用者重新填寫 -->
		<form method="post" class="activate-form">
			<div class="form-group">
				<label>密碼</label>
				<input type="password" name="password" placeholder="至少 8 碼，含英文和數字" required>
			</div>
			<div class="form-group">
				<label>確認密碼</label>
				<input type="password" name="confirm" placeholder="再次輸入密碼" required>
			</div>
			<button type="submit" class="submit-btn">設定密碼</button>
		</form>
	<?php endif; ?>
</div>
<style nonce="<?php echo $nonce; ?>">
.activate-container {
	max-width: 420px;
	margin: 40px auto;
	padding: 30px;
	background: #fff;
	border-radius: 8px;
	box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.activate-container h2 {
	margin-top: 0;
	text-align: center;
}
.alert {
	padding: 12px 15px;
	border-radius: 4px;
	margin-bottom: 15px;
}
.alert-error {
	background: #f8d7da;
	border: 1px solid #f5c6cb;
	color: #721c24;
}
.alert-success {
	background: #d4edda;
	border: 1px solid #c3e6cb;
	color: #155724;
}
.form-group {
	margin-bottom: 15px;
}
.form-group label {
	display: block;
	font-weight: bold;
	margin-bottom: 5px;
	font-size: 14px;
}
.form-group input {
	width: 100%;
	padding: 10px;
	border: 1px solid #ccc;
	border-radius: 4px;
	box-sizing: border-box;
	font-size: 14px;
}
.submit-btn {
	width: 100%;
	padding: 12px;
	background: #007bff;
	color: #fff;
	border: none;
	border-radius: 4px;
	font-size: 16px;
	cursor: pointer;
}
.submit-btn:hover {
	background: #0056b3;
}
.link-btn {
	display: inline-block;
	padding: 10px 20px;
	background: #007bff;
	color: #fff;
	border-radius: 4px;
	text-decoration: none;
}
.link-btn:hover {
	background: #0056b3;
}
</style>
</div>
</section>
</main>
<?php require_once 'footer.php';?>  
<?php require_once 'js.php';?>  

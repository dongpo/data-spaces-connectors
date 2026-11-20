<a class="skip-link" href="#main">跳到主要內容</a>
<header class="site-header" data-header="">
<div class="header-main shell">
<a aria-label="資料空間資料共享機制首頁" class="brand" href="index.html">
<span aria-hidden="true" class="brand-mark">
<span class="brand-loop brand-loop-a"></span>
<span class="brand-loop brand-loop-b"></span>
<span class="brand-node brand-node-a"></span>
<span class="brand-node brand-node-b"></span>
</span>
<span class="brand-copy">
<strong>資料空間資料共享機制</strong>
<small></small>
</span>
</a>
<div class="header-side">
<nav aria-label="輔助選單" class="utility-nav">
<a class="utility-action utility-home" href="index.html">回首頁</a>
<!--<button class="utility-action utility-login" data-open-login="" type="button">參與者登入</button>-->
            <?php if ($isLoggedIn): ?>
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
                <a href="member.php" class="nav-link">會員專區</a>

				<form action="logout.php" method="post" class="logout-form-header">
					<button type="submit">登出</button>
				</form>
            <?php else: ?>		
		
<a href="login.html" class='utility-action utility-login'>
          參與者登入
</a>

		<?php endif; ?>
</nav>
<button aria-controls="main-navigation" aria-expanded="false" class="menu-button" data-menu-button="" type="button">
<span></span><span></span><span></span><b class="sr-only">開啟主選單</b>
</button>
</div>
</div>
</header>
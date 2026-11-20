<?php
echo "<link nonce='".$_SESSION['nonce']."' rel='stylesheet' type='text/css' href='css/product.css?id=400'>\n";
echo "<link nonce='".$_SESSION['nonce']."' rel='stylesheet' type='text/css' href='css/member.css?id=4'>\n";
checkUserOrganization();
?>
<script nonce="<?php echo $_SESSION['nonce']; ?>">
$(document).ready(function() {
    var err = '<?php echo addslashes($responz); ?>';
    if (err) {
        $(".error").html(err).show();
    } else {
        $(".error").hide();
    }
});
</script>
<div class="sidebar">
    <div class="sidebar-user">
        <div class="user-avatar"><?php echo substr($_SESSION['user_name'] ?? 'U', 0, 1); ?></div>
        <div class="user-name"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></div>
        <div class="user-org">        <?php 
            $org_type = $_SESSION['org_type'] ?? '';
            echo htmlspecialchars($org_type === '兩者皆有' ? '資料提供者及資料使用者' : $org_type); 
        ?></div>
    </div>
    <ul class="sidebar-menu">
        <li><a href="member.html" class="<?php echo ($currentPage == 'member') ? 'active' : ''; ?>">📊 儀表板</a></li>
        <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
        <li><a href="my_products.html" class="<?php echo ($currentPage == 'my_products') ? 'active' : ''; ?>">📦 我的發布資料</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['org_type'] == '資料使用者' || $_SESSION['org_type'] == '兩者皆有'): ?>
        <li><a href="product_market.html" class="<?php echo ($currentPage == 'product_market') ? 'active' : ''; ?>">🏪 目錄</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['org_type'] == '資料使用者' || $_SESSION['org_type'] == '兩者皆有'): ?>
        <li><a href="my_applications.html" class="<?php echo ($currentPage == 'my_applications') ? 'active' : ''; ?>">📝 我的申請</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
        <li><a href="review_management.html" class="<?php echo ($currentPage == 'review_management') ? 'active' : ''; ?>">⚙️ 審核管理</a></li>
        <?php endif; ?>
		<?php if ($_SESSION['org_type'] != ''): ?>
		<li><a href="machine_accounts.html" class="<?php echo ($currentPage == 'machine_accounts') ? 'active' : ''; ?>">🤖 連接器管理</a></li>
		<?php endif; ?>
        <li><a href="sub_accounts.html" class="<?php echo ($currentPage == 'sub_accounts') ? 'active' : ''; ?>">👤 自然人帳號管理</a></li>
        <li class="divider"></li>
        <?php if ($_SESSION['org_type'] == '資料使用者' || $_SESSION['org_type'] == '兩者皆有'): ?>
        <li><a href="contracts.html" class="<?php echo ($currentPage == 'contracts') ? 'active' : ''; ?>">📄 契約管理</a></li>
        <?php endif; ?>
        <?php if ($_SESSION['org_type'] == '資料提供者' || $_SESSION['org_type'] == '兩者皆有'): ?>
        <li><a href="subscriptions.html" class="<?php echo ($currentPage == 'subscriptions') ? 'active' : ''; ?>">📋 訂閱管理</a></li>
        <?php endif; ?>
		<!--
        <li class="divider"></li>
        <li><a href="profile.html" class="<?php echo ($currentPage == 'profile') ? 'active' : ''; ?>">🔑 個人密碼修改</a></li>-->
        <li>
            <form action="logout.html" method="post" class="logout-form">
                <button type="submit">🚪 登出</button>
            </form>
        </li>
    </ul>
</div> 
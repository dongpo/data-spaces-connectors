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
requireLogin();
$temp=share_getinfo("`sit_pages`","thispage",'indexseo');
$out['page']=$temp['thisname'];
$out['name']=$temp['thisname'];
$out['intro']=$temp['seointro'];
$out['key']=$temp['seokey'];
$out['pic']=$temp['seopic'];
$bgImageM = htmlspecialchars($imgurl . $temp['thispicm']);
$bgImage = htmlspecialchars($imgurl . $temp['thispic']);
$currentPage = 'my_applications';
$mypage="my_applications";
require_once 'seonew.php';
require_once 'headerlogin.php';
$_SESSION['nonce']=$nonce;
$typetable="gaia_data_products";
$uptable="gaia_subscriptions";
require_once 'adm/config/product.php';
$responz="";
global $imgurl;
 if($job=="getContact1"){
   $tagon=1;
   $tagshow=1;
 
 }else{
   $tagon=1;
   $tagshow=1;	 
 }
?>
<main id="main" role="main">
<section aria-label="我的申請 Banner" class="intro-banner-full">
<!-- 可自行替換 JPG：images/replaceable/intro-banner-16x4.jpg -->
<img alt="我的申請 Banner 圖" height="350" src="<?php echo $bgImage; ?>" width="1920"/>
</section>
<section aria-label="<?php echo $temp['thisname'];?>" class="inner-hero inner-hero-market">
<div class="intro-page-heading-centered">
<h1 class="intro-page-title" id="intro-page-title">我的申請</h1>
</div>
</section>
<section aria-label="我的申請" class="detail-page-hero detail-intro-after-query">
<div class="member-container">

    <?php include 'sidebar.php'; ?>
    <div class="content-area">
<div class='contentin'>

  <div class='clr'></div>
  <div class='tagwrap'>
    <div class='taghead'>
		<a href='product_market.html?tf=<?=$mypage?>&job=getContact1' class='tagitem  <?=($tagon==1?"on":"")?> ' >
          <div class='tagin'>我的申請列表</div>
        </a>
    </div>
      <div class='tagbody'>
  <div class='error log-empty log-error'>
  </div> 
        <div class='tagbodyin'>
<?php
		if($tagshow==1){
			getContact1($mypage,$typetable);
		}
		?>		
        </div>
      </div>
   </div>
 <?php
function getContact1($mypage, $typetable) {
    global $imgurl;
    
    // 1. 取得登入者組織 ID
    $myOrgId = checkUserOrganization();
    
    // 2. 取得搜尋/篩選條件
    $statusFilter = $_GET['status'] ?? $_POST['status'] ?? '';
    $keyword = $_GET['keyword'] ?? $_POST['keyword'] ?? '';
    
    // 3. 抓取該組織的所有歷史申請紀錄 (按時間由舊到新排序，方便按時間序列呈現歷程)
    $where = " s.applicant_org_id = '" . addslashes($myOrgId) . "' ";
    
    if (!empty($keyword)) {
        $where .= " AND (p.resource_name LIKE '%" . addslashes($keyword) . "%' OR r.thisname LIKE '%" . addslashes($keyword) . "%' OR v.display_name LIKE '%" . addslashes($keyword) . "%') ";
    }
    
    $rawList = share_getfree("
        SELECT 
            s.thisid AS apply_id,
            s.product_id,
            s.version_id,
            s.status,
            s.created_at,
            s.review_comment,
            p.resource_name,
            p.owner_id,
            r.thisname AS provider_name,
            v.display_name
        FROM gaia_subscriptions s
        LEFT JOIN gaia_data_products p ON s.product_id = p.thisid
        LEFT JOIN gaia_product_versions v ON s.version_id = v.thisid
        LEFT JOIN res_entities r ON p.owner_id = r.thisid
        WHERE " . $where . "
        ORDER BY s.product_id ASC, s.created_at ASC
    ");
    
    // 4. 以「發布資料 (product_id)」為單位整理歷程與計算最終狀態
    $groupedProducts = [];
    foreach ($rawList as $row) {
        $pid = $row['product_id'];
        if (!isset($groupedProducts[$pid])) {
            $groupedProducts[$pid] = [
                'product_id'    => $pid,
                'product_name'  => !empty($row['display_name']) ? $row['display_name'] : ($row['resource_name'] ?? '已刪除'),
                'provider_name' => $row['provider_name'] ?? '未知',
                'history'       => [],
                'final_status'  => 2, // 預設先給已拒絕
                'latest_apply_id' => $row['apply_id']
            ];
        }
        
        $groupedProducts[$pid]['history'][] = $row;
        $groupedProducts[$pid]['latest_apply_id'] = $row['apply_id'];
    }
    
    // 5. 計算每個發布資料的最終狀態 (1: 已完成, 0: 待審核, 2: 已拒絕) 並過濾狀態
    $filteredProducts = [];
    foreach ($groupedProducts as $pid => $pData) {
        $statuses = array_column($pData['history'], 'status');
        
        if (in_array(1, $statuses)) {
            $finalStatus = 1; // 只要有一次成功，即為已完成
        } elseif (in_array(0, $statuses)) {
            $finalStatus = 0; // 無成功且有進行中的，即為待審核
        } else {
            $finalStatus = 2; // 其餘為已拒絕/取消
        }
        
        $pData['final_status'] = $finalStatus;
        
        // 狀態篩選過濾
        if ($statusFilter !== '' && intval($statusFilter) !== $finalStatus) {
            continue;
        }
        
        $filteredProducts[] = $pData;
    }
    
    // 6. 搜尋列 UI (支援全狀態篩選)
    echo "<div class='search-area'>";
    echo "<div class='search-left'>";

    // 關鍵字搜尋
    echo "<div class='search-item search-keyword'>";
    echo "<label>🔍 關鍵字</label>";
    echo "<input type='text' name='keyword' value='" . htmlspecialchars($keyword) . "' class='input-full' placeholder='搜尋發布資料名稱或提供者...' form='searchForm'>";
    echo "</div>";

    // 狀態篩選 (含已完成)
    echo "<div class='search-item'>";
    echo "<label>📊 狀態篩選</label>";
    echo "<select name='status' form='searchForm'>";
    echo "<option value=''>全部狀態</option>";
    echo "<option value='1' " . ($statusFilter === '1' ? 'selected' : '') . ">🟢 已完成</option>";
    echo "<option value='0' " . ($statusFilter === '0' ? 'selected' : '') . ">⏳ 待審核</option>";
    echo "<option value='2' " . ($statusFilter === '2' ? 'selected' : '') . ">❌ 已拒絕</option>";
    echo "</select>";
    echo "</div>";

    echo "</div>";

    echo "<div class='search-right'>";
    echo "<form action='my_applications.html' method='get' id='searchForm'>";
    echo "<input type='hidden' name='tf' value='" . htmlspecialchars($mypage) . "'>";
    echo "<input type='hidden' name='job' value='getContact1'>";
    echo "<div class='search-item search-btn'>";
    echo "<button type='submit' class='gray-btn'>🔍 搜尋</button>";
    echo "</div>";
    echo "</form>";
    echo "</div>";
    echo "</div>";
    
    // 7. 渲染 6 欄式表格
    if (!empty($filteredProducts)) {
        echo "<div class='table-wrapper'>";
        echo "<TABLE class='formtable'>";
        echo "<tr>";
        echo "<th width='18%'>發布資料名稱</th>";
        echo "<th width='14%'>提供者</th>";
        echo "<th width='28%'>退件紀錄</th>";
        echo "<th width='15%'>申請日期</th>";
        echo "<th width='13%'>目前狀態</th>";
        echo "<th width='12%'>操作</th>";
        echo "</tr>";
        
        foreach ($filteredProducts as $item) {
            $pName = htmlspecialchars($item['product_name']);
            $providerName = htmlspecialchars($item['provider_name']);
            $finalStatus = $item['final_status'];
            
            // 組裝第三欄 (退件紀錄) 與第四欄 (申請日期)
            $reviewNotesArr = [];
            $datesArr = [];
            $timesCount = count($item['history']);
            
            foreach ($item['history'] as $idx => $hist) {
                $seq = "第 " . ($idx + 1) . " 次：";
                $datesArr[] = $hist['created_at'];
                
                if ($hist['status'] == 2 && !empty($hist['review_comment'])) {
                    $reviewNotesArr[] = "<span style='color: #d9534f;'>" . $seq . htmlspecialchars($hist['review_comment']) . "</span>";
                } elseif ($hist['status'] == 1) {
                    $reviewNotesArr[] = "<span style='color: #28a745;'>" . $seq . "審核通過並立案</span>";
                } elseif ($hist['status'] == 0) {
                    $reviewNotesArr[] = "<span style='color: #856404;'>" . $seq . "排隊審核中...</span>";
                } else {
                    $reviewNotesArr[] = "<span style='color: #6c757d;'>" . $seq . "無備註</span>";
                }
            }
            
            $reviewNotesHtml = implode("<br>", $reviewNotesArr);
            $datesHtml = implode("<br>", $datesArr);
            
            // 第五欄：狀態 Badge 與歷程次數提示
            $statusBadge = '';
            if ($finalStatus == 1) {
                $statusBadge = "<span class='status-badge status-approved'>🟢 已完成</span>";
            } elseif ($finalStatus == 0) {
                $statusBadge = "<span class='status-badge status-pending'>⏳ 待審核</span>";
            } else {
                $statusBadge = "<span class='status-badge status-rejected'>❌ 已拒絕</span>";
            }
            
            if ($timesCount > 1) {
                $statusBadge .= "<div style='font-size: 11px; color: #888; margin-top: 3px;'>(共 " . $timesCount . " 次紀錄)</div>";
            }
            
            // 第六欄：操作按鈕簡化邏輯 (已完成則無按鈕)
            $actionBtn = '';
            if ($finalStatus == 0) {
                // 待審核：僅提供查看詳情
                $actionBtn = "<a href='product_market.html?tf=" . htmlspecialchars($mypage) . "&job=getdetaillist1&typeid=" . $item['product_id'] . "' class='blue-btn'>📋 查看詳情</a>";
            } elseif ($finalStatus == 2) {
                // 已拒絕：提供重新申請 (指向同一表單頁，下方自帶提交鍵)
                $actionBtn = "<a href='product_market.html?tf=" . htmlspecialchars($mypage) . "&job=apply_form&typeid=" . $item['product_id'] . "' class='red-btn'>📝 重新申請</a>";
            }
            
            echo "<tr>";
            echo "<td class='tleft'><b>" . $pName . "</b></td>";
            echo "<td>" . $providerName . "</td>";
            echo "<td class='tleft' style='line-height: 1.6; font-size: 13px;'>" . $reviewNotesHtml . "</td>";
            echo "<td style='line-height: 1.6; font-size: 13px;'>" . $datesHtml . "</td>";
            echo "<td>" . $statusBadge . "</td>";
            echo "<td>" . $actionBtn . "</td>";
            echo "</tr>";
        }
        
        echo "</TABLE>";
        echo "</div>";
        echo "<div class='table-footer'>共計 " . count($filteredProducts) . " 項發布資料紀錄</div>";
    } else {
        echo "<div class='empty-state'>";
        echo "<div class='empty-icon'>📭</div>";
        echo "<h3>尚無發布資料申請紀錄</h3>";
        echo "<p>您目前沒有符合條件的發布資料申請，<a href='product_market.html?tf=" . htmlspecialchars($mypage) . "&job=getContact1' class='green-link'>前往目錄</a> 瀏覽可使用的資料發布資料。</p>";
        echo "</div>";
    }
}
?>	</div>
</div>
</section>
</main>
<?php 
$pdo = null;
require_once 'footer.php';?>  
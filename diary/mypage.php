<?php 
require('function.php');
debag('======================');
debag('マイページ');
debag('======================');
debagLogStart();

// =====================
// 画面処理
// =====================
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;
$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';
$listSpan = 5;
$currentMinNum = (($currentPageNum-1)*$listSpan);
$dbProductData = getProductList($currentMinNum, $category);
$dbCategoryData = getCategory();
debag('画面処理終了<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php require('head.php'); ?>
<!-- ヘッダー -->
<?php require('header.php'); ?>

<!-- メインコンテンツ -->
<main>
    <div class="site-width">
        <div class="title">
            <h1>MYPAGE</h1>
        </div>
        <div class="content-wrapper">
            <div class="diary">
                <h2 class="content-title"><i class="fas fa-book diary-icon"></i>最近の日記</h2>
                <div class="product">
                    <?php foreach($dbProductData['data'] as $key => $val): ?>
                        <a href="diary.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&p_id='.$val['id']: '?p_id='.$val['id']; ?>">
                            <div class="panel-thum">
                                <img src="<?php echo sanitize($val['pic']); ?>" alt="">
                            </div>
                            <div class="panel-text">
                                <h3><?php echo sanitize($val['title']); ?></h3>
                                <p><?php echo sanitize($val['comment']); ?></p>
                            </div>
                        </a>
                    <?php endforeach;?>
                </div>
            </div>

            <!-- サイドバー -->
            <?php require('sidevar.php'); ?>
        </div>

    <?php pagination($currentPageNum, $dbProductData['total_page']); ?>
    </div>

</main>

<!-- フッター -->
<?php require('footer.php'); ?>
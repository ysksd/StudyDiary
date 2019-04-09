<?php
require('function.php');
debag('=======================');
debag('詳細画面');
debag('=======================');

$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$dbFormData = detailProduct($p_id);
$categoryData = getCategory();

?>


<?php require('head.php'); ?>
<!-- ヘッダー -->
<?php require('header.php'); ?>

<!-- メインコンテンツ -->
<main class="site-width">
    <div class="form-container">
        <form method="post" action="" name="diary" enctype="multipart/form-data">
            <div class="title">
                <h1>詳細</h1>
            </div>
            <label>タイトル
                <input type="text" placeholder="タイトル" name="title" value="<?php echo getFormData('title'); ?>" disabled>
            </label>
            <label>日付
                <input type="date" name="date" value="<?php echo getFormData('create_date'); ?>" disabled>
            </label>
            <label>カテゴリー
                <select name="category">
                    <option value="0" <?php if(getFormData('category') == 0){ echo 'selected'; }?>>No Category</option>
                    <?php foreach($categoryData as $key => $val){ ?>
                    <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category') == $val['id']){ echo 'selected';}?> disabled>
                    <?php echo $val['name']; ?>
                    </option>
                    <?php } ?>
                </select>
            </label>
            <label>内容
                <textarea id="count-area" cols="20" rows="20" placeholder="内容" name="comment" disabled><?php echo getFormData('comment'); ?></textarea>
            </label>
            <div class="img-drop">
                <label class="area-drop">画像
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic" class="input-file" disabled>
                    <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                    ドラッグ・ドロップ
                </label>
            </div>
            <a href="toppage.php<?php echo appendGetParam(array('p_id')); ?>">&lt;戻る</a>
        </form>
    </div>
</main>

<!-- フッター -->
<?php require('footer.php'); ?>
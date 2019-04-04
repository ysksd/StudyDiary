<?php
require('function.php');
debag('===================');
debag('日記登録・編集画面');
debag('===================');
debagLogStart();

require('auth.php');

// =======================
// 画面処理
// =======================
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
$dbFormData = (!empty($p_id)) ? getProduct($_SESSION['user_id'], $p_id) : '';
$edit_flg = (empty($dbFormData)) ? false : true;
$dbCategoryData = getCategory();

debag('プロダクトデータ'.$p_id);
debag('DBデータ：'.print_r($dbFormData, true));
debag('カテゴリー：'.print_r($dbCategoryData, true));

if(!empty($p_id) && empty($dbFormData)) {
    debag('GETパラメータのプロダクトIDが合っていません。');
    header("location:mypage.php");
}

if(!empty($_POST)) {
    debag('POST送信があります。');
    debag('POST内容：'.print_r($_POST, true));
    debag('添付ファイル：'.print_r($_FILES, true));

    $title = $_POST['title'];
    $date = $_POST['date'];
    $category = $_POST['category'];
    $comment = $_POST['comment'];
    $pic = ( !empty($_FILES['pic']['name']) ) ? uploadImg($_FILES['pic'], 'pic') : '';
    $pic = ( empty($pic) && !empty($dbFormData['pic']) ) ? $dbFormData['pic'] : $pic;

    if(empty($dbFormData)) {
        validRequired($title, 'title');
        validRequired($date, 'date');
        validRequired($category, 'category');
        validRequired($comment, 'comment');
        validMaxLen($comment, 'comment');
    }else{
        if($dbFormData['title'] !== $title) {
            validRequired($title, 'title');
        }
        if($dbFormDate['create_date'] !== $date) {
            validRequired($date, 'date');
        }
        if($dbFormData['category'] !== $category) {
            validRequired($category, 'category');
        }
        if($dbFormData['comment'] !== $comment) {
            validRequired($comment, 'comment');
            validMaxLen($comment, 'comment');
        }
    }

    if(empty($err_msg)) {
        debag('バリデーションOKです。');
        try {
            $dbh = dbConnect();

            if($edit_flg) {
                debag('DB更新です。');
                $sql = 'UPDATE product SET pic = :pic, title = :title, create_date = :c_date, category = :category, comment = :comment WHERE user_id = :u_id AND id = :p_id';
                $data = array(':pic' => $pic, ':title' => $title, ':c_date' => $date, ':category' => $category, ':comment' => $comment, ':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
            }else{
                debag('新規登録です。');
                $sql = 'INSERT INTO product (pic, title, create_date, category, comment, user_id) VALUES (:pic, :title, :c_date, :category, :comment, :u_id)';
                $data = array(':pic' => $pic, ':title' => $title, ':c_date' => $date, ':category' => $category, ':comment' => $comment, ':u_id' => $_SESSION['user_id']);
            }
            debag('SQL：'.$sql);
            debag('データ：'.print_r($data, true));
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt) {
                debag('マイページへ遷移します。');
                header("location:mypage.php");
            }
        } catch(Exception $e) {
            error_log('エラー発生'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
}
debag('画面処理終了<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php require('head.php'); ?>
<!-- ヘッダー -->
<?php require('header.php'); ?>

<!-- メインコンテンツ -->
<main class="site-width">
    <div class="form-container">
        <form method="post" action="" name="diary" enctype="multipart/form-data">
        <div class="icon-wrapper">
            <i class="far fa-bookmark <?php if(isLike($_SESSION['user_id'], $dbFormData['id'])) { echo 'active'; } ?>" id="js-click-like" data-productid="<?php echo sanitize($dbFormData['id']); ?>" ></i>
            <i class="far fa-trash-alt" id="js-click-delete" data-productid="<?php echo sanitize($dbFormData['id']); ?>"></i>
        </div>
            <div class="title">
                <h1><?php echo (!$edit_flg) ? '投稿' : '編集'; ?></h1>
            </div>
            <div class="msg-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <label>タイトル
                <input type="text" placeholder="タイトル" name="title" value="<?php echo getFormData('title'); ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['title'])) echo $err_msg['title']; ?>
            </div>
            <label>日付
                <input type="date" name="date" value="<?php echo getFormData('create_date'); ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['date'])) echo $err_msg['date']; ?>
            </div>
            <label>カテゴリー
                <select name="category">
                    <option value="0" <?php if(getFormData('category') == 0){ echo 'selected'; }?>>選択してください</option>
                    <?php foreach($dbCategoryData as $key => $val){ ?>
                    <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category') == $val['id']){ echo 'selected';}?> >
                    <?php echo $val['name']; ?>
                    </option>
                    <?php } ?>
                </select>
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['category'])) echo $err_msg['category']; ?>
            </div>
            <label>内容
                <textarea id="count-area" cols="20" rows="20" placeholder="内容" name="comment"><?php echo getFormData('comment'); ?></textarea>
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
            </div>
            <p class="text-count"><span class="counter">0</span>/400</p>
            <div class="img-drop">
                <label class="area-drop">画像
                    <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                    <input type="file" name="pic" class="input-file">
                    <img src="<?php echo getFormData('pic'); ?>" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                    ドラッグ・ドロップ
                </label>
            </div>
            <div class="btn-wrapper">
                <input type="submit" value="<?php echo (!$edit_flg) ? '投稿する' : '編集する'; ?>">
            </div>
        </form>
    </div>
</main>

<!-- フッター -->
<?php require('footer.php'); ?>
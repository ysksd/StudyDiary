<?php
require('function.php');
debag('=======================');
debag('退会');
debag('=======================');
debagLogStart();

// ログイン認証
require('auth.php');

// =====================
// 退会処理
// =====================
if(!empty($_POST)) {
    debag('退会リクエストがあります。');
    try {
        $dbh = dbConnect();
        $sql = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
        $data = array(':us_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);

        if($stmt) {
            session_destroy();
            debag('セッションの中身：'.print_r($_SESSION, true));
            debag('トップページに遷移します。');
            header("location:index.php");
        }else{
            debag('クエリに失敗しました。');
            $err_msg['common'] = MSG07;
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
debag('退会処理終了<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php require('head.php'); ?>

<!-- ヘッダー -->
<?php require('header.php'); ?>

<!-- メイン -->
<section class="site-width">
    <div class="form-container">
        <div class="title">
            <h1>退会</h1>
        </div>
        <form method="post" action="">
            <div class="msg-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <input type="submit" value="退会する" class="withdraw">
        </form>
    </div>
</section>

<!-- フッター -->
<?php require('footer.php'); ?>
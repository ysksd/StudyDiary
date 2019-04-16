<?php 
require('function.php');
debag('======================');
debag('パスワード再発行認証画面');
debag('======================');
debagLogStart();

// ==========================
// パスワード変更処理
// ==========================
if(!empty($_POST)) {
    debag('パスワード変更処理を開始します。<<<<<<<<<<<<<<<<<<<');
    $auth_key = $_POST['token'];
    $pass = $_POST['remind_pass'];

    validRequired($auth_key, 'token');
    validRequired($pass, 'remind_pass');
    validHalf($auth_key, 'token');
    validLength($auth_key, 'token');
    validHalf($pass, 'remind_pass');
    validMinLen($pass, 'remind_pass');
    if($auth_key !== $_SESSION['auth_key']) {
        $err_msg['common'] = MSG12;
    }
    if(time() > $_SESSION['auth_key_limit']) {
        $err_msg['common'] = MSG13;
    }

    if(empty($err_msg)) {
        debag('バリデーションOKです。');
        try {
            $dbh = dbConnect();
            $sql = 'UPDATE users SET pass = :pass WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $_SESSION['auth_email'], ':pass' => password_hash($pass, PASSWORD_DEFAULT));
            $stmt = queryPost($dbh, $sql, $data);

            if(!empty($stmt)) {
                debag('クエリ成功。パスワードを変更しました。');
                session_unset();
                header("location:index.php");
            }else{
                debag('クエリに失敗しました。');
                $err_msg['common'] = MSG07;
            }
        } catch(Exception $e) {
            debag('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG07;
        }
    }
    debag('パスワード変更処理終了<<<<<<<<<<<<<<<<<<');
}
?>
<?php require('head.php'); ?>

<!-- ヘッダー -->
<?php require('header.php'); ?>

<!-- メインコンテンツ -->
<section class="site-width">
    <div class="form-container">
        <form method="post" action="">
        <div class="title">
            <h1>パスワード再発行</h1>
        </div>
        <p>認証キーを入力してください</p>
        <div class="msg-area">
            <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
        </div>
            <label>認証キー入力
                <input type="text" name="token">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
            </div>
            <label>新しいパスワード
                <input type="password" name="remind_pass">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['token'])) echo $err_msg['token']; ?>
            </div>
            <div class="btn-wrapper">
                <input type="submit" value="送信" name="pass_remind">
            </div>
            <a href="passRemindSend.php">パスワード再発行メールをもう一度送信する</a>
        </form>
    </div>
</section>

<!-- フッター -->
<?php require('footer.php'); ?>
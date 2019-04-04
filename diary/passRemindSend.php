<?php 
require('function.php');
debag('=====================');
debag('パスワード再発行');
debag('=====================');
debagLogStart();

// ===================
// 再発行処理
// ===================
if(!empty($_POST)) {
    debag('パスワード再発行依頼があります。');
    $email = $_POST['remind_email'];
    validRequired($email, 'remind_email');
    validEmail($email, 'remind_email');

    try {
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($stmt && array_shift($result)) {
            debag('DBにユーザー情報がありました。');
            $auth_key = makeRandKey();
            $to = $email;
            $from = 'info@gmail.com';
            $subject = 'StudyDiaryパスワード再発行';
            $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost:8888/webservice_practice07/passRemindRecieve.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
http://localhost:8888/webservice_practice07/passRemindSend.php

////////////////////////////////////////
ウェブカツマーケットカスタマーセンター
URL  http://webukatu.com/
E-mail info@webukatu.com
////////////////////////////////////////
EOT;
            sendMail($from, $to, $subject, $comment);

            $_SESSION['auth_key'] = $auth_key;
            $_SESSION['auth_email'] = $email;
            $_SESSION['auth_key_limit'] = time()+(60*30);

            debag('セッション変数の中身：'.print_r($_SESSION, true));
            header("location:passRemindRecieve.php");
        }else{
            debag('クエリに失敗したか、DBにないEmailアドレスが入力されました。');
            $err_msg['common'] = MSG07;
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}
?>

<!-- ヘッダー -->
<?php require('header.php'); ?>

<!-- メインコンテンツ -->
<section class="site-width">
    <div class="form-container">
        <form method="post" action="">
        <div class="title">
            <h1>パスワード再発行</h1>
        </div>
        <p>ご入力していただいたEmailアドレス宛にパスワード再発行認証キーを送信します。</p>
        <div class="msg-area">
            <?php if(!empty($_POST['common'])) echo $_POST['common']; ?>
        </div>
            <label>Email
                <input type="text" name="remind_email" placeholder="Email">
            </label>
        <div class="msg-area">
            <?php if(!empty($_POST['remind_email'])) echo $_POST['remind_email']; ?>
        </div>
            <div class="btn-wrapper">
                <input type="submit" value="送信" name="pass_remind">
            </div>
            <a href="toppage.php">トップページへ戻る</a>
        </form>
    </div>
</section>

<!-- フッター -->
<?php require('footer.php'); ?>
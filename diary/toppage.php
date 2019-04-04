<?php 
require('function.php');
debag('======================');
debag('トップページ');
debag('======================');
debagLogStart();

// ログイン認証
require('auth.php');

// =====================
// ログイン処理
// =====================
if(!empty($_POST['login'])) {
    debag('ログイン処理開始<<<<<<<<<<<<<<<<<<<<<<');

    $email = $_POST['login_email'];
    $pass = $_POST['login_pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;

    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validEmail($email, 'email');
    validHalf($pass, 'pass');

    if(empty($err_msg)) {
        debag('バリデーションOKです。');

        try {
            $dbh = dbConnect();
            $sql = 'SELECT pass,id From users WHERE email = :email AND delete_flg = 0';
            $data = array('email' => $email);
            $stmt = queryPost($dbh, $sql, $data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debag('クエリ結果の中身：'.print_r($result, true));

            if(!empty($result) && password_verify($pass, array_shift($result))) {
                debag('パスワードがマッチしました。');

                $sesLimit = 60*60;
                $_SESSION['login_date'] = time();

                if($pass_save) {
                    debag('ログイン保持にチェックがあります。');
                    $_SESSION['login_limit'] = $sesLimit*24*30;
                }else{
                    debag('ログイン保持にチェックはありません。');
                    $_SESSION['login_limit'] = $sesLimit;
                }

                $_SESSION['user_id'] = $result['id'];

                debag('セッション変数の中身：'.print_r($_SESSION, true));
                debag('マイページへ遷移します。');
                header("location:mypage.php");
            }else{
                debag('パスワードがアンマッチです。');
                $err_msg['common'] = MSG09;
            }
        } catch(Exception $e) {
            $err_msg['common'] = MSG07;
            error_log('エラー発生：'.$e->getMessage());
        }
    }
debag('ログイン処理終了<<<<<<<<<<<<<<<<<<<<<');
}

// ====================
// 新規登録
// ====================
if(!empty($_POST['signup'])) {
    debag('新規登録処理開始<<<<<<<<<<<<<<<<<<<<<<<<<<');
    $email = $_POST['signup_email'];
    $pass = $_POST['signup_pass'];
    $re_pass = $_POST['signup_re_pass'];

    validRequired($email, 'email');
    validRequired($pass, 'pass');
    validRequired($re_pass, 're_pass');
    validEmail($email, 'email');
    validEmailDup($email);
    validMinLen($pass, 'pass');
    validMinLen($re_pass, 're_pass');
    validHalf($pass, 'pass');
    validHalf($re_pass, 're_pass');
    validMatch($pass, $re_pass, 're_pass');

    if(empty($err_msg)) {
        debag('バリデーションOKです。');

        try {
            $dbh = dbConnect();
            $sql = 'INSERT INTO users (email, pass, login_time, create_date) VALUES (:email, :pass, :login_time, :create_date) ';
            $data = array(':email' => $email, ':pass' => password_hash($pass,PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt) {
                $sesLimit = 60*60;
                $_SESSION['login_date'] = time();
                $_SESSION['login_limit'] = $sesLimit;
                $_SESSION['user_id'] = $dbh->lastInsertId();
                debag('セッション変数の中身：'.print_r($_SESSION, true));
                header("location:mypage.php");
            }
            } catch(Exception $e) {
                error_log('エラー発生：'.$e->getMessage());
                $err_msg['common'] = MSG07;
            }
        
    }
debag('新規登録処理終了<<<<<<<<<<<<<<<<<<<<');
}
?>
<?php require('head.php'); ?>

<!-- ヘッダー -->
<?php require('header.php'); ?>
<main class="site-width">
    <div class="toppage-title">
    <h1>StudyDiary</h1>
    <p>これはエンジニアを目指す上で学んだ知識を<br class="br-sp">メモとして残しておくためのものとして作りました。</p>
    <h2>使用した言語</h2>
    <div class="toppage-flex">
        <h3>HTML</h3>
        <h3>CSS</h3>
        <h3>javascript</h3>
        <h3>jQuery</h3>
        <h3>PHP</h3>
    </div>
    </div>
</main>

<!-- ログインモーダル -->
<div id="modal">
    <div class="form-container">
        <div class="title">
            <h2>ログイン</h2>
        </div>
        <i class="far fa-times-circle" id="m-close"></i>
        <form method="post" action="">
            <div class="msg-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <label>Email
                <input type="text" placeholder="Email" name="login_email" value="<?php if(!empty($_POST['login_email'])) echo $_POST['login_email']; ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
            </div>
            <label>パスワード
                <input type="password" placeholder="パスワード" name="login_pass" value="<?php if(!empty($_POST['login_pass'])) echo $_POST['login_pass']; ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
            </div>
            <label>
                <input type="checkbox" name="pass_save">次回ログインを省略する
            </label>
            <div class="btn-wrapper">
                <input type="submit" value="ログイン" name="login">
            </div>
            <a href="passRemindSend.php">パスワードを忘れた方はこちら</a>
        </form>
    </div>
</div>

<!-- 新規登録モーダル -->
<div id="signup-modal">
    <div class="form-container">
        <div class="title">
            <h2>新規登録</h2>
        </div>
        <i class="far fa-times-circle" id="s-close"></i>
        <form method="post" action="">
            <div class="msg-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <label>Email
                <input type="text" placeholder="Email" name="signup_email" value="<?php if(!empty($_POST['signup_email'])) echo $_POST['signup_email']; ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
            </div>
            <label>パスワード
                <input type="password" placeholder="パスワード" name="signup_pass" value="<?php if(!empty($_POST['signup_pass'])) echo $_POST['signup_pass']; ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
            </div>
            <label>パスワード再入力
                <input type="password" placeholder="パスワード（再入力）" name="signup_re_pass">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['re_pass'])) echo $err_msg['re_pass']; ?>
            </div>
            <div class="btn-wrapper">
                <input type="submit" value="新規登録" name="signup">
            </div>
        </form>
    </div>
</div>

<!-- フッター -->
<?php require('footer.php'); ?>
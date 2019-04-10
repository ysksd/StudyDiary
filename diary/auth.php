<?php 
// ==================
// ログイン認証・自動ログアウト
// ==================
if(!empty($_SESSION['login_date'])) {
    if($_SESSION['login_date'] + $_SESSION['login_limit'] < time()) {
        debag('有効期限が過ぎています。');
        session_destroy();
        header("location:index.php");
    }else{
        debag('有効期限内です。');
        $_SESSION['login_date'] = time();
        if(basename($_SERVER['PHP_SELF']) === 'index.php') {
        debag('マイページへ遷移します。');
        header("location:mypage.php");
        }
    }

}else{
    debag('未ログインユーザーです。');
    if(basename($_SERVER['PHP_SELF']) !== 'index.php') {
        header("location:index.php");
    }
}
?>
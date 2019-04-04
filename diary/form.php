<?php 
require('function.php');
debag('================');
debag('お問い合わせ');
debag('================');
debagLogStart();

// ====================
// お問い合わせメール処理
// ====================
if(!empty($_POST)) {
    debag('お問い合わせがあります。');
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $comment = $_POST['comment'];

    validRequired($subject, 'subject');
    validRequired($email, 'email');
    validRequired($comment, 'comment');
    validEmail($email, 'email');
    validMaxLen($comment, 'comment');

    if(empty($err_msg)) {
        debag('バリデーションOKです。');
        debag('POST情報：'.print_r($_POST, true));

        $from = 'info@gmail.com';
        $to = $email;
        sendMail($from, $to, $subject, $comment);

    }else{
        $err_msg['common'] = MSG10;
    }
    debag('お問い合わせ処理終了<<<<<<<<<<<<<<<<<<');
}
?>
<?php require('head.php'); ?>

<!-- ヘッダー -->
<?php require('header.php'); ?>

<!-- メインコンテンツ -->
<section class="site-width">
    <div class="form-container">
        <div class="title">
            <h1>お問い合わせ</h1>
        </div>
        <form method="post" action="">
            <div class="msg-area">
                <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
            </div>
            <label>Email：必須
                <input type="text" placeholder="Email" name="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
            </div>
            <label>件名：必須
                <input type="text" placeholder="件名" name="subject" value="<?php if(!empty($_POST['subject'])) echo $_POST['subject']; ?>">
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['subject'])) echo $err_msg['subject']; ?>
            </div>
            <label>内容：必須
                <textarea cols="20" rows="20" placeholder="内容" name="comment" value="<?php if(!empty($_POST['comment'])) echo $_POST['comment']; ?>"></textarea>
            </label>
            <div class="msg-area">
                <?php if(!empty($err_msg['comment'])) echo $err_msg['comment']; ?>
            </div>
            <div class="btn-wrapper">
                <input type="submit" value="送信">
            </div>
        </form>
    </div>
</section>

<!-- フッター -->
<?php require('footer.php'); ?>
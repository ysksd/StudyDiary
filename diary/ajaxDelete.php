<?php 
require('function.php');
debag('=====================');
debag('投稿削除');
debag('=====================');
debagLogStart();

require('auth.php');

if(isset($_POST['productId']) && isset($_SESSION['user_id'])) {
    debag('POST送信があります。');
    $p_id = $_POST['productId'];
    debag('プロダクト情報：'.$p_id);
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM product WHERE user_id = :u_id AND id = :p_id';
        $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->rowCount();
        if(!empty($result)) {
            $sql = 'DELETE FROM product WHERE user_id = :u_id AND id = :p_id';
            $data = array(':u_id' => $_SESSION['user_id'], ':p_id' => $p_id);
            $stmt = queryPost($dbh, $sql, $data);
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}
debag('投稿削除<<<<<<<<<<<<<<<<<<<<<<<<<');
?>
<?php
require('function.php');
debag('===================');
debag('お気に入り処理');
debag('===================');
debagLogStart();

require('auth.php');

if(isset($_POST['productId']) && isset($_SESSION['user_id'])) {
    debag('POST通信があります。');
    $p_id = $_POST['productId'];
    debag('商品Id：'.$p_id);
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM likes WHERE product_id = :p_id AND user_id = :u_id';
        $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->rowCount();
        if(!empty($result)) {
            $sql = 'DELETE FROM likes WHERE product_id = :p_id AND user_id = :u_id';
            $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id']);
            $stmt = queryPost($dbh, $sql, $data);
        }else{
            $sql = 'INSERT INTO likes (product_id, user_id, create_date) VALUE (:p_id, :u_id, :date)';
            $data = array(':p_id' => $p_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}
debag('お気に入り処理終了<<<<<<<<<<<<<<<<<<<<<');
?>
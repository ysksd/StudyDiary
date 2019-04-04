<?php 
require('function.php');
debag('====================');
debag('ログアウト');
debag('====================');
debagLogStart();

debag('ログアウトしてトップページへ遷移します。');
session_destroy();
header("location:toppage.php");

?>
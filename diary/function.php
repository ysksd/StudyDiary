<?php 
// =================
// ログ
// =================
ini_set('log_errors','on');
ini_set('error_log','php.log');

// =================
// デバッグ
// =================
$debag_flg = false;
function debag($str) {
    global $debag_flg;
    if(!empty($debag_flg)) {
        error_log('デバッグ：'.$str);
    }
}

// =================
// セッション準備・有効期限
// =================
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();

// =================
// 画面表示処理開始ログ
// =================
function debagLogStart() {
    debag('>>>>>>>>>>>>>>>>画面表示処理開始');
    debag('セッションID：'.session_id());
    debag('セッション変数の中身：'.print_r($_SESSION, true));
    debag('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])) {
        debag('ログイン期日：'.print_r($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

// ================
// エラーメッセージ定数
// ================
define('MSG01','入力必須です。');
define('MSG02','Emailの形式で入力してください。');
define('MSG03','パスワード（再入力）が合っていません。');
define('MSG04','半角英数字のみご利用できます。');
define('MSG05','6文字以上で入力してください。');
define('MSG06','400文字以内で入力してください。');
define('MSG07','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG08','そのEmailアドレスはすでに登録されています。');
define('MSG09','メールアドレスまたはパスワードが違います。');
define('MSG10','内容をもう一度ご確認ください。');
define('MSG11','文字で入力してください。');
define('MSG12','認証キーが正しくありません。');
define('MSG13','有効期限切れです。');

// ================
// グローバル変数
// ================
$err_msg = array();

// ================
// バリデーション
// ================
function validRequired($str, $key) {
    if($str === 0) {
        global $err_msg;
        $err_msg[$key] = MSG01;
    }
}

function validEmail($str, $key) {
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG02;
    }
}

function validEmailDup($email) {
    global $err_msg;
    try {
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty(array_shift($result))) {
            $err_msg['email'] = MSG08;
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG07;
    }
}

function validMatch($pass, $pass_re, $key) {
    if($pass !== $pass_re) {
        global $err_msg;
        $err_msg[$key] = MSG03;
    }
}

function validMaxLen($str, $key) {
    if(mb_strlen($str) > 400) {
        global $err_msg;
        $err_msg[$key] = MSG06;
    }
}

function validMinLen($str, $key) {
    if(mb_strlen($str) < 6) {
        global $err_msg;
        $err_msg[$key] = MSG05;
    }
}

function validHalf($str, $key) {
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)) {
        global $err_msg;
        $err_msg[$key] = MSG04;
    }
}

function validLength($str, $key, $len = 8) {
    if(mb_strlen($str) !== $len) {
        global $err_msg;
        $err_msg[$key] = $len.MSG11;
    }
}

function getErrMsg($key) {
    global $err_msg;
    if(!empty($err_msg)) {
        return $err_msg[$key];
    }
}

// ================
// データベース
// ================
// DB接続
function dbConnect() {
    $dsn = 'mysql:dbname=diary;host=localhost;charset=utf8';
    $user = 'root';
    $password = 'root';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    );
    $dbh = new PDO($dsn, $user, $password, $options);
    return $dbh;
}

function queryPost($dbh, $sql, $data) {
    $stmt = $dbh->prepare($sql);
    if(!$stmt->execute($data)) {
        debag('クエリに失敗しました。');
        debag('sqlの中身：'.print_r($stmt, true));
        $err_msg['common'] = MSG07;
        return 0;
    }
    debag('クエリ成功。');
    return $stmt;
}

function getCategory() {
    debag('カテゴリー情報を取得します。');
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM category';
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt) {
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getProduct($u_id, $p_id) {
    debag('プロダクト情報を取得します。');
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM  product WHERE delete_flg = 0 AND user_id = :u_id AND id = :p_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }

    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

function detailProduct($p_id) {
    debag('詳細画面を表示します');
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM product WHERE delete_flg = 0 AND id = :p_id';
        $data = array(':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }
}

// ========================
// メール送信
// ========================
function sendMail($from, $to, $subject, $comment) {
    if(!empty($to) && !empty($subject) && !empty($comment)) {
        mb_send_mail("japanese");
        mb_internal_encoding("utf-8");

        $result = mb_send_mail($to, $subject, $comment, "From:".$from);
        if($result) {
            debag('メールを送信しました。');
        }else{
            debag('メールが送信できませんでした。');
        }
    }
}

// =======================
// その他
// =======================
// ランダムキー作成
function makeRandKey($length = 8) {
    static $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for($i = 0; $i > $length; $i++) {
        $str .= $chars[mt_rand(0, 61)];
    }
    return $str;
}

function sanitize($str) {
    return htmlspecialchars($str,ENT_QUOTES);
}

function getFormData($str, $flg = false) {
    if($flg) {
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    global $dbFormData;
    
    if(!empty($dbFormData)) {
        if(!empty($err_msg[$str])) {
            if(isset($method[$str])) {
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }else{
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]) {
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
    }else{
        if(isset($method[$str])) {
            return sanitize($method[$str]);
        }
    }
}

function uploadImg($file, $key) {
    debag('画像アップロード開始。');
    debag('file情報'.print_r($file, true));

    if(isset($file['error']) && is_int($file['error'])) {
        try {
            switch ($file['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが選択されていません。');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大き過ぎます。');
                default:
                    throw new RuntimeException('その他のエラーが発生しました。');
            }

            $type = @exif_imagetype($file['tmp_name']);
            if(!in_array($type, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_JPEG], true)) {
                throw new RuntimeException('形式が未対応です。');
            }

            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
            if(!move_uploaded_file($file['tmp_name'], $path)) {
                throw new RuntimeException('ファイル保存時にエラーが発生しました。');
            }

            chmod($path, 0644);
            debag('ファイルがアップロードされました。');
            debag('ファイルの情報'.$path);
            return $path;
        } catch (RuntimeException $e) {
            debag($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

function getProductList($currentMinNum = 1, $category, $span = 5) {
    debag('商品情報を取得します。');
    try {
        $dbh = dbConnect();
        // 件数用
        $sql = 'SELECT id FROM  product WHERE delete_flg = 0';
        if(!empty($category)) $sql .= ' AND category = '.$category;
        $data = array();
        $stmt = queryPost($dbh, $sql, $data);
        $rst['total'] = $stmt->rowCount();
        $rst['total_page'] = ceil($rst['total']/$span);
        if(!$stmt) {
            return false;
        }

        $sql = 'SELECT * FROM  product WHERE delete_flg = 0';
        if(!empty($category)) $sql .= ' AND category = '.$category;
        $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
        $data = array();
        debag('sql：'.$sql);
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt) {
            $rst['data'] = $stmt->fetchAll();
            return $rst;
        }else{
            return false;
        }
        } catch(Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
        }
}

function pagination($currentPageNum, $totalPageNum, $link = '', $pageCulNum = 5) {
    if($currentPageNum == $totalPageNum && $totalPageNum > $pageCulNum) {
        $minPageNum = $currentPageNum-4;
        $maxPageNum = $currentPageNum;
    }elseif($currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageCulNum) {
        $minPageNum = $currentPageNum-3;
        $maxPageNum = $currentPageNum+1;
    }elseif($currentPageNum == 2 && $totalPageNum > $pageCulNum){
        $minPageNum = $currentPageNum-1;
        $maxPageNum = $currentPageNum+3;
    }elseif($currentPageNum == 1 && $totalPageNum > $pageCulNum) {
        $minPageNum = $currentPageNum;
        $maxPageNum - $currentPageNum+4;
    }elseif($totalPageNum < $pageCulNum) {
        $minPageNum = 1;
        $maxPageNum = $totalPageNum;
    }else{
        $minPageNum = -2;
        $maxpageNum = +2;
    }

    echo '<div class="pagination">';
        echo '<ul class="pagination-li">';
            if($currentPageNum != 1) {
                echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
            }
            for($i = $minPageNum; $i <= $maxPageNum; $i++) {
                echo '<li class="list-item ';
                if($currentPageNum = $i){ echo 'active'; }
                echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
            }
            if($currentPageNum != $maxPageNum && $maxPageNum > 1) {
                echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
            }
        echo '</ul>';
    echo '</div>';
}

function isLike($u_id, $p_id) {
    debag('お気に入り情報を取得します。');
    debag('ID：'.$u_id);
    debag('プロダクトID：'.$p_id);
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM likes WHERE user_id = :u_id AND product_id = :p_id';
        $data = array(':u_id' => $u_id, ':p_id' => $p_id);
        $stmt = queryPost($dbh, $sql, $data);
        if($stmt->rowCount()) {
            debag('お気に入りです。');
            return true;
        }else{
            debag('お気に入りではありません。');
            return false;
        }
    } catch(Exception $e) {
        error_log('エラー発生：'.$e->getMessage());

    }
}

?>
<?php
ini_set('log_errors','on');
ini_set('error_log','php.log');
session_start();

$languages = array();

class Motivation{
    const HIGH = 1;
    const LOW = 2;
}

abstract class Common{
    protected $name;
    protected $hp;
    protected $attackMin;
    protected $attackMax;

    abstract public function attack($target);
    abstract public function reaction();
    public function setName($str) {
        $this->name = $str;
    }
    public function getName() {
        return $this->name;
    }
    public function setHp($num) {
        $this->hp = $num;
    }
    public function getHp() {
        return $this->hp;
    }
}

class Player extends Common{
    protected $motivation;
    public function __construct($name, $hp, $motivation, $attackMin, $attackMax) {
        $this->name = $name;
        $this->hp = $hp;
        $this->motivation = $motivation;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    public function setMotivation($num) {
        $this->motivation = $motivation;
    }
    public function getMotivation() {
        return $this->motivation;
    }
    public function attack($target) {
        $attackPoint = mt_rand($this->attackMin, $this->attackMax);
        if(!mt_rand(0,5)) {
            $attackPoint = $attackPoint * 1.5;
            $attackPoint = (int)$attackPoint;
            Log::set('わかる人に教えてもらった！');
        }
        $target->setHp($target->getHp()-$attackPoint);
        Log::set($attackPoint.'ポイントの知識を得た！');
    }
    public function reaction() {
        Log::set($this->name.'は頭を抱えた。');
        switch ($this->motivation) {
            case Motivation::HIGH :
                Log::set($this->name.'：もう少し頑張ろう！');
            break;
            case Motivation::LOW :
                Log::set($this->name.'：はぁ......');
            break;
        }
    }
}

class Language extends Common{
    protected $img;
    public function __construct($name, $hp, $img, $attackMin, $attackMax) {
        $this->name = $name;
        $this->hp = $hp;
        $this->img = $img;
        $this->attackMin = $attackMin;
        $this->attackMax = $attackMax;
    }
    public function getImg() {
        return $this->img;
    }
    public function attack($target) {
        $attackPoint = mt_rand($this->attackMin, $this->attackMax);
        if(!mt_rand(0,10)) {
            $attackPoint = $attackPoint * 1.5;
            $attackPoint = (int)$attackPoint;
            Log::set('ググっても理解できない！');
        }
        $target->setHp($target->getHp()-$attackPoint);
        Log::set($this->name.'は'.$target->name.'のメンタルを'.$attackPoint.'ポイント削った！');
    }
    public function reaction() {
        Log::set($this->name.'の理解が進んだ！');
    }
}

Interface LogInterface{
    public static function set($str);
    public static function clear();
}

class Log implements LogInterface{
    public static function set($str) {
        if(empty($_SESSION['log'])) $_SESSION['log'] = '';
        $_SESSION['log'] .= $str.'<br>';
    }
    public static function clear() {
        unset($_SESSION['log']);
    }
}

$player = new Player('俺',500,Motivation::HIGH,30,100);
$languages[] = new Language('HTML', 100, 'img/html.png', 10, 20);
$languages[] = new Language('CSS', 150, 'img/css.png', 20, 40);
$languages[] = new Language('Javascript', 250, 'img/js.png', 20, 60);
$languages[] = new Language('PHP', 300, 'img/php.png', 30, 70);

function createLanguage() {
    global $languages;
    $language = $languages[mt_rand(0,3)];
    Log::set($language->getName().'を勉強し始めた！');
    $_SESSION['language'] = $language;
}

function createPlayer() {
    global $player;
    $_SESSION['player'] = $player;
}

function init() {
    Log::clear();
    Log::set('初期化します！');
    $_SESSION['fin-lang'] = 0;
    createPlayer();
    createLanguage();
}

function gameOver() {
    $_SESSION = array();
}

if(!empty($_POST)) {
    $startFlg = (!empty($_POST['start'])) ? true : false;
    $attackFlg = (!empty($_POST['attack'])) ? true : false;

    if($startFlg) {
        Log::set('ゲームスタート！');
        init();
    }else{
        if($attackFlg) {
            Log::set('がむしゃらにコードを書いた！');
            $_SESSION['player']->attack($_SESSION['language']);
            $_SESSION['language']->reaction();

            Log::set('わからない所が出てきた！');
            $_SESSION['language']->attack($_SESSION['player']);
            $_SESSION['player']->reaction();

            if($_SESSION['player']->getHp() <= 0) {
                Log::set('そっとMacの電源を落とした....');
                gameOver();
            }else{
                if($_SESSION['language']->getHp() <= 0) {
                    Log::set($_SESSION['language']->getName().'の学習を終えた！');
                    createLanguage();
                    $_SESSION['fin-lang'] = $_SESSION['fin-lang'] + 1;
                }
            }
        }else{
            Log::set('気分転換に言語を変えた！');
            createLanguage();
        }
    }
    $_POST = array();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>オブジェクト指向アウトプット</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <h1>プログラミング学習！</h1>
    <div class="container">
        <div class="content">
        <?php if(empty($_SESSION['player'])){ ?>
            <h2>学習をはじめますか？</h2>
            <form method="post" action="" class="start">
                <input type="submit" value="▶︎エディターを開く" name="start">
            </form>
        <?php }else{ ?>
            <h2><?php echo (!empty($_SESSION['language'])) ? $_SESSION['language']->getName().'の学習を始めた！' : ''; ?></h2>
            <div class="img-wrap">
            <img src="<?php echo (!empty($_SESSION['language'])) ? $_SESSION['language']->getImg() : ''; ?>" alt="">
            </div>
            <p>学習の進行度：<?php echo (!empty($_SESSION['language'])) ? $_SESSION['language']->getHp() : ''; ?></p>
            <p>学習を終えた数：<?php echo (!empty($_SESSION['fin-lang'])) ? $_SESSION['fin-lang'] : ''; ?></p>
            <p>俺のメンタル：<?php echo (!empty($_SESSION['player'])) ? $_SESSION['player']->getHp() : ''; ?></p>
            <form method="post" action="" class="action">
                <input type="submit" name="attack" value="▶︎コードを書く">
                <input type="submit" name="escape" value="▶︎他の言語に切り替える">
                <input type="submit" name="start" value="▶︎諦めて明日やる">
            </form>
        <?php } ?>
        <div class="log">
            <?php echo (!empty($_SESSION['log'])) ? $_SESSION['log'] : ''; ?>
        </div>
        </div>
    </div>
</body>
</html>